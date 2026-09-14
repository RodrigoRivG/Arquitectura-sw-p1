<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$categoria = $categoria ?? null;
$error = $error ?? null;
$action = $categoria === null
    ? 'index.php?controller=categoria&action=crear'
    : 'index.php?controller=categoria&action=editar&id=' . (int) $categoria['id'];
?>

<h2><?= $categoria === null ? 'Nueva categoría' : 'Editar categoría' ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= $action ?>" method="post">
    <label>
        Nombre
        <input type="text" name="nombre" value="<?= htmlspecialchars($categoria['nombre'] ?? '') ?>" required>
    </label>
    <label>
        Descripción
        <textarea name="descripcion"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
    </label>
    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-secondary" href="index.php?controller=categoria&action=listar">Cancelar</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
