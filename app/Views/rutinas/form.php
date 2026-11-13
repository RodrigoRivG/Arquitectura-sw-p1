<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$rutina = $rutina ?? null;
$error = $error ?? null;
$catalogo = $catalogo ?? [];
$asignados = $asignados ?? [];
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

    <h3>Ejercicios de la rutina</h3>

    <?php if (empty($catalogo)): ?>
        <p>
            El catálogo de ejercicios está vacío; la rutina se puede guardar sin ejercicios.
            <a class="btn btn-small" href="index.php?controller=ejercicio&action=crear">Crear ejercicio</a>
        </p>
    <?php else: ?>
        <p>Marque los ejercicios que forman parte de la rutina e indique series, repeticiones y orden.</p>
        <table>
            <thead>
                <tr>
                    <th>Incluir</th>
                    <th>Ejercicio</th>
                    <th>Series</th>
                    <th>Repeticiones</th>
                    <th>Orden</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($catalogo as $ejercicio): ?>
                    <?php
                    $ejId = (int) $ejercicio['id'];
                    $asignado = $asignados[$ejId] ?? null;
                    $campo = 'ejercicios[' . $ejId . ']';
                    ?>
                    <tr>
                        <td><input type="checkbox" name="<?= $campo ?>[ejercicio_id]" value="<?= $ejId ?>" <?= $asignado !== null ? 'checked' : '' ?>></td>
                        <td><?= htmlspecialchars($ejercicio['nombre']) ?></td>
                        <td><input type="number" min="0" name="<?= $campo ?>[series]" value="<?= htmlspecialchars((string) ($asignado['series'] ?? '')) ?>"></td>
                        <td><input type="number" min="0" name="<?= $campo ?>[repeticiones]" value="<?= htmlspecialchars((string) ($asignado['repeticiones'] ?? '')) ?>"></td>
                        <td><input type="number" min="0" name="<?= $campo ?>[orden]" value="<?= htmlspecialchars((string) ($asignado['orden'] ?? '')) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-secondary" href="index.php?controller=rutina&action=listar">Cancelar</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
