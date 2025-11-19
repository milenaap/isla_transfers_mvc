<?php
$isEdit = !empty($hotel);
$title  = $isEdit ? 'Editar hotel' : 'Nuevo hotel';
?>

<h2><?= htmlspecialchars($title) ?></h2>

<?php if (!empty($_SESSION['hotel_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['hotel_error']) ?>
  </div>
  <?php unset($_SESSION['hotel_error']); ?>
<?php endif; ?>

<form action="/index.php?page=hotel_save" method="post" class="form-card">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id_hotel" value="<?= (int)$hotel['id_hotel'] ?>">
  <?php endif; ?>

  <label>Nombre del hotel
    <input
      type="text"
      name="nombre"
      required
      value="<?= $isEdit ? htmlspecialchars($hotel['nombre']) : '' ?>"
    >
  </label>

  <label>ID Zona
    <input
      type="number"
      name="id_zona"
      value="<?= $isEdit ? (int)$hotel['id_zona'] : '' ?>"
    >
  </label>

  <label>Comisión (%)
    <input
      type="number"
      name="comision"
      value="<?= $isEdit ? (int)$hotel['Comision'] : '' ?>"
    >
  </label>

  <label>Email del hotel
    <input
      type="email"
      name="email_hotel"
      required
      value="<?= $isEdit ? htmlspecialchars($hotel['email_hotel']) : '' ?>"
    >
  </label>

  <label>Contraseña (solo si quieres cambiarla)
    <input
      type="password"
      name="password"
      placeholder="<?= $isEdit ? 'Dejar vacío para mantener' : '' ?>"
    >
  </label>

  <button type="submit">
    <?= $isEdit ? 'Guardar cambios' : 'Crear hotel' ?>
  </button>
</form>
