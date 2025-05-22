<?php
session_start(); // Inicia la sesi��n
session_unset(); // Elimina todas las variables de sesi��n
session_destroy(); // Destruye la sesi��n
setcookie(session_name(), '', time() - 3600, '/'); // Elimina la cookie de sesi��n
header('Location: login.php'); // Redirige al formulario de login
exit;
?>
