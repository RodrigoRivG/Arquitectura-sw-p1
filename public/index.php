<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/autoload.php';

use App\Core\Router;

session_start();

$router = new Router();
$router->dispatch($_GET);
