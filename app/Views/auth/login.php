<?php require dirname(__DIR__) . '/layout/header_publico.php'; ?>

<?php $error = $error ?? null; ?>

<h2>Ingreso del instructor</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="index.php?controller=auth&action=login" method="post">
    <label>
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required autofocus>
    </label>
    <label>
        Contraseña
        <input type="password" name="password" required>
    </label>
    <button class="btn btn-primary" type="submit">Ingresar</button>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
