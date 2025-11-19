<?php
// app/Controllers/AdminController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use PDO;

class AdminController extends Controller
{
    public function index(): void
    {
        // Usamos la clase DB centralizada
        $pdo = DB::pdo();

        $stats = [
            'reservas'  => (int)$pdo->query("SELECT COUNT(*) FROM transfer_reservas")->fetchColumn(),
            'viajeros'  => (int)$pdo->query("SELECT COUNT(*) FROM transfer_viajeros")->fetchColumn(),
            'hoteles'   => (int)$pdo->query("SELECT COUNT(*) FROM transfer_hoteles")->fetchColumn(),
            'vehiculos' => (int)$pdo->query("SELECT COUNT(*) FROM transfer_vehiculos")->fetchColumn(),
        ];

        $this->render('admin_dashboard', [
            'stats' => $stats,
        ]);
    }
}
