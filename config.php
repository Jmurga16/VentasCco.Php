<?php

// Define el tiempo máximo de inactividad (en segundos)
$inactive_time = 600; // 10 minutos

// Solo configura los parámetros y llama a session_start() si no hay sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params($inactive_time, "/"); // Establece el tiempo de vida de la cookie en el navegador (60 segundos)
    ini_set('session.gc_maxlifetime', $inactive_time); // Tiempo de vida de la sesión en el servidor (60 segundos)
    session_start();
}

// Verifica si la sesión ha expirado por inactividad
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $inactive_time) {
    session_unset(); // Elimina todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header('Location: login.php'); // Redirige al formulario de login
    exit;
}

// Actualiza la hora de la última actividad
$_SESSION['LAST_ACTIVITY'] = time(); // Establece la última actividad en el tiempo actual
?>
