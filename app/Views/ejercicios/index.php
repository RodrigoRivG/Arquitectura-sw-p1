<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Ejercicios</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a class="btn btn-add" href="index.php?controller=ejercicio&action=crear">Nuevo ejercicio</a></p>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Video</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ejercicios as $ejercicio): ?>
            <tr>
                <td><?= htmlspecialchars($ejercicio['nombre']) ?></td>
                <td><?= htmlspecialchars($ejercicio['descripcion'] ?? '') ?></td>
                <td>
                    <?php if (!empty($ejercicio['video_url'])): ?>
                        <a href="<?= htmlspecialchars($ejercicio['video_url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($ejercicio['video_titulo']) ?></a>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-small" href="index.php?controller=ejercicio&action=editar&id=<?= (int) $ejercicio['id'] ?>">Editar</a>
                    <form action="index.php?controller=ejercicio&action=eliminar&id=<?= (int) $ejercicio['id'] ?>" method="post" style="display:inline">
                        <button class="btn btn-small btn-danger" type="submit" onclick="return confirm('¿Eliminar este ejercicio? También se eliminará su video.')">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
