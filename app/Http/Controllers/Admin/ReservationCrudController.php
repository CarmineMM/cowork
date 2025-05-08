<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Reservation\Status as ReservationStatus;
use App\Enums\Room\Status as RoomStatus;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Policies\ReservationPolicy;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

/**
 * Class ReservationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReservationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Reservation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reservation');
        CRUD::setEntityNameStrings('Reservación', 'Reservaciones');

        // Agregar botón de exportación
        $this->crud->addButton('top', 'export_excel', 'view', 'vendor.backpack.crud.buttons.export_excel', 'end');

        // Verificar permisos para cada operación
        $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);
        $reservationPolicy = new ReservationPolicy;
        $entity = $this->crud->getCurrentEntry();

        if ($reservationPolicy->viewAny(backpack_user())) {
            $this->crud->allowAccess('list');
        }

        if ($reservationPolicy->view(backpack_user(), $entity)) {
            $this->crud->allowAccess('show');
        }

        if ($reservationPolicy->create(backpack_user())) {
            $this->crud->allowAccess('create');
        }

        if ($reservationPolicy->update(backpack_user(), $entity)) {
            $this->crud->allowAccess('update');
        }

        if ($reservationPolicy->delete(backpack_user(), $entity)) {
            $this->crud->allowAccess('delete');
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('title')->label('Titulo de la reservación');

        $this->crud->addColumn([
            'label'     => 'Sala', // Table column heading
            'name'      => 'room_id', // the column that contains the ID of that connected entity;
            'entity'    => 'room', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);

        // Filtros son del backpack PRO
        // $this->crud->addFilter([
        //     'type'  => 'select',
        //     'name'  => 'room_id',
        //     'label' => 'Filtrar por sala'
        // ], function () {
        //     return \App\Models\Room::pluck('name', 'id')->toArray();
        // }, function ($value) {
        //     $this->crud->addClause('where', 'room_id', $value);
        // });

        if (!backpack_user()->can('admin.reservations.index')) {
            CRUD::column('status')
                ->label('Estado de la reservación')
                ->value(fn($entry) => $entry->status->getLabel())
                ->wrapper([
                    'element' => 'span',
                    'class' => function ($crud, $column, $entry, $related_key) {
                        return match ($entry->status) {
                            ReservationStatus::Approved => 'badge badge-success',
                            ReservationStatus::Pending => 'badge badge-info',
                            ReservationStatus::Rejected => 'badge badge-error',
                        };
                    },
                ]);
        }

        CRUD::column('start_reservation')->type('datetime')->label('Inicio de la reservación');
        CRUD::column('end_reservation')->type('datetime')->label('Finalización');

        // Mostrar la persona que reserva en caso de ser un administrador
        if (backpack_user()->can('admin.reservations.index')) {
            $this->crud->addColumn([
                'label'     => 'Usuario que reserva', // Table column heading
                'name'      => 'user_id', // the column that contains the ID of that connected entity;
                'entity'    => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
            ]);

            // Ademas, para ser mas rápido, se podrá cambiar el estatus de las reservaciones desde la propia lista
            // TODO: No se si me estoy equivocando, según la documentación esto debería ser un select, pero no lo es...
            $this->crud->addColumn([
                'label'     => 'Estado de la reserva',
                'type'      => 'select_from_array',
                'name'      => 'status',
                'options'   => ReservationStatus::getLabels(),
                'value'     => fn($entry) => $entry->status->value
            ]);
        }
    }

    /**
     * Exportar reservaciones a Excel
     */
    public function exportExcel()
    {
        $reservations = Reservation::with(['user', 'room'])->get();

        $headers = [
            'Cliente',
            'Sala',
            'Hora de reserva',
            'Hora de finalización',
            'Motivo',
            'Estado',
            'Duración (horas)'
        ];

        $rows = $reservations->map(function ($reservation) {
            return [
                $reservation->user->name,
                $reservation->room->name,
                $reservation->start_reservation->format('Y-m-d H:i'),
                $reservation->end_reservation->format('Y-m-d H:i'),
                $reservation->title,
                $reservation->status->getLabel(),
                $reservation->start_reservation->diffInHours($reservation->end_reservation)
            ];
        });

        $filename = 'reservaciones-' . now()->format('Y-m-d') . '.csv';

        $handle = fopen('php://temp', 'r+');

        // Agregar BOM para UTF-8
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir headers
        fputcsv($handle, $headers);

        // Escribir filas
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReservationRequest::class);
        $attributesShared = [];

        if ($this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->status !== ReservationStatus::Pending) {
            $attributesShared['disabled'] = 'disabled';
        }
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('title')->label('Motivo de la reservación');
        CRUD::field('start_reservation')
            ->type('datetime')
            ->label('Fecha de la reservación')
            ->hint('Todas las reservaciones tendrán una (1) hora de duración')
            ->attributes($attributesShared);

        CRUD::field([
            'label'     => 'Sala de la reservación',
            'type'      => 'select',
            'name'      => 'room_id', // the db column for the foreign key

            'model'     => Room::class, // related model
            'attribute' => 'name',

            'options'   => (function ($query) {
                return $query->where('status', RoomStatus::Available)->get();
            }),

            'attributes' => $attributesShared,
        ]);

        // Si el usuario tiene permisos administrativos, el puede elegir a nombre de quien hacer la reservación
        // Y editar el estatus
        if (backpack_user()->can('admin.reservations.create')) {
            CRUD::field([
                'name'  => 'status',
                'label' => 'Estado de la reserva',
                'type'  => 'enum',
                'options' => ReservationStatus::getLabels(),
            ]);

            CRUD::field([  // Select
                'label'     => 'Usuario que reserva',
                'type'      => 'select',
                'name'      => 'user_id', // the db column for the foreign key

                'model'     => User::class, // related model
                'attribute' => 'name',

                'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }),
            ]);
        }

        /**
         * Modificar el saving de backpack según la permisología del usuario actual
         */
        Reservation::creating(function ($entry) {
            if (!backpack_user()->can('admin.reservations.create')) {
                $entry->user_id = backpack_user()->getKey();
                $entry->status = ReservationStatus::Pending;
            }

            // Si el end_reservation no viene, establecerlo haciendo uso del star_reservation
            // Ayudara a que si en un futuro se desea hacer ese field nuevo, no haya que modificar esta lógica.
            if (!$entry->end_reservation) {
                $entry->end_reservation = Carbon::parse($entry->start_reservation)->addHour();
            }
        });
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
