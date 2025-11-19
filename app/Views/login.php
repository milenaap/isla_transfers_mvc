<div class="form-wrapper">
  <div class="form-card">
    <h2>Iniciar sesión</h2>
    <p class="form-subtitle">
      Introduce tu email para acceder a tu cuenta
    </p>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
      </div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form action="/index.php?page=login_submit" method="post">
      <label>Email
        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
      </label>

      <label>Contraseña
        <input type="password" name="password" required>
      </label>

      <button type="submit">Iniciar sesión</button>

      <div class="form-extra">
        ¿Aún no estás registrado?
        <a href="/index.php?page=register">Regístrate</a>
      </div>
    </form>
  </div>
</div>
