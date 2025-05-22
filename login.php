<?php
session_start(); // Inicia la sesión
include('config.php'); // Incluye config.php para acceder a PASSWORD_HASH

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userPassword = $_POST['password']; // Captura la contraseña ingresada

    // Verifica si la contraseña ingresada coincide con el hash almacenado
    if (password_verify($userPassword, PASSWORD_HASH)) {
        // Contraseña correcta: guarda la sesión y redirige
        $_SESSION['authenticated'] = true;
        header('Location: index.php'); // Redirige a index.php
        exit;
    } else {
        // Contraseña incorrecta: muestra un mensaje de error
        echo '<p style="color: red;">Mot de passe incorrect. Veuillez réessayer.</p>';
    }
}

// Verifica si ya está autenticado, redirige a index.php si lo está
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php'); // Si ya está autenticado, redirige al índice
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAP CUSTOMER CHECK OUT MANAGER - LOGIN</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'SAPicons', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('images/foto.jpg'); /* Cambia por la ruta de tu imagen */
            background-size: cover;
            background-position: center;
        }
        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.7); /* Fondo semi-transparente */
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            max-width: 450px;
            border: 1px solid #d9d9d9; /* Bordes m谩s definidos */
            line-height: 0.5; /* Reduce la altura de las líneas */
            
        }
         /* Bot贸n */
  .btn {
    background-color: #3f51b5;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
  }

        h1 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }
        a {
            display: block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #3f51b5; /* Azul SAP */
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        a:hover {
            color: #fff; /* Mant茅n el color blanco al pasar el rat贸n */
            text-decoration: underline;
            
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 100px;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.5));
        }
        .footer {
            position: absolute;
            bottom: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <img src="images/logo_sap.png" alt="Logo SAP" class="logo"/> <!-- Cambia por la ruta de tu logo -->
    <div class="container">
        <h1 style="text-align: left;">Customer Checkout Manager</h1>
        
        <p style="text-align: left; font-size: 1em; color: gray;">Se connecter</p>
        
    <form method="POST" action="login.php">
    <div class="form-group">
       
        <div class="row">
            
                <input type="password" id="password" name="password" class="form-control form-control-sm" placeholder="Mot de passe" required>
            
        </div>
    </div>
    <div class="row justify-content-center">
        
            <button type="submit" class="btn btn-success mt-4 col-md-12">Entrer</button>
        
    </div>
</form>
</body>
</html>

