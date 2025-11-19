<?php
// app/Controllers/PerfilController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use PDO;

class PerfilController extends Controller
{
    public function show(): void
    {
        $user = current_user();
        $this->render('perfil', ['user' => $user]);
    }

    public function update(): void
    {
        $pdo  = DB::pdo();
        $user = current_user();

        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password']   ?? '';

        if (!$name || !$email) {
            $_SESSION['perfil_error'] = 'Nombre y email son obligatorios';
            $this->redirect('perfil');
        }

        $id   = $user['id'];
        $role = $user['role'];

        if ($role === 'admin') {
            if ($pass) {
                $st = $pdo->prepare("
                    UPDATE transfer_admin 
                    SET nombre = ?, email_admin = ?, password = ?
                    WHERE id_admin = ?
                ");
                $st->execute([$name, $email, $pass, $id]);
            } else {
                $st = $pdo->prepare("
                    UPDATE transfer_admin 
                    SET nombre = ?, email_admin = ?
                    WHERE id_admin = ?
                ");
                $st->execute([$name, $email, $id]);
            }

        } elseif ($role === 'hotel') {
            if ($pass) {
                $st = $pdo->prepare("
                    UPDATE transfer_hoteles 
                    SET nombre = ?, email_hotel = ?, password = ?
                    WHERE id_hotel = ?
                ");
                $st->execute([$name, $email, $pass, $id]);
            } else {
                $st = $pdo->prepare("
                    UPDATE transfer_hoteles 
                    SET nombre = ?, email_hotel = ?
                    WHERE id_hotel = ?
                ");
                $st->execute([$name, $email, $id]);
            }

        } elseif ($role === 'user') {
            if ($pass) {
                $st = $pdo->prepare("
                    UPDATE transfer_viajeros 
                    SET nombre = ?, email_viajero = ?, password = ?
                    WHERE id_viajero = ?
                ");
                $st->execute([$name, $email, $pass, $id]);
            } else {
                $st = $pdo->prepare("
                    UPDATE transfer_viajeros 
                    SET nombre = ?, email_viajero = ?
                    WHERE id_viajero = ?
                ");
                $st->execute([$name, $email, $id]);
            }
        }

        // Actualizar sesión
        $_SESSION['user']['name']  = $name;
        $_SESSION['user']['email'] = $email;

        $this->redirect('perfil');
    }
}
