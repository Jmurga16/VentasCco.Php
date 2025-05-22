<?php
session_start(); // Inicia la sesi車n
session_unset(); // Elimina todas las variables de sesi車n
session_destroy(); // Destruye la sesi車n
setcookie(session_name(), '', time() - 3600, '/'); // Elimina la cookie de sesi車n
header('Location: login.php'); // Redirige al formulario de login
exit;
?>
