<?php
// app/views/user_dashboard.php

$user = $user ?? current_user();
$role = $user['role'] ?? 'user';

// Seguridad: si no hay usuario logueado, no debería estar aquí
if (!$user) {
    header('Location: /index.php?page=login');
    exit;
}

$reservas = $reservas ?? [];

// Cálculos de resumen
$totalReservas  = count($reservas);
$futuras48      = 0;
$bloqueadas48   = 0;

$ahora = time();

foreach ($reservas as $r) {
    $fecha   = $r['fecha_entrada'] ?? null;
    $hora    = $r['hora_entrada'] ?? '00:00:00';

    if ($fecha) {
        $ts      = strtotime("$fecha $hora");
        $difHora = ($ts - $ahora) / 3600;

        if ($difHora >= 48) {
            $futuras48++;
        } else {
            $bloqueadas48++;
        }
    }
}

// Título según rol (user u hotel)
$tituloPanel = ($role === 'hotel') ? 'Reservas del hotel' : 'Mis reservas';
?>

<h2><?= htmlspecialchars($tituloPanel) ?></h2>


<?php if ($totalReservas > 0): ?>
  <div class="grid">
    <div class="card stat">
      <span class="stat-label">Total reservas</span>
      <span class="stat-value"><?= $totalReservas ?></span>
    </div>

    <div class="card stat">
      <span class="stat-label">Reservas modificables (&gt; 48h)</span>
      <span class="stat-value"><?= $futuras48 ?></span>
    </div>

    <div class="card stat">
      <span class="stat-label">Bloqueadas (&lt; 48h)</span>
      <span class="stat-value"><?= $bloqueadas48 ?></span>
    </div>
  </div>
<?php else: ?>
  <p>No tienes reservas todavía. Puedes crear una desde el menú «Nueva reserva».</p>
<?php endif; ?>


<h3 style="margin-top: 2rem;">Listado de reservas</h3>

<?php if (!empty($_SESSION['reserva_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['reserva_error']) ?>
  </div>
  <?php unset($_SESSION['reserva_error']); ?>
<?php endif; ?>

<?php if (empty($reservas)): ?>
  <p>No hay reservas para mostrar.</p>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Hotel destino</th>
        <th>Tipo de reserva</th>
        <th>Localizador</th>
        <th>Creado por</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($reservas as $r): ?>
      <?php
        $fecha   = $r['fecha_entrada'] ?? '';
        $hora    = substr($r['hora_entrada'] ?? '00:00:00', 0, 5);
        $tipo    = $r['tipo'] ?? '';
        $hotel   = $r['hotel'] ?? '';
        $loc     = $r['localizador'] ?? '';
        $idRes   = (int)($r['id_reserva'] ?? 0);

        $ts      = strtotime(($r['fecha_entrada'] ?? '') . ' ' . ($r['hora_entrada'] ?? '00:00:00'));
        $difHora = ($ts - $ahora) / 3600;

        // El usuario particular solo puede editar/borrar si faltan >= 48h
        $puedeModificar = ($role === 'user' && $difHora >= 48);

        // ===== CÁLCULO "CREADO POR" =====
        $creadoRaw = $r['creado_por'] ?? null;
        $creadoPorLabel = '';

        if ($creadoRaw === 'admin') {
            $creadoPorLabel = 'ADMINISTRADOR';
        } elseif ($creadoRaw === 'hotel') {
            $creadoPorLabel = 'HOTEL';
        } elseif ($creadoRaw === 'user') {
            $creadoPorLabel = 'USUARIO (tú)';
        } else {
            // Fallback por si la columna no existe en alguna reserva antigua
            $emailReserva = $r['email_cliente'] ?? '';
            $emailUser    = $user['email'] ?? '';

            if ($emailReserva === $emailUser) {
                $creadoPorLabel = 'USUARIO (tú)';
            } elseif ($role === 'admin') {
                $creadoPorLabel = 'ADMINISTRADOR';
            } elseif ($role === 'hotel') {
                $creadoPorLabel = 'HOTEL';
            } else {
                $creadoPorLabel = 'ADMIN / HOTEL';
            }
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($fecha) ?></td>
        <td><?= htmlspecialchars($hora) ?></td>
        <td><?= htmlspecialchars($hotel) ?></td>
        <td><?= htmlspecialchars($tipo) ?></td>
        <td><?= htmlspecialchars($loc) ?></td>
        <td><?= htmlspecialchars($creadoPorLabel) ?></td>
        <td class="table-actions">
          <?php if ($role === 'user'): ?>
            <?php if ($puedeModificar): ?>
              <!-- EDITAR -->
              <a
                href="/index.php?page=reserva_edit&id=<?= $idRes ?>"
                class="action-icon edit"
                title="Editar reserva"
              >
                <!-- mismo icono lápiz que admin -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                  <path fill="none" stroke="currentColor" stroke-width="2" d="m14 4l6 6zm8.294 1.294c.39.39.387 1.025-.008 1.42L9 20l-7 2l2-7L17.286 1.714a1 1 0 0 1 1.42-.008zM3 19l2 2m2-4l8-8"/>
                </svg>
              </a>

              <!-- ELIMINAR -->
              <a
                href="/index.php?page=reserva_delete&id=<?= $idRes ?>"
                class="action-icon delete"
                title="Cancelar reserva"
                onclick="return confirm('¿Seguro que deseas cancelar esta reserva?');"
              >
                <!-- mismo icono papelera que admin -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                  <path fill="currentColor" d="M7 3h2a1 1 0 0 0-2 0M6 3a2 2 0 1 1 4 0h4a.5.5 0 0 1 0 1h-.564l-1.205 8.838A2.5 2.5 0 0 1 9.754 15H6.246a2.5 2.5 0 0 1-2.477-2.162L2.564 4H2a.5.5 0 0 1 0-1zm1 3.5a.5.5 0 0 0-1 0v5a.5.5 0 0 0 1 0zM9.5 6a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5m-4.74 6.703A1.5 1.5 0 0 0 6.246 14h3.508a1.5 1.5 0 0 0 1.487-1.297L12.427 4H3.573z"/>
                </svg>
              </a>
            <?php else: ?>
              <span class="badge badge-muted" title="No se puede modificar/cancelar (menos de 48h)">
                &lt; 48h
              </span>
            <?php endif; ?>
          <?php else: ?>
            <!-- Para hotel, si quieres no permitir acciones aquí -->
            <span class="badge badge-muted">Solo lectura</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
