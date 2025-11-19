<?php
$isEdit = !empty($vehiculo);
?>

<h2><?= $isEdit ? 'Editar vehículo' : 'Nuevo vehículo' ?></h2>

<?php if (!empty($_SESSION['vehiculo_error'])): ?>
  <div class="alert alert-error">
    <?= htmlspecialchars($_SESSION['vehiculo_error']) ?>
  </div>
  <?php unset($_SESSION['vehiculo_error']); ?>
<?php endif; ?>

<form action="/index.php?page=vehiculo_save" method="post" class="form-card">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id_vehiculo" value="<?= (int)$vehiculo['id_vehiculo'] ?>">
  <?php endif; ?>

  <label>Descripción
    <input type="text" name="descripcion"
           value="<?= $isEdit ? htmlspecialchars($vehiculo['descripcion']) : '' ?>" required>
  </label>

  <label>Email del conductor
    <input type="email" name="email_conductor"
           value="<?= $isEdit ? htmlspecialchars($vehiculo['email_conductor']) : '' ?>">
  </label>

  <button type="submit"><?= $isEdit ? 'Guardar cambios' : 'Crear vehículo' ?></button>
</form>
