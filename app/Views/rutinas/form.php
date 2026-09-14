<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$rutina = $rutina ?? null;
$error = $error ?? null;
$esEdicion = $rutina !== null && isset($rutina['id']);
$action = $esEdicion
    ? 'index.php?controller=rutina&action=editar&id=' . (int) $rutina['id']
    : 'index.php?controller=rutina&action=crear';
$categoriaSeleccionada = (int) ($rutina['categoria_id'] ?? 0);
$niveles = ['Principiante', 'Intermedio', 'Avanzado'];
?>

<h2><?= $esEdicion ? 'Editar rutina' : 'Nueva rutina' ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="<?= $action ?>" method="post">
    <label>
        Nombre
        <input type="text" name="nombre" value="<?= htmlspecialchars($rutina['nombre'] ?? '') ?>" required>
    </label>
    <label>
        Categoría
        <select name="categoria_id" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>" <?= (int) $categoria['id'] === $categoriaSeleccionada ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Nivel
        <select name="nivel">
            <option value="">-- Sin especificar --</option>
            <?php foreach ($niveles as $nivel): ?>
                <option value="<?= htmlspecialchars($nivel) ?>" <?= ($rutina['nivel'] ?? '') === $nivel ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nivel) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Descripción
        <textarea name="descripcion"><?= htmlspecialchars($rutina['descripcion'] ?? '') ?></textarea>
    </label>
    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-secondary" href="index.php?controller=rutina&action=listar">Cancelar</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
