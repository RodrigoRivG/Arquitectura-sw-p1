<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private const DEFAULT_CONTROLLER = 'public';
    private const DEFAULT_ACTION = 'index';

    public function dispatch(array $params): void
    {
        $controllerName = (string) ($params['controller'] ?? self::DEFAULT_CONTROLLER);
        $actionName = (string) ($params['action'] ?? self::DEFAULT_ACTION);

        $controllerClass = 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';

        if (!class_exists($controllerClass)) {
            $this->notFound("Controlador '{$controllerName}' no encontrado.");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $actionName)) {
            $this->notFound("Acción '{$actionName}' no encontrada en '{$controllerName}'.");
            return;
        }

        $controller->$actionName();
    }

    private function notFound(string $mensaje): void
    {
        http_response_code(404);
        echo '<h1>404 - No encontrado</h1><p>' . htmlspecialchars($mensaje) . '</p>';
    }
}
