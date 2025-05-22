
<?php
$servername = "easyvoip.es"; // Cambia por el nombre del servidor de tu base de datos
$username = "hubbyvoip_GN1"; // Cambia por tu nombre de usuario de la base de datos
$password = "Tj{5fy/N3&<9"; // Cambia por tu contrase«Ða de la base de datos
$dbname = "hubbyvoip_GNU"; // Cambia por el nombre de tu base de datos

// Crear conexi«Ñn
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexi«Ñn
if ($conn->connect_error) {
    die("Conexi«Ñn fallida: " . $conn->connect_error);
}
?>
