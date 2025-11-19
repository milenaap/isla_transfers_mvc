<?php
// app/Models/ReservaModel.php
namespace App\Models;

use App\Core\DB;
use PDO;

class ReservaModel
{
    /**
     * Reservas filtradas por email de cliente (viajero).
     */
    public static function byEmail(string $email): array
    {
        $pdo = DB::pdo();

        $st = $pdo->prepare("
            SELECT r.*,
                   h.nombre        AS hotel,
                   t.`Descripción` AS tipo
            FROM transfer_reservas r
            LEFT JOIN transfer_hoteles       h ON r.id_destino      = h.id_hotel
            LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
            WHERE r.email_cliente = ?
            ORDER BY r.fecha_entrada DESC, r.hora_entrada ASC
        ");
        $st->execute([$email]);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reservas de un hotel concreto, por id_hotel.
     */
    public static function byHotelId(int $hotelId): array
    {
        $pdo = DB::pdo();

        $st = $pdo->prepare("
            SELECT r.*,
                   h.nombre        AS hotel,
                   t.`Descripción` AS tipo
            FROM transfer_reservas r
            LEFT JOIN transfer_hoteles       h ON r.id_destino      = h.id_hotel
            LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
            WHERE r.id_hotel = ?
            ORDER BY r.fecha_entrada DESC, r.hora_entrada ASC
        ");
        $st->execute([$hotelId]);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Todas las reservas (para admin).
     */
    public static function all(): array
    {
        $pdo = DB::pdo();

        $st = $pdo->query("
            SELECT r.*,
                   h.nombre        AS hotel,
                   t.`Descripción` AS tipo
            FROM transfer_reservas r
            LEFT JOIN transfer_hoteles       h ON r.id_destino      = h.id_hotel
            LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
            ORDER BY r.fecha_entrada DESC, r.hora_entrada ASC
        ");

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar una reserva por id.
     */
    public static function find(int $id): ?array
    {
        $pdo = DB::pdo();

        $st = $pdo->prepare("
            SELECT r.*,
                   h.nombre        AS hotel,
                   t.`Descripción` AS tipo
            FROM transfer_reservas r
            LEFT JOIN transfer_hoteles       h ON r.id_destino      = h.id_hotel
            LEFT JOIN transfer_tipo_reservas t ON r.id_tipo_reserva = t.id_tipo_reserva
            WHERE r.id_reserva = ?
            LIMIT 1
        ");
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Crear reserva. Devuelve id_reserva.
     *
     * Espera en $data las claves:
     * localizador, id_hotel, id_tipo_reserva, email_cliente,
     * fecha_reserva, fecha_modificacion, id_destino,
     * fecha_entrada, hora_entrada,
     * numero_vuelo_entrada, origen_vuelo_entrada,
     * hora_vuelo_salida, fecha_vuelo_salida,
     * num_viajeros, id_vehiculo
     */
    public static function create(array $data): int
    {
        $pdo = DB::pdo();

        $sql = "
            INSERT INTO transfer_reservas (
                localizador,
                id_hotel,
                id_tipo_reserva,
                email_cliente,
                fecha_reserva,
                fecha_modificacion,
                id_destino,
                fecha_entrada,
                hora_entrada,
                numero_vuelo_entrada,
                origen_vuelo_entrada,
                hora_vuelo_salida,
                fecha_vuelo_salida,
                num_viajeros,
                id_vehiculo
            ) VALUES (
                :localizador,
                :id_hotel,
                :id_tipo_reserva,
                :email_cliente,
                :fecha_reserva,
                :fecha_modificacion,
                :id_destino,
                :fecha_entrada,
                :hora_entrada,
                :numero_vuelo_entrada,
                :origen_vuelo_entrada,
                :hora_vuelo_salida,
                :fecha_vuelo_salida,
                :num_viajeros,
                :id_vehiculo
            )
        ";

        $st = $pdo->prepare($sql);

        $st->execute([
            ':localizador'          => $data['localizador'],
            ':id_hotel'             => $data['id_hotel'],
            ':id_tipo_reserva'      => $data['id_tipo_reserva'],
            ':email_cliente'        => $data['email_cliente'],
            ':fecha_reserva'        => $data['fecha_reserva'],
            ':fecha_modificacion'   => $data['fecha_modificacion'],
            ':id_destino'           => $data['id_destino'],
            ':fecha_entrada'        => $data['fecha_entrada'],
            ':hora_entrada'         => $data['hora_entrada'],
            ':numero_vuelo_entrada' => $data['numero_vuelo_entrada'],
            ':origen_vuelo_entrada' => $data['origen_vuelo_entrada'],
            ':hora_vuelo_salida'    => $data['hora_vuelo_salida'],
            ':fecha_vuelo_salida'   => $data['fecha_vuelo_salida'],
            ':num_viajeros'         => $data['num_viajeros'],
            ':id_vehiculo'          => $data['id_vehiculo'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    /**
     * Actualizar una reserva existente.
     *
     * Recibe $id y el mismo array $data que en create()
     * (excepto que el localizador normalmente ya existe).
     */
    public static function update(int $id, array $data): void
    {
        $pdo = DB::pdo();

        $sql = "
            UPDATE transfer_reservas
            SET
                localizador          = :localizador,
                id_hotel             = :id_hotel,
                id_tipo_reserva      = :id_tipo_reserva,
                email_cliente        = :email_cliente,
                fecha_reserva        = :fecha_reserva,
                fecha_modificacion   = :fecha_modificacion,
                id_destino           = :id_destino,
                fecha_entrada        = :fecha_entrada,
                hora_entrada         = :hora_entrada,
                numero_vuelo_entrada = :numero_vuelo_entrada,
                origen_vuelo_entrada = :origen_vuelo_entrada,
                hora_vuelo_salida    = :hora_vuelo_salida,
                fecha_vuelo_salida   = :fecha_vuelo_salida,
                num_viajeros         = :num_viajeros,
                id_vehiculo          = :id_vehiculo
            WHERE id_reserva = :id_reserva
        ";

        $st = $pdo->prepare($sql);

        $st->execute([
            ':localizador'          => $data['localizador'],
            ':id_hotel'             => $data['id_hotel'],
            ':id_tipo_reserva'      => $data['id_tipo_reserva'],
            ':email_cliente'        => $data['email_cliente'],
            ':fecha_reserva'        => $data['fecha_reserva'],
            ':fecha_modificacion'   => $data['fecha_modificacion'],
            ':id_destino'           => $data['id_destino'],
            ':fecha_entrada'        => $data['fecha_entrada'],
            ':hora_entrada'         => $data['hora_entrada'],
            ':numero_vuelo_entrada' => $data['numero_vuelo_entrada'],
            ':origen_vuelo_entrada' => $data['origen_vuelo_entrada'],
            ':hora_vuelo_salida'    => $data['hora_vuelo_salida'],
            ':fecha_vuelo_salida'   => $data['fecha_vuelo_salida'],
            ':num_viajeros'         => $data['num_viajeros'],
            ':id_vehiculo'          => $data['id_vehiculo'],
            ':id_reserva'           => $id,
        ]);
    }

    /**
     * Eliminar reserva por id.
     */
    public static function delete(int $id): void
    {
        $pdo = DB::pdo();

        $st = $pdo->prepare("DELETE FROM transfer_reservas WHERE id_reserva = ?");
        $st->execute([$id]);
    }
}
