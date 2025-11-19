<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        if (!empty($_SESSION['user'])) {

            // ADMIN
            if ($_SESSION['user']['role'] === 'admin') {
                $this->redirect('admin');
            }

            // HOTEL / VIAJERO
                $this->redirect('user');
        }

        // Visitante
        $this->render('home');
    }
}
