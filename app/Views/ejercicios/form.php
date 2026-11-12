<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$ejercicio = $ejercicio ?? null;
$error = $error ?? null;
$esEdicion = $ejercicio !== null && isset($ejercicio['id']);
$action = $esEdicion
    ? 'index.php?controller=ejercicio&action=editar&id=' . (int) $ejercicio['id']
    : 'index.php?controller=ejercicio&action=crear';
?>

<h2><?= $esEdicion ? 'Editar ejercicio' : 'Nuevo ejercicio' ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= $action ?>" method="post">
    <label>
        Nombre
        <input type="text" name="nombre" value="<?= htmlspecialchars($ejercicio['nombre'] ?? '') ?>" required>
    </label>
    <label>
        Descripción
        <textarea name="descripcion"><?= htmlspecialchars($ejercicio['descripcion'] ?? '') ?></textarea>
    </label>

    <fieldset>
        <legend>Video</legend>
        <label>
            Título
            <input type="text" name="video_titulo" value="<?= htmlspecialchars($ejercicio['video_titulo'] ?? '') ?>" required>
        </label>
        <label>
            URL
            <input type="url" name="video_url" value="<?= htmlspecialchars($ejercicio['video_url'] ?? '') ?>" required>
        </label>
    </fieldset>

    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-secondary" href="index.php?controller=ejercicio&action=listar">Cancelar</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
