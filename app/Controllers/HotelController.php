<?php
// app/Controllers/HotelController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use PDO;

class HotelController extends Controller
{
    /** Listado de hoteles */
    public function index(): void
    {
        $pdo = DB::pdo();

        $st = $pdo->query("
            SELECT id_hotel, id_zona, nombre, Comision, email_hotel
            FROM transfer_hoteles
            ORDER BY nombre
        ");

        $hoteles = $st->fetchAll(PDO::FETCH_ASSOC);

        $this->render('hoteles', [
            'hoteles' => $hoteles,
        ]);
    }

    /** Formulario crear / editar */
    public function form(): void
    {
        $pdo   = DB::pdo();
        $id    = (int)($_GET['id'] ?? 0);
        $hotel = null;

        if ($id) {
            $st = $pdo->prepare("
                SELECT id_hotel, id_zona, nombre, Comision, email_hotel
                FROM transfer_hoteles
                WHERE id_hotel = ?
                LIMIT 1
            ");
            $st->execute([$id]);
            $hotel = $st->fetch(PDO::FETCH_ASSOC);
        }

        $this->render('hotel_form', [
            'hotel' => $hotel,
        ]);
    }

    /** Guardar (crear o actualizar) */
    public function save(): void
    {
        $pdo = DB::pdo();

        $id        = (int)($_POST['id_hotel'] ?? 0);
        $nombre    = trim($_POST['nombre'] ?? '');
        $idZona    = $_POST['id_zona'] !== '' ? (int)$_POST['id_zona'] : null;
        $comision  = $_POST['comision'] !== '' ? (int)$_POST['comision'] : null;
        $email     = trim($_POST['email_hotel'] ?? '');
        $password  = $_POST['password'] ?? ''; // opcional

        if (!$nombre || !$email) {
            $_SESSION['hotel_error'] = 'Nombre y email del hotel son obligatorios.';
            $dest = $id ? 'hotel_form&id='.$id : 'hotel_form';
            $this->redirect($dest);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['hotel_error'] = 'El email no tiene un formato válido.';
            $dest = $id ? 'hotel_form&id='.$id : 'hotel_form';
            $this->redirect($dest);
        }

        if ($id) {
            // UPDATE
            if ($password) {
                $st = $pdo->prepare("
                    UPDATE transfer_hoteles
                    SET id_zona = ?, nombre = ?, Comision = ?, email_hotel = ?, password = ?
                    WHERE id_hotel = ?
                ");
                $st->execute([$idZona, $nombre, $comision, $email, $password, $id]);
            } else {
                $st = $pdo->prepare("
                    UPDATE transfer_hoteles
                    SET id_zona = ?, nombre = ?, Comision = ?, email_hotel = ?
                    WHERE id_hotel = ?
                ");
                $st->execute([$idZona, $nombre, $comision, $email, $id]);
            }
        } else {
            // INSERT
            $st = $pdo->prepare("
                INSERT INTO transfer_hoteles (id_zona, nombre, Comision, email_hotel, password)
                VALUES (?,?,?,?,?)
            ");
            $st->execute([$idZona, $nombre, $comision, $email, $password ?: '']);
        }

        $this->redirect('hoteles');
    }

    /** Eliminar hotel */
    public function delete(): void
    {
        $pdo = DB::pdo();
        $id  = (int)($_GET['id'] ?? 0);

        if ($id) {
            $st = $pdo->prepare("DELETE FROM transfer_hoteles WHERE id_hotel = ?");
            $st->execute([$id]);
        }

        $this->redirect('hoteles');
    }
}
