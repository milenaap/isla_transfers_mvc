<?php
// app/controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;

class AuthController extends Controller
{
    // ======================
    //  VISTAS
    // ======================

    public function showLogin(): void
    {
        $this->render('login');
    }

    public function showRegister(): void
    {
        $this->render('register');
    }

    // ======================
    //  LOGIN
    // ======================

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (!$email || !$pass) {
            $_SESSION['login_error'] = 'Email y contraseña son obligatorios.';
            $this->redirect('login');
        }

        // 1) ADMIN
        $st = DB::pdo()->prepare("SELECT * FROM transfer_admin WHERE email_admin = ? LIMIT 1");
        $st->execute([$email]);
        $admin = $st->fetch(\PDO::FETCH_ASSOC);

        if ($admin && $pass === $admin['password']) {
            $_SESSION['user'] = [
                'id'    => (int)$admin['id_admin'],
                'name'  => $admin['nombre'] ?? 'Admin',
                'email' => $admin['email_admin'],
                'role'  => 'admin',
            ];
            $this->redirect('admin');
        }

        // 2) HOTEL
        $st = DB::pdo()->prepare("SELECT * FROM transfer_hoteles WHERE email_hotel = ? LIMIT 1");
        $st->execute([$email]);
        $hotel = $st->fetch(\PDO::FETCH_ASSOC);

        if ($hotel && $pass === $hotel['password']) {
            $_SESSION['user'] = [
                'id'    => (int)$hotel['id_hotel'],
                'name'  => $hotel['nombre'],
                'email' => $hotel['email_hotel'],
                'role'  => 'hotel',
            ];
            $this->redirect('user');
        }

        // 3) VIAJERO
        $st = DB::pdo()->prepare("SELECT * FROM transfer_viajeros WHERE email_viajero = ? LIMIT 1");
        $st->execute([$email]);
        $user = $st->fetch(\PDO::FETCH_ASSOC);

        if ($user && $pass === $user['password']) {
            $_SESSION['user'] = [
                'id'    => (int)$user['id_viajero'],
                'name'  => $user['nombre'],
                'email' => $user['email_viajero'],
                'role'  => 'user',
            ];
            $this->redirect('user');
        }

        // Si llega aquí, login incorrecto
        $_SESSION['login_error'] = 'Credenciales inválidas.';
        $this->redirect('login');
    }

    // ======================
    //  REGISTRO
    // ======================

    public function register(): void
    {
        $tipo     = $_POST['tipo_cliente'] ?? 'particular';
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$tipo || !$email || !$password) {
            $_SESSION['register_error'] = 'Tipo de cliente, email y contraseña son obligatorios.';
            $this->redirect('register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'El email no tiene un formato válido.';
            $this->redirect('register');
        }

        if ($tipo === 'hotel') {
            // ------- HOTEL -------
            $nombreHotel = trim($_POST['nombre_hotel'] ?? '');
            $idZona      = !empty($_POST['id_zona']) ? (int)$_POST['id_zona'] : null;
            $comision    = $_POST['comision'] !== '' ? (int)$_POST['comision'] : null;

            if (!$nombreHotel) {
                $_SESSION['register_error'] = 'El nombre del hotel es obligatorio para clientes corporativos.';
                $this->redirect('register');
            }

            // Email duplicado en hoteles
            $st = DB::pdo()->prepare("SELECT 1 FROM transfer_hoteles WHERE email_hotel=? LIMIT 1");
            $st->execute([$email]);
            if ($st->fetchColumn()) {
                $_SESSION['register_error'] = 'Ya existe un hotel registrado con ese email.';
                $this->redirect('register');
            }

            $st = DB::pdo()->prepare("
                INSERT INTO transfer_hoteles (id_zona, nombre, Comision, email_hotel, password)
                VALUES (?,?,?,?,?)
            ");
            $st->execute([$idZona, $nombreHotel, $comision, $email, $password]);
            $id = (int)DB::pdo()->lastInsertId();

            $_SESSION['user'] = [
                'id'    => $id,
                'name'  => $nombreHotel,
                'email' => $email,
                'role'  => 'hotel',
            ];

            $this->redirect('user');

        } else {
            // ------- PARTICULAR / VIAJERO -------
            $nombre       = trim($_POST['nombre'] ?? '');
            $apellido1    = trim($_POST['apellido1'] ?? '');
            $apellido2    = trim($_POST['apellido2'] ?? '');
            $direccion    = trim($_POST['direccion'] ?? '');
            $codigoPostal = trim($_POST['codigoPostal'] ?? '');
            $ciudad       = trim($_POST['ciudad'] ?? '');
            $pais         = trim($_POST['pais'] ?? '');

            if (!$nombre || !$apellido1 || !$direccion || !$codigoPostal || !$ciudad || !$pais) {
                $_SESSION['register_error'] = 'Rellena todos los datos personales del cliente particular.';
                $this->redirect('register');
            }

            $st = DB::pdo()->prepare("SELECT 1 FROM transfer_viajeros WHERE email_viajero=? LIMIT 1");
            $st->execute([$email]);
            if ($st->fetchColumn()) {
                $_SESSION['register_error'] = 'Ya existe un viajero registrado con ese email.';
                $this->redirect('register');
            }

            $st = DB::pdo()->prepare("
                INSERT INTO transfer_viajeros
                (nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email_viajero, password)
                VALUES (?,?,?,?,?,?,?,?,?)
            ");
            $st->execute([
                $nombre, $apellido1, $apellido2,
                $direccion, $codigoPostal, $ciudad, $pais,
                $email, $password
            ]);
            $id = (int)DB::pdo()->lastInsertId();

            $_SESSION['user'] = [
                'id'    => $id,
                'name'  => $nombre,
                'email' => $email,
                'role'  => 'user',
            ];

            $this->redirect('user');
        }
    }

    // ======================
    //  LOGOUT
    // ======================

    public function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $this->redirect('home');
    }
}
