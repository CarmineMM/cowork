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

        // Verificar permisos para cada operación
        $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);
        $reservationPolicy = new ReservationPolicy;
        $entity = $this->crud->getCurrentEntry();

        if ($reservationPolicy->viewAny(backpack_user())) {
            $this->crud->allowAccess('list');
        }

        if ($entity && $reservationPolicy->view(backpack_user(), $entity)) {
            $this->crud->allowAccess('show');
        }

        if ($reservationPolicy->create(backpack_user())) {
            $this->crud->allowAccess('create');
        }

        if ($entity && $reservationPolicy->update(backpack_user(), $entity)) {
            $this->crud->allowAccess('update');
        }

        if ($entity && $reservationPolicy->delete(backpack_user(), $entity)) {
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

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
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
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('title')->label('Motivo de la reservación');
        CRUD::field('start_reservation')
            ->type('datetime')
            ->label('Fecha de la reservación')
            ->hint('Todas las reservaciones tendrán una (1) hora de duración');

        CRUD::field([
            'label'     => 'Sala de la reservación',
            'type'      => 'select',
            'name'      => 'room_id', // the db column for the foreign key

            'model'     => Room::class, // related model
            'attribute' => 'name',

            'options'   => (function ($query) {
                return $query->where('status', RoomStatus::Available)->get();
            }),
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
