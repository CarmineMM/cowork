{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Reservaciones" icon="la la-calendar-check" :link="backpack_url('reservation')" />

@if (backpack_user()->can('admin.rooms.index'))
    <x-backpack::menu-item title="Salas" icon="la la-door-open" :link="backpack_url('room')" />
@endif

@if (backpack_user()->can('admin.roles.index'))
    <x-backpack::menu-item title="Roles" icon="la la-user-shield" :link="backpack_url('role')" />
@endif
