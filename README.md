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
