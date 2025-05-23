<?php
session_start();
include('config.php'); // Aquí deberías tener la conexión a la base de datos
include('db.php');

// Si ya está autenticado, redirige
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php');
    exit;
}

// Obtener empresas para el select
$empresas = [];

$result = $conn->query("SELECT id, nombre FROM empresa ORDER BY nombre");
while ($row = $result->fetch_assoc()) {
    $empresas[] = $row;
}

// Procesar el login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa_id = intval($_POST['empresa']);
    $email = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Buscar usuario y login
    $stmt = $conn->prepare(
        "SELECT l.password, u.id as usuario_id, u.nombre, r.nombre as rol
         FROM login l
         JOIN usuario u ON l.usuario_id = u.id
         JOIN rol r ON u.rol_id = r.id
         WHERE l.email = ? AND u.empresa_id = ? AND u.activo = 1"
    );
    $stmt->bind_param('si', $email, $empresa_id);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si se encontró un usuario y empresa
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hash, $usuario_id, $nombre, $rol);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify(trim($password), $hash)) {
            $_SESSION['authenticated'] = true;
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['empresa_id'] = $empresa_id;
            $_SESSION['rol'] = $rol; // <--- Guarda el rol en la sesión
            header('Location: index.php');
            exit;
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur ou entreprise incorrect.";
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - SAP CUSTOMER CHECK OUT MANAGER</title>
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
            background-image: url('images/foto.jpg');
            background-size: cover;
            background-position: center;
        }

        .container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            max-width: 450px;
            border: 1px solid #d9d9d9;
            line-height: 0.5;

        }

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
            background-color: #3f51b5;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        a:hover {
            color: #fff;
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
    <img src="images/logo_sap.png" alt="Logo SAP" class="logo" /> <!-- Cambia por la ruta de tu logo -->
    <div class="container">
        <h1 style="text-align: left;">Customer Checkout Manager</h1>
        <p style="text-align: left; font-size: 1em; color: gray;">Se connecter</p>

        <?php if (!empty($error)): ?>
            <p style="color:red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="empresa">Entreprise</label>
                <select id="empresa" name="empresa" class="form-control" required>
                    <option value="">Sélectionnez une entreprise</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa['id'] ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="usuario">Utilisateur</label>
                <input type="email" id="usuario" name="usuario" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Se connecter</button>
        </form>
    </div>
</body>

</html>