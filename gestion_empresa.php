<?php
include('config.php');
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}
include 'db.php';

$mensaje = "";

// Crear o actualizar empresa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $nombre = trim($_POST['nombre']);
    $identificacion_fiscal = trim($_POST['identificacion_fiscal']);

    if ($accion === 'agregar') {
        $stmt = $conn->prepare("INSERT INTO empresa (nombre, identificacion_fiscal) VALUES (?, ?)");
        $stmt->bind_param('ss', $nombre, $identificacion_fiscal);
        if ($stmt->execute()) {
            $mensaje = "Entreprise ajoutée avec succès.";
        } else {
            $mensaje = "Erreur lors de l'ajout : " . $conn->error;
        }
        $stmt->close();
    } elseif ($accion === 'editar') {
        $empresa_id = intval($_POST['empresa_id']);
        $stmt = $conn->prepare("UPDATE empresa SET nombre=?, identificacion_fiscal=? WHERE id=?");
        $stmt->bind_param('ssi', $nombre, $identificacion_fiscal, $empresa_id);
        if ($stmt->execute()) {
            $mensaje = "Entreprise modifiée avec succès.";
        } else {
            $mensaje = "Erreur lors de la modification : " . $conn->error;
        }
        $stmt->close();
    }
}

// Eliminar empresa
if (isset($_GET['eliminar'])) {
    $empresa_id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM empresa WHERE id=$empresa_id");
    $mensaje = "Entreprise supprimée.";
}

// Listar empresas
$empresas = [];
$sql = "SELECT id, nombre, identificacion_fiscal, created_at FROM empresa ORDER BY nombre";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $empresas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SAP Customer Checkout Manager - Gestion des entreprises</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'SAPicons', sans-serif;
            font-size: 14px;
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
        th, td {
            border: 1px solid #ddd;
            padding: 0.8rem;
            text-align: center;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th {
            background-color: #f2f2f2;
        }
        .content {
            background-color: #fff;
            padding: 1rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                <img src="images/nav-back.png" alt="Retour" style="width: 12px; height: auto; cursor: pointer;">
            </span>
            <img src="images/logo_sap.png" alt="Logo SAP" class="logo" onclick="window.location.href='index.php';" style="width: 80px; margin-left: 5px;" />
        </div>
        <h1 style="font-size: 18px;">SAP Customer Checkout Manager - Gestion des entreprises</h1>
    </div>

    <div class="content">
        <div class="header justify-content-center">
            <h1>Gestion des entreprises</h1>
        </div>

        <div class="mb-4">
            <button class="btn btn-success" data-toggle="modal" data-target="#modalEmpresa" onclick="nuevaEmpresa()">Ajouter une entreprise</button>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Identification fiscale</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresas as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['identificacion_fiscal']) ?></td>
                        <td><?= htmlspecialchars($e['created_at']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalEmpresa"
                                onclick='editarEmpresa(<?= json_encode($e) ?>)'>Éditer</button>
                            <a href="?eliminar=<?= $e['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette entreprise ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter/Éditer -->
<div class="modal fade" id="modalEmpresa" tabindex="-1" role="dialog" aria-labelledby="modalEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="formEmpresa">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmpresaLabel">Entreprise</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="empresa_id" id="empresa_id">
                    <input type="hidden" name="accion" id="accion" value="agregar">
                    <div class="form-group">
                        <label for="nombre">Nom :</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="identificacion_fiscal">Identification fiscale :</label>
                        <input type="text" class="form-control" name="identificacion_fiscal" id="identificacion_fiscal" required>
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
function nuevaEmpresa() {
    $('#modalEmpresaLabel').text('Ajouter une entreprise');
    $('#accion').val('agregar');
    $('#empresa_id').val('');
    $('#nombre').val('');
    $('#identificacion_fiscal').val('');
}
function editarEmpresa(e) {
    $('#modalEmpresaLabel').text('Éditer entreprise');
    $('#accion').val('editar');
    $('#empresa_id').val(e.id);
    $('#nombre').val(e.nombre);
    $('#identificacion_fiscal').val(e.identificacion_fiscal);
}
</script>
</body>
</html>