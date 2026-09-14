<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$video = $video ?? null;
$error = $error ?? null;
$esEdicion = $video !== null && isset($video['id']);
$action = $esEdicion
    ? 'index.php?controller=video&action=editar&id=' . (int) $video['id']
    : 'index.php?controller=video&action=crear&ejercicio_id=' . (int) $ejercicio['id'];
?>

<h2><?= $esEdicion ? 'Editar video' : 'Nuevo video' ?> — <?= htmlspecialchars($ejercicio['nombre']) ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= $action ?>" method="post">
    <label>
        Título
        <input type="text" name="titulo" value="<?= htmlspecialchars($video['titulo'] ?? '') ?>" required>
    </label>
    <label>
        URL
        <input type="url" name="url" value="<?= htmlspecialchars($video['url'] ?? '') ?>" required>
    </label>
    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-secondary" href="index.php?controller=video&action=listar&ejercicio_id=<?= (int) $ejercicio['id'] ?>">Cancelar</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
