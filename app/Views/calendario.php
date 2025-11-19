<h2>Calendario de trayectos</h2>
<p>Reservas ordenadas por fecha y hora (vista: <?= htmlspecialchars($vista) ?>).</p>

<form method="get" action="/index.php" class="calendar-filters">
  <input type="hidden" name="page" value="calendario">

  <label>
    Fecha base
    <input type="date" name="fecha" value="<?= htmlspecialchars($fechaBase) ?>">
  </label>

  <label>
    Vista
    <select name="vista">
      <option value="dia"    <?= $vista==='dia'    ? 'selected' : '' ?>>Día</option>
      <option value="semana" <?= $vista==='semana' ? 'selected' : '' ?>>Semana</option>
      <option value="mes"    <?= $vista==='mes'    ? 'selected' : '' ?>>Mes</option>
    </select>
  </label>

  <button type="submit">Ver</button>
</form>

<!-- Leyenda de colores por tipo -->
<div class="calendar-legend">
  <span class="legend-item">
    <span class="legend-color event-tipo-1"></span> Aeropuerto → Hotel
  </span>
  <span class="legend-item">
    <span class="legend-color event-tipo-2"></span> Hotel → Aeropuerto
  </span>
  <span class="legend-item">
    <span class="legend-color event-tipo-3"></span> Ida y vuelta
  </span>
</div>

<div class="calendar-grid">
  <?php foreach ($reservas as $r): ?>
    <?php
      $fecha = $r['fecha_entrada'];
      $hora  = substr($r['hora_entrada'], 0, 5);
      $tipoId = (int)$r['id_tipo_reserva'];   // 1,2,3
      $clase  = 'event-tipo-' . $tipoId;      // para el color
    ?>
    <div class="calendar-event <?= $clase ?>">
      <strong><?= htmlspecialchars($fecha) ?> <?= htmlspecialchars($hora) ?></strong><br>
      <?= htmlspecialchars($r['tipo']) ?> ·
      <?= htmlspecialchars($r['hotel'] ?? '') ?><br>
      Cliente: <?= htmlspecialchars($r['email_cliente']) ?><br>
      Localizador: <?= htmlspecialchars($r['localizador']) ?>
    </div>
  <?php endforeach; ?>

  <?php if (empty($reservas)): ?>
    <p>No hay reservas en el rango seleccionado.</p>
  <?php endif; ?>
</div>

<h3>Listado detallado</h3>
<table class="table table-striped">
  <thead>
    <tr>
      <th>Fecha y hora trayecto</th>
      <th>Localizador</th>
      <th>Email cliente</th>
      <th>Tipo de reserva</th>
      <th>Hotel destino</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($reservas as $r): ?>
      <tr>
        <td>
          <?= htmlspecialchars($r['fecha_entrada']) ?>
          <?= htmlspecialchars(substr($r['hora_entrada'], 0, 5)) ?>
        </td>
        <td><?= htmlspecialchars($r['localizador']) ?></td>
        <td><?= htmlspecialchars($r['email_cliente']) ?></td>
        <td><?= htmlspecialchars($r['tipo']) ?></td>
        <td><?= htmlspecialchars($r['hotel'] ?? '') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
