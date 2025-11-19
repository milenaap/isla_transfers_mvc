<h2>Mi perfil</h2>

<?php if (!empty($_SESSION['perfil_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['perfil_error']) ?>
  </div>
  <?php unset($_SESSION['perfil_error']); ?>
<?php endif; ?>

<form action="/index.php?page=perfil_update" method="post" class="form-card">
  <label>Nombre
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
  </label>
  <label>Email
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
  </label>
  <label>Nueva contraseña (opcional)
    <input type="password" name="password">
  </label>
  <button type="submit">Guardar cambios</button>
</form>
