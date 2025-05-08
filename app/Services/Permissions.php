<?php

namespace App\Services;

class Permissions
{
    public array $adminPermissions = [
        'admin.roles.index',
        'admin.roles.create',
        'admin.roles.update',
        'admin.roles.delete',

        'admin.rooms.index',
        'admin.rooms.create',
        'admin.rooms.update',
        'admin.rooms.delete',
    ];

    public array $clientPermissions = [];
}
