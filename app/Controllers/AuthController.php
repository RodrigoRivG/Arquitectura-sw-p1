<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Instructor;

class AuthController extends Controller
{
    private Instructor $instructor;

    public function __construct()
    {
        $this->instructor = new Instructor();
    }

    public function login(): void
    {
        if (Auth::estaAutenticado()) {
            $this->redirect('index.php?controller=categoria&action=listar');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $instructor = $email !== '' ? $this->instructor->buscarPorEmail($email) : null;

            if ($instructor === null || !password_verify($password, $instructor['password_hash'])) {
                $this->render('auth/login', ['error' => 'Email o contraseña incorrectos.', 'email' => $email]);
                return;
            }

            Auth::iniciarSesion($instructor);
            $this->redirect('index.php?controller=categoria&action=listar');
            return;
        }

        $this->render('auth/login', ['error' => null, 'email' => '']);
    }

    public function logout(): void
    {
        Auth::cerrarSesion();
        $this->redirect('index.php?controller=public&action=index');
    }
}
