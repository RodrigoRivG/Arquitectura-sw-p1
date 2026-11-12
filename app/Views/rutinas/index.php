<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Rutinas</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a class="btn btn-add" href="index.php?controller=rutina&action=crear">Nueva rutina</a></p>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Nivel</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rutinas as $rutina): ?>
            <tr>
                <td><?= htmlspecialchars($rutina['nombre']) ?></td>
                <td><?= htmlspecialchars($rutina['categoria_nombre']) ?></td>
                <td><?= htmlspecialchars($rutina['nivel'] ?? '') ?></td>
                <td><?= htmlspecialchars($rutina['descripcion'] ?? '') ?></td>
                <td>
                    <a class="btn btn-small" href="index.php?controller=rutina&action=editar&id=<?= (int) $rutina['id'] ?>">Editar</a>
                    <form action="index.php?controller=rutina&action=eliminar&id=<?= (int) $rutina['id'] ?>" method="post" style="display:inline">
                        <button class="btn btn-small btn-danger" type="submit" onclick="return confirm('¿Eliminar esta rutina?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
