<?php
// app/core/helpers.php

// Asegurar que la sesión está iniciada UNA sola vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ¿Hay usuario logueado?
function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

// Obtener usuario actual (o null)
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

// ¿Es admin?
function is_admin(): bool
{
    return is_logged_in() && (($_SESSION['user']['role'] ?? null) === 'admin');
}

// ¿Es usuario particular?
function is_user(): bool
{
    return is_logged_in() && (($_SESSION['user']['role'] ?? null) === 'user');
}

// ¿Es hotel?
function is_hotel(): bool
{
    return is_logged_in() && (($_SESSION['user']['role'] ?? null) === 'hotel');
}

// Obligar a estar logueado
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /index.php?page=login');
        exit;
    }
}

// Obligar a ser admin
function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /index.php?page=home');
        exit;
    }
}
