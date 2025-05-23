<?php
include('config.php');
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}
include 'db.php';

// Obtener roles para el select
$roles = [];
$roles_result = $conn->query("SELECT id, nombre FROM rol ORDER BY nombre");
while ($row = $roles_result->fetch_assoc()) {
    $roles[] = $row;
}

// Acciones CRUD
$empresa_id = $_SESSION['empresa_id'];
$mensaje = "";

// Crear o actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $nombre = trim($_POST['nombre']);
    $rol_id = intval($_POST['rol_id']);
    $email = trim($_POST['email']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($accion === 'agregar') {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("INSERT INTO usuario (empresa_id, rol_id, nombre, activo) VALUES ($empresa_id, $rol_id, '$nombre', $activo)");
        $usuario_id = $conn->insert_id;
        $conn->query("INSERT INTO login (usuario_id, email, password) VALUES ($usuario_id, '$email', '$password')");
        $mensaje = "Utilisateur ajouté avec succès.";
    } elseif ($accion === 'editar') {
        $usuario_id = intval($_POST['usuario_id']);
        $conn->query("UPDATE usuario SET nombre='$nombre', rol_id=$rol_id, activo=$activo WHERE id=$usuario_id AND empresa_id=$empresa_id");
        $conn->query("UPDATE login SET email='$email' WHERE usuario_id=$usuario_id");
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $conn->query("UPDATE login SET password='$password' WHERE usuario_id=$usuario_id");
        }
        $mensaje = "Utilisateur modifié avec succès.";
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $usuario_id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM login WHERE usuario_id=$usuario_id");
    $conn->query("DELETE FROM usuario WHERE id=$usuario_id AND empresa_id=$empresa_id");
    $mensaje = "Utilisateur supprimé.";
}

// Listar usuarios de la empresa actual
$usuarios = [];
$sql = "SELECT u.id, u.nombre, u.activo, u.rol_id, r.nombre AS rol, l.email 
        FROM usuario u
        JOIN rol r ON u.rol_id = r.id
        JOIN login l ON l.usuario_id = u.id
        WHERE u.empresa_id = $empresa_id
        ORDER BY u.nombre";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>SAP Customer Checkout Manager - Gestion des utilisateurs</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'SAPicons', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background-color: #F7F7F7;
        }

        .header {

            color: #333;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-button {
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .back-button:hover {
            color: #fff;
            text-decoration: underline;
        }

        h1 {
            align-items: center;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1rem;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 0.8rem;
            text-align: left;
            background-color: #fff;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #f2f2f2;
        }

        .content {
            background-color: #fff;
            padding: 1rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #3f51b5;
            color: #fff;
            
        }


        .btn {
            margin-right: 5px;
            
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div style="display: flex; align-items: center;">
                <span class="back-button" onclick="window.location.href='index.php';">
                    <img src="images/nav-back.png" alt="Atrás" style="width: 12px; height: auto; cursor: pointer;">
                </span>

                <img src="images/logo_sap.png" alt="Logo SAP" class="logo" onclick="window.location.href='index.php';" style="width: 80px; margin-left: 5px;" /> <!-- Reducción del tamaño del logo -->
            </div>
            <h1 style="font-size: 18px;">SAP Customer Checkout Manager - Gestion des utilisateurs</h1>
        </div>

        <div class="content">


            <div class="header justify-content-center">
                <h1>Gestion des utilisateurs</h1>
            </div>

            <div class="mb-4">
                <button class="btn btn-success" data-toggle="modal" data-target="#modalUsuario" onclick="nuevoUsuario()">Ajouter un utilisateur</button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actif</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['rol']) ?></td>
                            <td><?= $u['activo'] ? 'Oui' : 'Non' ?></td>
                            <td>
                                <?php if ($u['id'] == 1): ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Éditer</button>
                                    <button class="btn btn-secondary btn-sm" disabled>Supprimer</button>
                                <?php else: ?>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalUsuario"
                                        onclick='editarUsuario(<?= json_encode($u) ?>)'>Éditer</button>
                                    <a href="?eliminar=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Modal Ajouter/Éditer -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" id="formUsuario">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUsuarioLabel">Utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="usuario_id" id="usuario_id">
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <div class="form-group">
                            <label for="nombre">Nom :</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="rol_id">Rôle :</label>
                            <select class="form-control" name="rol_id" id="rol_id" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe :</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <small id="passwordHelp" class="form-text text-muted">Laissez vide pour ne pas changer (édition).</small>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="activo" id="activo" checked>
                            <label class="form-check-label" for="activo">Actif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function nuevoUsuario() {
            $('#modalUsuarioLabel').text('Ajouter un utilisateur');
            $('#accion').val('agregar');
            $('#usuario_id').val('');
            $('#nombre').val('');
            $('#email').val('');
            $('#rol_id').val('');
            $('#password').val('');
            $('#activo').prop('checked', true);
            $('#passwordHelp').show();
        }

        function editarUsuario(u) {
            $('#modalUsuarioLabel').text('Éditer utilisateur');
            $('#accion').val('editar');
            $('#usuario_id').val(u.id);
            $('#nombre').val(u.nombre);
            $('#email').val(u.email);
            $('#rol_id').val(u.rol_id || '');
            $('#password').val('');
            $('#activo').prop('checked', u.activo == 1);
            $('#passwordHelp').show();
        }
    </script>
</body>

</html>