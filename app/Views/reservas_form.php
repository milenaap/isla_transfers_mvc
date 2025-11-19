<?php
// Modo: crear o editar
$modo    = $modo    ?? 'create';
$reserva = $reserva ?? null;

$isEdit = ($modo === 'edit' && $reserva);
$action = $isEdit ? 'reserva_update' : 'reserva_save';
$btnTxt = $isEdit ? 'Guardar cambios' : 'Guardar reserva';
?>

<h2><?= $isEdit ? 'Editar reserva' : 'Nueva reserva' ?></h2>

<?php if (!empty($_SESSION['reserva_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['reserva_error']) ?>
  </div>
  <?php unset($_SESSION['reserva_error']); ?>
<?php endif; ?>

<form action="/index.php?page=<?= $action ?>" method="post" class="form-card">

  <?php if ($isEdit): ?>
    <input type="hidden" name="id_reserva" value="<?= (int)$reserva['id_reserva'] ?>">
    <input type="hidden" name="localizador" value="<?= htmlspecialchars($reserva['localizador']) ?>">
    <input type="hidden" name="fecha_reserva_original" value="<?= htmlspecialchars($reserva['fecha_reserva']) ?>">
  <?php endif; ?>

  <label>Tipo de reserva
    <select name="id_tipo_reserva" required>
      <option value="">-- Selecciona --</option>
      <?php foreach ($tipos as $t): ?>
        <?php
          $selected = $isEdit && (int)$reserva['id_tipo_reserva'] === (int)$t['id_tipo_reserva']
            ? 'selected'
            : '';
        ?>
        <option value="<?= (int)$t['id_tipo_reserva'] ?>" <?= $selected ?>>
          <?= htmlspecialchars($t['descripcion']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <?php
    $fechaValue = $isEdit ? substr($reserva['fecha_entrada'], 0, 10) : '';
    $horaValue  = $isEdit ? substr($reserva['hora_entrada'], 0, 5)   : '';
  ?>

  <label>Fecha del trayecto
    <input type="date" name="fecha" value="<?= htmlspecialchars($fechaValue) ?>" required>
  </label>

  <label>Hora
    <input type="time" name="hora" value="<?= htmlspecialchars($horaValue) ?>" required>
  </label>

  <label>Hotel destino / recogida
    <select name="id_hotel" required>
      <option value="">-- Selecciona hotel --</option>
      <?php foreach ($hoteles as $h): ?>
        <?php
          $selected = $isEdit && (int)$reserva['id_hotel'] === (int)$h['id_hotel']
            ? 'selected'
            : '';
        ?>
        <option value="<?= (int)$h['id_hotel'] ?>" <?= $selected ?>>
          <?= htmlspecialchars($h['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Vehículo
    <select name="id_vehiculo" required>
      <option value="">-- Selecciona vehículo --</option>
      <?php foreach ($vehiculos as $v): ?>
        <?php
          $selected = $isEdit && (int)$reserva['id_vehiculo'] === (int)$v['id_vehiculo']
            ? 'selected'
            : '';
        ?>
        <option value="<?= (int)$v['id_vehiculo'] ?>" <?= $selected ?>>
          <?= htmlspecialchars($v['descripcion']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>Nº de viajeros
    <input type="number" name="num_viajeros" min="1"
           value="<?= $isEdit ? (int)$reserva['num_viajeros'] : 1 ?>" required>
  </label>

  <label>Email del cliente
    <input type="email" name="email_cliente"
           value="<?= $isEdit ? htmlspecialchars($reserva['email_cliente']) : '' ?>" required>
  </label>

  <label>Número de vuelo
    <input type="text" name="numero_vuelo_entrada"
           value="<?= $isEdit ? htmlspecialchars($reserva['numero_vuelo_entrada']) : '' ?>">
  </label>

  <label>Origen del vuelo
    <input type="text" name="origen_vuelo_entrada"
           value="<?= $isEdit ? htmlspecialchars($reserva['origen_vuelo_entrada']) : '' ?>">
  </label>

  <button type="submit"><?= $btnTxt ?></button>
</form>
