<?php
// app/Controllers/ReservaController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Models\ReservaModel;
use PDO;

class ReservaController extends Controller
{
    // ==========================
    // LISTADO ADMIN
    // ==========================
    public function index(): void
    {
        // Admin ve todas las reservas
        $reservas = ReservaModel::all();

        $this->render('reservas_list', [
            'reservas' => $reservas,
        ]);
    }

    // ==========================
    // FORMULARIO NUEVA / EDITAR
    // ==========================
    public function create(): void
    {
        $pdo  = DB::pdo();
        $user = current_user();

        // Hoteles, vehículos y tipos de reserva
        $hoteles = $pdo->query("
            SELECT id_hotel, nombre 
            FROM transfer_hoteles
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        $vehiculos = $pdo->query("
            SELECT id_vehiculo, `Descripción` AS descripcion
            FROM transfer_vehiculos
            ORDER BY `Descripción`
        ")->fetchAll(PDO::FETCH_ASSOC);

        $tipos = $pdo->query("
            SELECT id_tipo_reserva, `Descripción` AS descripcion
            FROM transfer_tipo_reservas
            ORDER BY id_tipo_reserva
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('reservas_form', [
            'hoteles'   => $hoteles,
            'vehiculos' => $vehiculos,
            'tipos'     => $tipos,
            'user'      => $user,
        ]);
    }

    // ==========================
    // GUARDAR NUEVA RESERVA
    // ==========================
    public function store(): void
    {
        $user = current_user();
        if (!$user) {
            $this->redirect('login');
        }

        $id_tipo_reserva = (int)($_POST['id_tipo_reserva'] ?? 0);
        $fecha           = $_POST['fecha'] ?? '';
        $hora            = $_POST['hora'] ?? '';
        $id_hotel        = (int)($_POST['id_hotel'] ?? 0);
        $id_vehiculo     = (int)($_POST['id_vehiculo'] ?? 0);
        $num_viajeros    = (int)($_POST['num_viajeros'] ?? 1);
        $email_cliente   = trim($_POST['email_cliente'] ?? '');
        $num_vuelo       = trim($_POST['numero_vuelo_entrada'] ?? '');
        $origen          = trim($_POST['origen_vuelo_entrada'] ?? '');

        if (!$id_tipo_reserva || !$fecha || !$hora || !$id_hotel || !$id_vehiculo) {
            $_SESSION['reserva_error'] = 'Faltan datos obligatorios.';
            header('Location: /index.php?page=reserva_form');
            exit;
        }

        $role = $user['role'] ?? null;

        // ===== Regla de 48 horas y email para usuarios particulares =====
        if ($role === 'user') {
            // email del cliente SIEMPRE el del usuario logueado
            $email_cliente = $user['email'];

            $fechaHoraReserva = strtotime($fecha . ' ' . $hora);
            $ahora            = time();
            $diferenciaHoras  = ($fechaHoraReserva - $ahora) / 3600;

            if ($diferenciaHoras < 48) {
                $_SESSION['reserva_error'] =
                    'Las reservas deben realizarse con un mínimo de 48 horas de antelación.';
                header('Location: /index.php?page=reserva_form');
                exit;
            }

        } else {
            // Admin / hotel → el email viene del formulario
            if (!$email_cliente || !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['reserva_error'] = 'El email del cliente es obligatorio y debe ser válido.';
                header('Location: /index.php?page=reserva_form');
                exit;
            }
        }

        $fecha_reserva      = date('Y-m-d H:i:s');
        $fecha_modificacion = $fecha_reserva;

        $localizador = 'TRF-' . date('Ymd-His') . '-' .
            strtoupper(substr(md5(uniqid('', true)), 0, 6));

        $data = [
            'localizador'          => $localizador,
            'id_hotel'             => $id_hotel,
            'id_tipo_reserva'      => $id_tipo_reserva,
            'email_cliente'        => $email_cliente,
            'fecha_reserva'        => $fecha_reserva,
            'fecha_modificacion'   => $fecha_modificacion,
            'id_destino'           => $id_hotel,
            'fecha_entrada'        => $fecha,
            'hora_entrada'         => $hora,
            'numero_vuelo_entrada' => $num_vuelo,
            'origen_vuelo_entrada' => $origen,
            'hora_vuelo_salida'    => null,
            'fecha_vuelo_salida'   => null,
            'num_viajeros'         => $num_viajeros,
            'id_vehiculo'          => $id_vehiculo,
            // NUEVO: quién creó la reserva (admin / user / hotel)
            'creado_por'           => $role ?? 'desconocido',
        ];

        ReservaModel::create($data);

        $_SESSION['reserva_ok'] = 'Reserva creada correctamente.';

        if ($role === 'admin') {
            header('Location: /index.php?page=reservas_admin');
        } else {
            header('Location: /index.php?page=user');
        }
        exit;
    }

    // ==========================
    // ELIMINAR / CANCELAR
    // ==========================
    public function destroy(): void
    {
        $user = current_user();
        if (!$user) {
            $this->redirect('login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $reserva = ReservaModel::find($id);
        if (!$reserva) {
            $_SESSION['reserva_error'] = 'La reserva no existe.';
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $role = $user['role'] ?? null;

        // ===== Reglas por rol =====

        if ($role === 'user') {
            // Solo puede tocar SUS reservas
            if (strcasecmp($reserva['email_cliente'] ?? '', $user['email'] ?? '') !== 0) {
                $_SESSION['reserva_error'] = 'No puedes cancelar reservas de otros usuarios.';
                $this->redirect('user');
            }

            // Regla 48 horas
            $fecha   = $reserva['fecha_entrada'] ?? '';
            $hora    = $reserva['hora_entrada'] ?? '00:00:00';
            $ts      = strtotime("$fecha $hora");
            $ahora   = time();
            $difHoras = ($ts - $ahora) / 3600;

            if ($difHoras < 48) {
                $_SESSION['reserva_error'] =
                    'No puedes cancelar esta reserva: faltan menos de 48 horas.';
                $this->redirect('user');
            }
        }

        if ($role === 'hotel') {
            // Hotel solo puede cancelar reservas asociadas a su id_hotel
            if ((int)($reserva['id_hotel'] ?? 0) !== (int)$user['id']) {
                $_SESSION['reserva_error'] =
                    'No puedes cancelar reservas de otros hoteles.';
                $this->redirect('user');
            }
            // (si quisieras, podrías también aplicar regla de 48h aquí)
        }

        // Admin → sin restricciones adicionales

        ReservaModel::delete($id);

        $_SESSION['reserva_ok'] = 'Reserva eliminada correctamente.';

        if ($role === 'admin') {
            $this->redirect('reservas_admin');
        } else {
            $this->redirect('user');
        }
    }

    // ==========================
    // EDITAR: mostrar formulario
    // ==========================
    public function edit(): void
    {
        $user = current_user();
        if (!$user) {
            $this->redirect('login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $reserva = ReservaModel::find($id);
        if (!$reserva) {
            $_SESSION['reserva_error'] = 'La reserva no existe.';
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $role = $user['role'] ?? null;

        // Reglas de acceso similares a destroy()
        if ($role === 'user') {
            if (strcasecmp($reserva['email_cliente'] ?? '', $user['email'] ?? '') !== 0) {
                $_SESSION['reserva_error'] = 'No puedes editar reservas de otros usuarios.';
                $this->redirect('user');
            }

            $fecha   = $reserva['fecha_entrada'] ?? '';
            $hora    = $reserva['hora_entrada'] ?? '00:00:00';
            $ts      = strtotime("$fecha $hora");
            $ahora   = time();
            $difHoras = ($ts - $ahora) / 3600;

            if ($difHoras < 48) {
                $_SESSION['reserva_error'] =
                    'No puedes editar esta reserva: faltan menos de 48 horas.';
                $this->redirect('user');
            }
        }

        if ($role === 'hotel') {
            if ((int)($reserva['id_hotel'] ?? 0) !== (int)$user['id']) {
                $_SESSION['reserva_error'] =
                    'No puedes editar reservas de otros hoteles.';
                $this->redirect('user');
            }
        }

        $pdo = DB::pdo();

        $hoteles = $pdo->query("
            SELECT id_hotel, nombre 
            FROM transfer_hoteles
            ORDER BY nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        $vehiculos = $pdo->query("
            SELECT id_vehiculo, `Descripción` AS descripcion
            FROM transfer_vehiculos
            ORDER BY `Descripción`
        ")->fetchAll(PDO::FETCH_ASSOC);

        $tipos = $pdo->query("
            SELECT id_tipo_reserva, `Descripción` AS descripcion
            FROM transfer_tipo_reservas
            ORDER BY id_tipo_reserva
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('reservas_form', [
            'hoteles'   => $hoteles,
            'vehiculos' => $vehiculos,
            'tipos'     => $tipos,
            'reserva'   => $reserva,
            'modo'      => 'edit',
            'user'      => $user,
        ]);
    }

    // ==========================
    // UPDATE: guardar cambios
    // ==========================
    public function update(): void
    {
        $user = current_user();
        if (!$user) {
            $this->redirect('login');
        }

        $id = (int)($_POST['id_reserva'] ?? 0);
        if (!$id) {
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $reservaOriginal = ReservaModel::find($id);
        if (!$reservaOriginal) {
            $_SESSION['reserva_error'] = 'La reserva no existe.';
            $this->redirect($user['role'] === 'admin' ? 'reservas_admin' : 'user');
        }

        $role = $user['role'] ?? null;

        // Reglas de acceso (igual que en edit/destroy)
        if ($role === 'user') {
            if (strcasecmp($reservaOriginal['email_cliente'] ?? '', $user['email'] ?? '') !== 0) {
                $_SESSION['reserva_error'] = 'No puedes editar reservas de otros usuarios.';
                $this->redirect('user');
            }

            $fecha   = $reservaOriginal['fecha_entrada'] ?? '';
            $hora    = $reservaOriginal['hora_entrada'] ?? '00:00:00';
            $ts      = strtotime("$fecha $hora");
            $ahora   = time();
            $difHoras = ($ts - $ahora) / 3600;

            if ($difHoras < 48) {
                $_SESSION['reserva_error'] =
                    'No puedes editar esta reserva: faltan menos de 48 horas.';
                $this->redirect('user');
            }
        }

        if ($role === 'hotel') {
            if ((int)($reservaOriginal['id_hotel'] ?? 0) !== (int)$user['id']) {
                $_SESSION['reserva_error'] =
                    'No puedes editar reservas de otros hoteles.';
                $this->redirect('user');
            }
        }

        // Campos que llegan del formulario
        $id_tipo_reserva = (int)($_POST['id_tipo_reserva'] ?? 0);
        $fecha           = $_POST['fecha'] ?? '';
        $hora            = $_POST['hora'] ?? '';
        $id_hotel        = (int)($_POST['id_hotel'] ?? 0);
        $id_vehiculo     = (int)($_POST['id_vehiculo'] ?? 0);
        $num_viajeros    = (int)($_POST['num_viajeros'] ?? 1);
        $email_cliente   = trim($_POST['email_cliente'] ?? '');
        $num_vuelo       = trim($_POST['numero_vuelo_entrada'] ?? '');
        $origen          = trim($_POST['origen_vuelo_entrada'] ?? '');

        if (!$id_tipo_reserva || !$fecha || !$hora || !$id_hotel || !$id_vehiculo) {
            $_SESSION['reserva_error'] = 'Faltan datos obligatorios para actualizar la reserva.';
            header('Location: /index.php?page=reserva_edit&id=' . $id);
            exit;
        }

        if ($role === 'user') {
            // el email se mantiene como el del usuario aunque en el form haya otro
            $email_cliente = $user['email'];
        } else {
            if (!$email_cliente || !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['reserva_error'] = 'El email del cliente es obligatorio y debe ser válido.';
                header('Location: /index.php?page=reserva_edit&id=' . $id);
                exit;
            }
        }

        $fecha_reserva      = $reservaOriginal['fecha_reserva'] ?? date('Y-m-d H:i:s');
        $fecha_modificacion = date('Y-m-d H:i:s');

        $data = [
            'localizador'          => $reservaOriginal['localizador'],
            'id_hotel'             => $id_hotel,
            'id_tipo_reserva'      => $id_tipo_reserva,
            'email_cliente'        => $email_cliente,
            'fecha_reserva'        => $fecha_reserva,
            'fecha_modificacion'   => $fecha_modificacion,
            'id_destino'           => $id_hotel,
            'fecha_entrada'        => $fecha,
            'hora_entrada'         => $hora,
            'numero_vuelo_entrada' => $num_vuelo,
            'origen_vuelo_entrada' => $origen,
            'hora_vuelo_salida'    => null,
            'fecha_vuelo_salida'   => null,
            'num_viajeros'         => $num_viajeros,
            'id_vehiculo'          => $id_vehiculo,
            'creado_por'           => $reservaOriginal['creado_por'] ?? 'desconocido',
        ];

        ReservaModel::update($id, $data);

        $_SESSION['reserva_ok'] = 'Reserva actualizada correctamente.';

        if ($role === 'admin') {
            header('Location: /index.php?page=reservas_admin');
        } else {
            header('Location: /index.php?page=user');
        }
        exit;
    }
}
