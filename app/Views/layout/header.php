<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Isla Transfers</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<?php
  $logged  = is_logged_in();
  $isAdmin = is_admin();
  $isUser  = is_user();
  $isHotel = is_hotel();
  $user    = $logged ? current_user() : null;
?>
<header class="topbar">
  <div class="topbar-inner">
    <h1 class="logo">Isla Transfers</h1>

    <nav class="main-nav">

      <?php if (!$logged): ?>

        <a href="/index.php?page=login" class="nav-btn nav-btn-outline">Iniciar sesión</a>
        <a href="/index.php?page=register" class="btn-primary">Registrarse</a>

      <?php else: ?>

        <?php if ($isAdmin): ?>

          <a href="/index.php?page=admin" class="nav-link">Panel admin</a>
          <a href="/index.php?page=reserva_form" class="nav-btn-green">Crear reserva</a>
          <a href="/index.php?page=reservas_admin" class="nav-link">Reservas</a>
          <a href="/index.php?page=vehiculos" class="nav-link">Vehículos</a>
          <a href="/index.php?page=hoteles" class="nav-link">Destinos / Hoteles</a>
          <a href="/index.php?page=calendario" class="nav-link">Calendario</a>

        <?php elseif ($isUser): ?>
          <a href="/index.php?page=admin" class="nav-link">Panel principal</a>
          <a href="/index.php?page=reservas_user" class="nav-link">Reservas</a>
          <a href="/index.php?page=reserva_form" class="nav-btn-green">Crear reserva</a>
          <a href="/index.php?page=calendario" class="nav-link">Calendario</a>

        <?php elseif ($isHotel): ?>

          <a href="/index.php?page=user" class="nav-link">Reservas hotel</a>
          <a href="/index.php?page=reserva_form" class="nav-btn-green">Crear reserva</a>
          <a href="/index.php?page=calendario" class="nav-link">Calendario</a>

        <?php endif; ?>

        <a href="/index.php?page=perfil" class="nav-link">Perfil</a>
        <a href="/index.php?page=logout" class="btn-primary">
          Salir (<?= htmlspecialchars($user['name']) ?>)
        </a>

      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
