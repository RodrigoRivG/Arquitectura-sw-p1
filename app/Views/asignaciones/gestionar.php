<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Ejercicios de la rutina: <?= htmlspecialchars($rutina['nombre']) ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p><a class="btn btn-secondary" href="index.php?controller=rutina&action=listar">Volver a rutinas</a></p>

<table>
    <thead>
        <tr>
            <th>Ejercicio</th>
            <th>Series</th>
            <th>Repeticiones</th>
            <th>Orden</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($asignados)): ?>
            <tr><td colspan="5">Esta rutina todavía no tiene ejercicios asignados.</td></tr>
        <?php endif; ?>
        <?php foreach ($asignados as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['ejercicio_nombre']) ?></td>
                <td><?= htmlspecialchars((string) ($item['series'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($item['repeticiones'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string) ($item['orden'] ?? '')) ?></td>
                <td>
                    <form action="index.php?controller=asignacion&action=quitar" method="post" style="display:inline">
                        <input type="hidden" name="rutina_id" value="<?= (int) $rutina['id'] ?>">
                        <input type="hidden" name="ejercicio_id" value="<?= (int) $item['ejercicio_id'] ?>">
                        <button class="btn btn-small btn-danger" type="submit" onclick="return confirm('¿Quitar este ejercicio de la rutina?')">Quitar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Asignar ejercicio</h2>

<?php if (empty($disponibles)): ?>
    <p>
        No hay ejercicios disponibles para asignar
        (o ya están todos asignados a esta rutina, o el catálogo está vacío).
        <a class="btn btn-small" href="index.php?controller=ejercicio&action=crear">Crear ejercicio</a>
    </p>
<?php else: ?>
    <form action="index.php?controller=asignacion&action=asignar" method="post">
        <input type="hidden" name="rutina_id" value="<?= (int) $rutina['id'] ?>">
        <label>
            Ejercicio
            <select name="ejercicio_id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($disponibles as $ejercicio): ?>
                    <option value="<?= (int) $ejercicio['id'] ?>"><?= htmlspecialchars($ejercicio['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Series
            <input type="number" name="series" min="0">
        </label>
        <label>
            Repeticiones
            <input type="number" name="repeticiones" min="0">
        </label>
        <label>
            Orden
            <input type="number" name="orden" min="0">
        </label>
        <button class="btn btn-primary" type="submit">Asignar</button>
    </form>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
