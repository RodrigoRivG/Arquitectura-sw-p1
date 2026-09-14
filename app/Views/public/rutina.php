<?php require dirname(__DIR__) . '/layout/header_publico.php'; ?>

<p><a class="btn btn-secondary" href="index.php?controller=public&action=index">&laquo; Volver al catálogo</a></p>

<span class="categoria-tag"><?= htmlspecialchars($rutina['categoria_nombre']) ?></span>
<h2><?= htmlspecialchars($rutina['nombre']) ?></h2>

<?php if (!empty($rutina['nivel'])): ?>
    <p class="ejercicio-meta">Nivel: <?= htmlspecialchars($rutina['nivel']) ?></p>
<?php endif; ?>

<?php if (!empty($rutina['descripcion'])): ?>
    <p><?= htmlspecialchars($rutina['descripcion']) ?></p>
<?php endif; ?>

<h2>Ejercicios</h2>

<?php if (empty($ejercicios)): ?>
    <p>Esta rutina todavía no tiene ejercicios asignados.</p>
<?php endif; ?>

<?php foreach ($ejercicios as $ejercicio): ?>
    <div class="ejercicio-block">
        <h3><?= htmlspecialchars($ejercicio['ejercicio_nombre']) ?></h3>
        <p class="ejercicio-meta">
            <?php if ($ejercicio['series'] !== null): ?>Series: <?= (int) $ejercicio['series'] ?> &nbsp;<?php endif; ?>
            <?php if ($ejercicio['repeticiones'] !== null): ?>Repeticiones: <?= (int) $ejercicio['repeticiones'] ?><?php endif; ?>
        </p>
        <?php if (!empty($ejercicio['ejercicio_descripcion'])): ?>
            <p><?= htmlspecialchars($ejercicio['ejercicio_descripcion']) ?></p>
        <?php endif; ?>

        <?php if (!empty($ejercicio['videos'])): ?>
            <div class="video-grid">
                <?php foreach ($ejercicio['videos'] as $video): ?>
                    <div class="video-card">
                        <h3><?= htmlspecialchars($video['titulo']) ?></h3>
                        <?= \App\Core\VideoEmbed::render($video['url']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
