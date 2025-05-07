<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Policies\RolePolicy;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RoleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RoleCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Role::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/role');
        CRUD::setEntityNameStrings('role', 'roles');


        // Verificar permisos para cada operación
        $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);
        $rolePolicy = new RolePolicy;

        if ($rolePolicy->viewAny(backpack_user())) {
            $this->crud->allowAccess('list');
        }

        if (backpack_user()->can('admin.roles.index')) {
            $this->crud->allowAccess('show');
        }

        if ($rolePolicy->create(backpack_user())) {
            $this->crud->allowAccess('create');
        }

        if (backpack_user()->can('admin.roles.update')) {
            $this->crud->allowAccess('update');
        }

        if (backpack_user()->can('admin.roles.delete')) {
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
        CRUD::column('name');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2',
        ]);
        CRUD::field('name')->label('Nombre del Rol')->tab('Información');

        // Grupo para los permisos de administrador
        CRUD::group(
            CRUD::field([
                'label'     => 'Permisos de posibles',
                'type'      => 'checklist',
                'name'      => 'permissions',
                'entity'    => 'permissions',
                'attribute' => 'name',
                'model'     => 'Spatie\Permission\Models\Permission',
                'pivot'     => true,
                'show_select_all' => true,
                // 'options' => (function ($query) {
                //     return $query->whereLike('name', 'admin.%');
                // }),
            ]),
        )->tab('Permisos');

        // Grupo para los permisos de administrador


        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
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
