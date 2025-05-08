<?php

namespace App\Services;

class Permissions
{
    /**
     * Permisos para los administradores
     *
     * @var array
     */
    public array $adminPermissions = [
        'admin.roles.index',
        'admin.roles.create',
        'admin.roles.update',
        'admin.roles.delete',

        'admin.rooms.index',
        'admin.rooms.create',
        'admin.rooms.update',
        'admin.rooms.delete',

        'admin.reservations.index',
        'admin.reservations.create',
        'admin.reservations.update',
        'admin.reservations.delete',
    ];

    /**
     * Permisos para los clientes
     *
     * @var array
     */
    public array $clientPermissions = [
        'client.reservations.index',
        'client.reservations.create',
        'client.reservations.update',
        'client.reservations.delete',
    ];
}
