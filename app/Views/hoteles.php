<h2>Destinos / Hoteles</h2>

<a href="/index.php?page=hotel_form" class="nav-btn nav-btn-success">
  Añadir hotel
</a>

<?php if (!empty($_SESSION['hotel_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['hotel_error']) ?>
  </div>
  <?php unset($_SESSION['hotel_error']); ?>
<?php endif; ?>

<table class="table">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>ZONA (id_zona)</th>
      <th>Comisión</th>
      <th>Email hotel</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($hoteles as $h): ?>
      <tr>
        <td><?= htmlspecialchars($h['nombre']) ?></td>
        <td><?= htmlspecialchars((string)$h['id_zona']) ?></td>
        <td><?= htmlspecialchars((string)$h['Comision']) ?> %</td>
        <td><?= htmlspecialchars($h['email_hotel']) ?></td>
        <td class="table-actions">
          <!-- EDITAR -->
          <a
            href="/index.php?page=hotel_form&id=<?= (int)$h['id_hotel'] ?>"
            class="action-icon edit"
            title="Editar hotel"
          >
            <!-- mismo icono lápiz que reservas -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
              <path fill="none" stroke="currentColor" stroke-width="2" d="m14 4l6 6zm8.294 1.294c.39.39.387 1.025-.008 1.42L9 20l-7 2l2-7L17.286 1.714a1 1 0 0 1 1.42-.008zM3 19l2 2m2-4l8-8"/>
            </svg>
          </a>

          <!-- ELIMINAR -->
          <a
            href="/index.php?page=hotel_delete&id=<?= (int)$h['id_hotel'] ?>"
            class="action-icon delete"
            title="Eliminar hotel"
            onclick="return confirm('¿Eliminar este hotel?');"
          >
            <!-- mismo icono papelera que reservas -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
              <path fill="currentColor" d="M7 3h2a1 1 0 0 0-2 0M6 3a2 2 0 1 1 4 0h4a.5.5 0 0 1 0 1h-.564l-1.205 8.838A2.5 2.5 0 0 1 9.754 15H6.246a2.5 2.5 0 0 1-2.477-2.162L2.564 4H2a.5.5 0 0 1 0-1zm1 3.5a.5.5 0 0 0-1 0v5a.5.5 0 0 0 1 0zM9.5 6a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5m-4.74 6.703A1.5 1.5 0 0 0 6.246 14h3.508a1.5 1.5 0 0 0 1.487-1.297L12.427 4H3.573z"/>
            </svg>
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
