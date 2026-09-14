<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Auth::estaAutenticado()) {
            $this->redirect('index.php?controller=auth&action=login');
        }
    }
}
