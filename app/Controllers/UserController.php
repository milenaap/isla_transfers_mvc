<?php
// app/Controllers/UserController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ReservaModel;

class UserController extends Controller
{
    public function index(): void
    {
        $user = current_user();

        if (!$user) {
            $this->redirect('login');
        }

        $reservas = [];

        switch ($user['role'] ?? null) {
            case 'user':   // viajero: reservas por email
                $reservas = ReservaModel::byEmail($user['email']);
                break;

            case 'hotel':  // hotel: reservas ligadas al hotel
                $reservas = ReservaModel::byHotelId((int)$user['id']);
                break;

            case 'admin':  // admin: ve todas
                $reservas = ReservaModel::all();
                break;
        }

        $this->render('user_dashboard', [
            'user'     => $user,
            'reservas' => $reservas,
        ]);
    }
}
