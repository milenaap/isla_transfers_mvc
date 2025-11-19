<?php
// app/Core/DB.php
namespace App\Core;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config.php';
            $db = $config['db'];

            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $db['user'],
                    $db['pass'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                // Para la entrega, un mensaje claro
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
