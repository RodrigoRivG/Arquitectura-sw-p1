<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gym Admin</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header>
    <h1>Panel del instructor</h1>
    <nav>
        <a href="index.php?controller=categoria&action=listar">Categorías</a>
        <a href="index.php?controller=rutina&action=listar">Rutinas</a>
        <a href="index.php?controller=ejercicio&action=listar">Ejercicios</a>
        <a href="index.php?controller=public&action=index">Ver sitio público</a>
        <a href="index.php?controller=auth&action=logout">
            Cerrar sesión (<?= htmlspecialchars(\App\Core\Auth::nombreInstructor() ?? '') ?>)
        </a>
    </nav>
</header>
<main>
