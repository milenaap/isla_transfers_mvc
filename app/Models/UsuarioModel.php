<?php
// app/models/UsuarioModel.php

class UsuarioModel
{
    public static function findAdminByEmail(PDO $pdo, string $email)
    {
        $st = $pdo->prepare("SELECT * FROM transfer_admin WHERE email_admin = ? LIMIT 1");
        $st->execute([$email]);
        return $st->fetch();
    }

    public static function findHotelByEmail(PDO $pdo, string $email)
    {
        $st = $pdo->prepare("SELECT * FROM transfer_hoteles WHERE email_hotel = ? LIMIT 1");
        $st->execute([$email]);
        return $st->fetch();
    }

    public static function findViajeroByEmail(PDO $pdo, string $email)
    {
        $st = $pdo->prepare("SELECT * FROM transfer_viajeros WHERE email_viajero = ? LIMIT 1");
        $st->execute([$email]);
        return $st->fetch();
    }

    public static function createViajero(PDO $pdo, array $data): void
    {
        $sql = "INSERT INTO transfer_viajeros
            (nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email_viajero, password)
            VALUES (?,?,?,?,?,?,?,?,?)";
        $st = $pdo->prepare($sql);
        $st->execute([
            $data['nombre'],
            $data['apellido1'],
            $data['apellido2'],
            $data['direccion'],
            $data['codigoPostal'],
            $data['ciudad'],
            $data['pais'],
            $data['email_viajero'],
            $data['password'],
        ]);
    }
}
