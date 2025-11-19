<div class="form-wrapper">
  <div class="form-card">
    <h2>Registrarse</h2>
    <p class="form-subtitle">
      Introduce tus datos para crear una cuenta
    </p>

    <?php if (!empty($_SESSION['register_error'])): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['register_error']) ?>
      </div>
      <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>

    <form action="/index.php?page=register_submit" method="post">
      <!-- Tipo de cliente -->
      <label>Tipo de cliente
        <select name="tipo_cliente" required>
          <option value="">Selecciona una opción</option>
          <option value="particular">Particular</option>
          <option value="hotel">Hotel</option>
        </select>
      </label>

      <!-- DATOS PARTICULARES -->
      <h3 class="form-section-title">Datos personales (particular)</h3>
      <label>Nombre
        <input type="text" name="nombre" placeholder="Nombre" >
      </label>
      <label>Primer apellido
        <input type="text" name="apellido1" placeholder="Primer apellido" >
      </label>
      <label>Segundo apellido
        <input type="text" name="apellido2" placeholder="Segundo apellido">
      </label>
      <label>Dirección
        <input type="text" name="direccion" placeholder="Calle y número">
      </label>
      <label>Código postal
        <input type="text" name="codigoPostal">
      </label>
      <label>Ciudad
        <input type="text" name="ciudad">
      </label>
      <label>País
        <input type="text" name="pais">
      </label>

      <!-- DATOS HOTEL -->
      <h3 class="form-section-title">Datos del hotel (si es corporativo)</h3>
      <label>Nombre hotel
        <input type="text" name="nombre_hotel" placeholder="Nombre comercial del hotel">
      </label>
      <label>Zona (opcional rápido)
        <select name="id_zona">
          <option value="">Selecciona zona (opcional)</option>
          <option value="1">Barcelona</option>
          <option value="2">Tarragona</option>
          <option value="3">Girona</option>
          <option value="4">Lleida</option>
          <option value="5">Zaragoza</option>
        </select>
      </label>
      <label>Comisión (opcional)
        <input type="number" name="comision" min="0" max="100" placeholder="%">
      </label>

      <!-- COMÚN -->
      <h3 class="form-section-title">Acceso</h3>
      <label>Email
        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
      </label>
      <label>Contraseña
        <input type="password" name="password" required>
      </label>

      <button type="submit">Crear cuenta</button>

      <div class="form-extra">
        ¿Ya tienes una cuenta?
        <a href="/index.php?page=login">Inicia sesión</a>
      </div>
    </form>
  </div>
</div>
