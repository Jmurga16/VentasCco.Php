<?php
include('config.php'); // Incluir config.php para gestionar la sesi®Æn

// Verifica si el usuario est®¢ autenticado
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Si no est®¢ autenticado, redirige al formulario de login
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>Customer Checkout Manager</title>
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
            border: 1px solid #d9d9d9; /* Bordes m√°s definidos */
            
        }
         /* Bot√≥n */
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
            color: #fff; /* Mant√©n el color blanco al pasar el rat√≥n */
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
        <h1>Customer Checkout Manager</h1>
               <button type="submit" class="btn btn-primary mt-4 col-md-12" onclick="window.location.href='query_tickets.php'">Consulter les tickets de ventes</button>
    <button type="submit" class="btn btn-primary mt-2 col-md-12" onclick="window.location.href='sales_report.php'">Rapports de ventes</button>
     
     <form action="logout.php" method="POST">
    <button type="submit" class="btn btn-danger mt-2 col-md-12">Se deconnecter</button>
</form>
         </div>
</body>
</html>
