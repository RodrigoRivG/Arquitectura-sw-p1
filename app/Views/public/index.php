<?php require dirname(__DIR__) . '/layout/header_publico.php'; ?>

<h2>Catálogo de rutinas</h2>

<div class="filtros">
    <a class="<?= $categoriaSeleccionada === 0 ? 'activo' : '' ?>" href="index.php?controller=public&action=index">Todas</a>
    <?php foreach ($categorias as $categoria): ?>
        <a class="<?= $categoriaSeleccionada === (int) $categoria['id'] ? 'activo' : '' ?>"
           href="index.php?controller=public&action=index&categoria_id=<?= (int) $categoria['id'] ?>">
            <?= htmlspecialchars($categoria['nombre']) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($rutinas)): ?>
    <p>No hay rutinas publicadas en esta categoría todavía.</p>
<?php endif; ?>

<div class="rutina-grid">
    <?php foreach ($rutinas as $rutina): ?>
        <a class="rutina-card" href="index.php?controller=public&action=rutina&id=<?= (int) $rutina['id'] ?>">
            <span class="categoria-tag"><?= htmlspecialchars($rutina['categoria_nombre']) ?></span>
            <h3><?= htmlspecialchars($rutina['nombre']) ?></h3>
            <?php if (!empty($rutina['nivel'])): ?>
                <p class="ejercicio-meta">Nivel: <?= htmlspecialchars($rutina['nivel']) ?></p>
            <?php endif; ?>
            <p><?= htmlspecialchars($rutina['descripcion'] ?? '') ?></p>
        </a>
    <?php endforeach; ?>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
