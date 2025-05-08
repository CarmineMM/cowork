# Antes de iniciar el proyecto leer aquí:

Ejecutar las migraciones y seeders que tienen los usuario bases y data de prueba para el aplicativo

```shell
php artisan migrate --seed
```

Revisar el UserSeeder, ahi se encuentran 2 usuarios para ingresar (Cliente y Administrador)

```php
User::create([
    'name' => 'Carmine',
    'email' => 'carmine@mail.com',
    'password' => 1234,
]);
User::create([
    'name' => 'TW Group',
    'email' => 'hola@twgroup.cl',
    'password' => 1234,
]);
```

# Tener el .env

Asegurarse que el .env la **APP_URL** corresponda a la url donde se están lanzando el proyecto.

# Consideraciones

Hice uso de la mayor cantidad de conceptos de Laravel que vi que podia usar, como los Enums, Traits, Policies, Request, Rules...
Probablemente me hubiera gustado hacer uso de los Notifications y Mails (Como notificar a un usuario que su reservación) fue aprobada o rechazada,
los Jobs (Para mandar Emails o las mismas notificaciones), Observadores (para saber cuando una reservación fue creada en pendiente y necesita probación), en fin...

Hay un par de cosas en las que tuve problemas con Backpack por que eran features PRO, como por ejemplo los filtros o las exportaciones a Excel,
por suerte backpack basa sus CRUDs en controladores asi que no fue muy complejo hacer un método que pudiera cumplir la expectativa de exportar las reservaciones...
Aunque, no pude cumplir la necesidad de filtrar reservaciones por sala.

> **Nota:** Había usado backpack por encima pero, me tomare mi tiempo de conocerlo un poco mas indistintamente de los resultados de esta prueba. (Aunque siento que a backpack le falta mucho para ganarle a Filament 😄).
