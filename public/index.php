<?php
// public/index.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/helpers.php';

// Autoload sencillo PSR-4 para App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\PerfilController;
use App\Controllers\ReservaController;
use App\Controllers\VehiculoController;
use App\Controllers\CalendarioController;

$page = $_GET['page'] ?? 'home';

switch ($page) {
    // ======================
    // PÁGINA INICIO (FRONT)
    // ======================
    case 'home':
        (new HomeController())->index();
        break;

    // ======================
    // AUTH
    // ======================
    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'login_submit':
        (new AuthController())->login();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'register':
        (new AuthController())->showRegister();
        break;

    case 'register_submit':
        (new AuthController())->register();
        break;

    // ======================
    // PANEL ADMIN
    // ======================
    case 'admin':
        require_admin();                 // SOLO ADMIN
        (new AdminController())->index();
        break;

    // ======================
    // PANEL PRINCIPAL USUARIO / HOTEL
    // ======================
    case 'user':
        require_login();                 // cualquier usuario logueado
        (new UserController())->index();
        break;

    // ======================
    // PERFIL
    // ======================
    case 'perfil':
        require_login();
        (new PerfilController())->show();
        break;

    case 'perfil_update':
        require_login();
        (new PerfilController())->update();
        break;

    // ======================
    // RESERVAS (ADMIN + USUARIO)
    // ======================
    // Lista de reservas SOLO admin
    case 'reservas_admin':
        require_admin();
        (new ReservaController())->index();
        break;

    // Formulario de nueva reserva (admin y usuarios)
    case 'reserva_form':
        require_login();
        (new ReservaController())->create();
        break;

    // Guardar reserva (admin y usuarios)
    case 'reserva_save':
        require_login();
        (new ReservaController())->store();
        break;

    // Eliminar / cancelar reserva (solo admin)
    case 'reserva_delete':
        require_login();
        (new ReservaController())->destroy();
        break;

    case 'reserva_edit':
        require_login();
        (new ReservaController())->edit();
        break;
        
    case 'reserva_update':
        require_login();
        (new ReservaController())->update();
        break;


    // ======================
    // VEHÍCULOS (SOLO ADMIN)
    // ======================
    case 'vehiculos':
        require_admin();
        (new VehiculoController())->index();
        break;

    case 'vehiculo_form':
        require_admin();
        (new VehiculoController())->form();
        break;

    case 'vehiculo_save':
        require_admin();
        (new VehiculoController())->save();
        break;

    case 'vehiculo_delete':
        require_admin();
        (new VehiculoController())->delete();
        break;
        // HOTELES / DESTINOS
    case 'hoteles':
        require_admin();
        (new \App\Controllers\HotelController())->index();
        break;

    case 'hotel_form':
        require_admin();
        (new \App\Controllers\HotelController())->form();
        break;

    case 'hotel_save':
        require_admin();
        (new \App\Controllers\HotelController())->save();
        break;

    case 'hotel_delete':
        require_admin();
        (new \App\Controllers\HotelController())->delete();
        break;


    // ======================
    // CALENDARIO (ADMIN + USUARIO)
    // ======================
    case 'calendario':
        require_login();
        (new CalendarioController())->index();
        break;

    // ======================
    // POR DEFECTO → HOME
    // ======================
    default:
        (new HomeController())->index();
        break;
}
