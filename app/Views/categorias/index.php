<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Categorías</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a class="btn btn-add" href="index.php?controller=categoria&action=crear">Nueva categoría</a></p>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categorias as $categoria): ?>
            <tr>
                <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                <td><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></td>
                <td>
                    <a class="btn btn-small" href="index.php?controller=categoria&action=editar&id=<?= (int) $categoria['id'] ?>">Editar</a>
                    <form action="index.php?controller=categoria&action=eliminar&id=<?= (int) $categoria['id'] ?>" method="post" style="display:inline">
                        <button class="btn btn-small btn-danger" type="submit" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
