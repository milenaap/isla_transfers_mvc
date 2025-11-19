<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use PDO;

class VehiculoController extends Controller
{
    public function index(): void
    {
        $pdo = DB::pdo();
        $vehiculos = $pdo->query("
            SELECT id_vehiculo, `Descripción` AS descripcion, email_conductor
            FROM transfer_vehiculos
            ORDER BY `Descripción`
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('vehiculos_list', ['vehiculos' => $vehiculos]);
    }

    public function form(): void
    {
        $pdo = DB::pdo();
        $id  = (int)($_GET['id'] ?? 0);
        $vehiculo = null;

        if ($id) {
            $st = $pdo->prepare("
                SELECT id_vehiculo, `Descripción` AS descripcion, email_conductor
                FROM transfer_vehiculos
                WHERE id_vehiculo = ?
            ");
            $st->execute([$id]);
            $vehiculo = $st->fetch(PDO::FETCH_ASSOC);
        }

        $this->render('vehiculos_form', ['vehiculo' => $vehiculo]);
    }

    public function save(): void
    {
        $pdo  = DB::pdo();
        $id   = (int)($_POST['id_vehiculo'] ?? 0);
        $desc = trim($_POST['descripcion'] ?? '');
        $mail = trim($_POST['email_conductor'] ?? '');

        if (!$desc) {
            $_SESSION['vehiculo_error'] = 'La descripción es obligatoria.';
            header('Location: /index.php?page=vehiculo_form' . ($id ? "&id=$id" : ''));
            exit;
        }

        if ($id) {
            $st = $pdo->prepare("
                UPDATE transfer_vehiculos
                SET `Descripción` = ?, email_conductor = ?
                WHERE id_vehiculo = ?
            ");
            $st->execute([$desc, $mail, $id]);
        } else {
            $st = $pdo->prepare("
                INSERT INTO transfer_vehiculos (`Descripción`, email_conductor)
                VALUES (?,?)
            ");
            $st->execute([$desc, $mail]);
        }

        header('Location: /index.php?page=vehiculos');
        exit;
    }

    public function delete(): void
    {
        $pdo = DB::pdo();
        $id  = (int)($_GET['id'] ?? 0);

        if ($id) {
            $st = $pdo->prepare("DELETE FROM transfer_vehiculos WHERE id_vehiculo = ?");
            $st->execute([$id]);
        }

        header('Location: /index.php?page=vehiculos');
        exit;
    }
}
