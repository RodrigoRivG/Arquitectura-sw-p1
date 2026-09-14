<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function iniciarSesion(array $instructor): void
    {
        $_SESSION['instructor_id'] = $instructor['id'];
        $_SESSION['instructor_nombre'] = $instructor['nombre'];
    }

    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['instructor_id']);
    }

    public static function cerrarSesion(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function nombreInstructor(): ?string
    {
        return $_SESSION['instructor_nombre'] ?? null;
    }
}
