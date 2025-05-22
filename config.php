<?php

session_start(); // Inicia la sesión

// Define la contraseña correcta (asegurémonos de que esta contraseña esté definida de manera adecuada)
define('PASSWORD_HASH', password_hash('NUMEROUNO123', PASSWORD_DEFAULT)); // Hashea la contraseña

// Define el tiempo máximo de inactividad (en segundos)
$inactive_time = 60; // 1 minuto

// Configura la duración de la cookie de sesión
session_set_cookie_params($inactive_time, "/"); // Establece el tiempo de vida de la cookie en el navegador (60 segundos)

// Configura el manejo de sesiones en tiempo de ejecución
ini_set('session.gc_maxlifetime', $inactive_time); // Tiempo de vida de la sesión en el servidor (60 segundos)

// Inicia la sesión
session_start();

// Verifica si la sesión ha expirado por inactividad
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $inactive_time) {
    // Si ha pasado más de 1 minuto desde la última actividad, destruye la sesión
    session_unset(); // Elimina todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header('Location: login.php'); // Redirige al formulario de login
    exit;
}

// Actualiza la hora de la última actividad
$_SESSION['LAST_ACTIVITY'] = time(); // Establece la última actividad en el tiempo actual
?>
