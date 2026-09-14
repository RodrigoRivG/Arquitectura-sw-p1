<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Videos de: <?= htmlspecialchars($ejercicio['nombre']) ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>
    <a class="btn btn-add" href="index.php?controller=video&action=crear&ejercicio_id=<?= (int) $ejercicio['id'] ?>">Agregar video</a>
    <a class="btn btn-secondary" href="index.php?controller=ejercicio&action=listar">Volver a ejercicios</a>
</p>

<?php if (empty($videos)): ?>
    <p>Este ejercicio todavía no tiene videos.</p>
<?php endif; ?>

<div class="video-grid">
    <?php foreach ($videos as $video): ?>
        <div class="video-card">
            <h3><?= htmlspecialchars($video['titulo']) ?></h3>
            <?= \App\Core\VideoEmbed::render($video['url']) ?>
            <div class="video-actions">
                <a class="btn btn-small" href="index.php?controller=video&action=editar&id=<?= (int) $video['id'] ?>">Editar</a>
                <form action="index.php?controller=video&action=eliminar&id=<?= (int) $video['id'] ?>" method="post" style="display:inline">
                    <button class="btn btn-small btn-danger" type="submit" onclick="return confirm('¿Eliminar este video?')">Eliminar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
