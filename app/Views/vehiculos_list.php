<h2>Vehículos</h2>

<a href="/index.php?page=vehiculo_form" class="nav-btn nav-btn-success">
  Añadir vehículo
</a>

<table class="table">
  <thead>
    <tr>
      <th>Descripción</th>
      <th>Email conductor</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($vehiculos as $v): ?>
      <tr>
        <td><?= htmlspecialchars($v['descripcion']) ?></td>
        <td><?= htmlspecialchars($v['email_conductor']) ?></td>
        <td class="table-actions">
  <!-- EDITAR -->
  <a 
    href="/index.php?page=vehiculo_form&id=<?= (int)$v['id_vehiculo'] ?>" 
    class="action-icon edit" 
    title="Editar vehículo"
  >
    <!-- icono lápiz -->
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
      <path fill="none" stroke="currentColor" stroke-width="2" d="m14 4l6 6zm8.294 1.294c.39.39.387 1.025-.008 1.42L9 20l-7 2l2-7L17.286 1.714a1 1 0 0 1 1.42-.008zM3 19l2 2m2-4l8-8"/>
    </svg>
  </a>

  <!-- ELIMINAR -->
  <a
    href="/index.php?page=vehiculo_delete&id=<?= (int)$v['id_vehiculo'] ?>"
    class="action-icon delete"
    title="Eliminar vehículo"
    onclick="return confirm('¿Eliminar vehículo?');"
  >
    <!-- icono papelera -->
    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26">
      <path fill="currentColor" d="M11.5-.031c-1.958 0-3.531 1.627-3.531 3.594V4H4c-.551 0-1 .449-1 1v1H2v2h2v15c0 1.645 1.355 3 3 3h12c1.645 0 3-1.355 3-3V8h2V6h-1V5c0-.551-.449-1-1-1h-3.969v-.438c0-1.966-1.573-3.593-3.531-3.593zm0 2.062h3c.804 0 1.469.656 1.469 1.531V4H10.03v-.438c0-.875.665-1.53 1.469-1.53zM6 8h5.125c.124.013.247.031.375.031h3c.128 0 .25-.018.375-.031H20v15c0 .563-.437 1-1 1H7c-.563 0-1-.437-1-1zm2 2v12h2V10zm4 0v12h2V10zm4 0v12h2V10z"/>
    </svg>
  </a>
</td>


      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
