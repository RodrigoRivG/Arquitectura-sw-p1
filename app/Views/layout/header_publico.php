<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Rutinas del gimnasio</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header>
    <h1>Rutinas de entrenamiento</h1>
    <nav>
        <a href="index.php?controller=public&action=index">Catálogo</a>
        <?php if (\App\Core\Auth::estaAutenticado()): ?>
            <a href="index.php?controller=categoria&action=listar">Panel de administración</a>
        <?php else: ?>
            <a href="index.php?controller=auth&action=login">Iniciar sesión</a>
        <?php endif; ?>
    </nav>
</header>
<main>
