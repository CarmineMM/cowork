<?php

namespace App\Services;

class Permissions
{
    public array $adminPermissions = [
        'admin.roles.index',
        'admin.roles.create',
        'admin.roles.update',
        'admin.roles.delete',
    ];

    public array $clientPermissions = [];
}
