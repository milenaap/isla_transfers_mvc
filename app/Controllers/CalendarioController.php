<?php
// app/Controllers/CalendarioController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use PDO;

class CalendarioController extends Controller
{
    public function index(): void
    {
        $vista     = $_GET['vista'] ?? 'semana';       // dia | semana | mes
        $fechaBase = $_GET['fecha'] ?? date('Y-m-d');  // fecha de referencia

        $user = current_user();
        $role = $user['role'] ?? null;
        $emailUser = $user['email'] ?? null;
        $idUser    = $user['id'] ?? null;

        $inicio = new \DateTime($fechaBase);

        switch ($vista) {
            case 'dia':
                $fin = clone $inicio;
                break;

            case 'semana':
                $fin = clone $inicio;
                $fin->modify('+6 days');
                break;

            case 'mes':
            default:
                $inicio->modify('first day of this month');
                $fin = clone $inicio;
                $fin->modify('last day of this month');
                break;
        }

        $pdo = DB::pdo();

        // ================
        // FILTRADO POR ROL
        // ================
        if ($role === 'admin') {
            // ADMIN → ve todo
            $sql = "
                SELECT r.*, h.nombre AS hotel, t.Descripción AS tipo
                FROM transfer_reservas r
                LEFT JOIN transfer_hoteles h ON r.id_destino = h.id_hotel
                LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
                WHERE r.fecha_entrada BETWEEN :ini AND :fin
                ORDER BY r.fecha_entrada, r.hora_entrada
            ";
            $st = $pdo->prepare($sql);
            $st->execute([
                ':ini' => $inicio->format('Y-m-d'),
                ':fin' => $fin->format('Y-m-d'),
            ]);

        } elseif ($role === 'user') {
            // USUARIO PARTICULAR → ve SOLO sus reservas + reservas cuyo email_cliente sea admin
            $sql = "
                SELECT r.*, h.nombre AS hotel, t.Descripción AS tipo
                FROM transfer_reservas r
                LEFT JOIN transfer_hoteles h ON r.id_destino = h.id_hotel
                LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
                WHERE r.fecha_entrada BETWEEN :ini AND :fin
                AND (
                    r.email_cliente = :emailUser
                    OR r.creado_por = 'admin'
                )
                ORDER BY r.fecha_entrada, r.hora_entrada
            ";

            $st = $pdo->prepare($sql);
            $st->execute([
                ':ini'       => $inicio->format('Y-m-d'),
                ':fin'       => $fin->format('Y-m-d'),
                ':emailUser' => $emailUser,
            ]);

        } elseif ($role === 'hotel') {
            // HOTEL → solo reservas que tengan su id_hotel
            $sql = "
                SELECT r.*, h.nombre AS hotel, t.Descripción AS tipo
                FROM transfer_reservas r
                LEFT JOIN transfer_hoteles h ON r.id_destino = h.id_hotel
                LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
                WHERE r.fecha_entrada BETWEEN :ini AND :fin
                AND r.id_hotel = :idHotel
                ORDER BY r.fecha_entrada, r.hora_entrada
            ";

            $st = $pdo->prepare($sql);
            $st->execute([
                ':ini'     => $inicio->format('Y-m-d'),
                ':fin'     => $fin->format('Y-m-d'),
                ':idHotel' => $idUser,
            ]);
        }

        $reservas = $st->fetchAll(PDO::FETCH_ASSOC);

        $this->render('calendario', [
            'reservas'  => $reservas,
            'vista'     => $vista,
            'fechaBase' => $fechaBase,
        ]);
    }
}
