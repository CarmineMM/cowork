<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Room\Status as RoomStatus;
use App\Http\Requests\RoomRequest;
use App\Policies\RoomPolicy;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RoomCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RoomCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Room::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/room');
        CRUD::setEntityNameStrings('room', 'rooms');

        $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);
        $roomPolicy = new RoomPolicy;

        if ($roomPolicy->viewAny(backpack_user())) {
            $this->crud->allowAccess('list');
        }

        if (backpack_user()->can('admin.rooms.index')) {
            $this->crud->allowAccess('show');
        }

        if ($roomPolicy->create(backpack_user())) {
            $this->crud->allowAccess('create');
        }

        if (backpack_user()->can('admin.rooms.update')) {
            $this->crud->allowAccess('update');
        }

        if (backpack_user()->can('admin.rooms.delete')) {
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
        CRUD::column('name')->label('Nombre de la sala');
        CRUD::column('description')->label('Descripción');
        CRUD::column('status')
            ->label('Estado de la sala')
            ->value(fn($entry) => $entry->status->getLabel())
            ->wrapper([
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    return match ($entry->status->value) {
                        RoomStatus::Available->value => 'badge badge-primary',
                        RoomStatus::NotAvailable->value => 'badge badge-secondary',
                    };
                },
            ]);

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
        CRUD::setValidation(RoomRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.
        CRUD::field('name')->label('Nombre de la sala');
        CRUD::field('description')->label('Descripción')->type('textarea');
        CRUD::field([
            'name'  => 'status',
            'label' => 'Disponible para reservaciones',
            'type'  => 'enum',
            'default' => RoomStatus::Available->value,
            // optional, specify the enum options with custom display values
            'options' => RoomStatus::getLabels(),
        ]);

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
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
