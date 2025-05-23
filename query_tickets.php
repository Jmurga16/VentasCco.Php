<?php

include('config.php'); // Incluir config.php para gestionar la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Si no está autenticado, redirige al formulario de login
    header('Location: login.php');
    exit;
}

// Incluir archivo de conexión
include 'db.php';

// Inicializar variables de filtro y paginación
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$pos_group = isset($_GET['pos_group']) ? $_GET['pos_group'] : '';
$items_per_page = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 10;
$page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
$offset = ($page_number - 1) * $items_per_page;

// Consulta SQL principal con paginación
$sql = "SELECT ID, Date, Time, `POS group`, `Total net revenue`, `Total VAT`, `Total due amount` 
        FROM tickets_ventas WHERE 1=1";

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND DATE(Date) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

if (!empty($pos_group)) {
    $sql .= " AND `POS group` = '$pos_group'";
}

$sql .= " ORDER BY Date LIMIT $items_per_page OFFSET $offset";

// Ejecutar la consulta principal
$result = $conn->query($sql);

// Obtener opciones únicas para el desplegable de POS_group
$pos_options = [];
$pos_query = "SELECT DISTINCT `POS group` FROM tickets_ventas WHERE `POS group` IS NOT NULL";
$pos_result = $conn->query($pos_query);

if ($pos_result && $pos_result->num_rows > 0) {
    while ($row = $pos_result->fetch_assoc()) {
        $pos_options[] = $row['POS group'];
    }
}

// Consulta para contar total de registros para la paginación
$count_sql = "SELECT COUNT(*) AS total FROM tickets_ventas WHERE 1=1";

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $count_sql .= " AND DATE(Date) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

if (!empty($pos_group)) {
    $count_sql .= " AND `POS group` = '$pos_group'";
}

$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $items_per_page);

// Calcular rango de páginas a mostrar (resumen)
$range = 2; // Número de páginas a mostrar antes y después de la página activa
$start_page = max(1, $page_number - $range);
$end_page = min($total_pages, $page_number + $range);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <title>SAP Customer Checkout Manager - Consulter les tickets de ventes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Estilos base */
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'SAPicons', sans-serif;
            /* Utiliza la fuente SAPicons si está disponible */
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background-color: #F7F7F7;
        }

        /* Cabecera */
        .header {
            background-color: #F7F7F7;
            color: #333;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Botón de retroceso */
        .back-button {
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .back-button:hover {
            color: #fff;
            /* Mantén el color blanco al pasar el ratón */
            text-decoration: underline;
        }

        /* Tabla */
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

        /* Formulario */
        .filters {
            background-color: #fff;
            padding: 1rem;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Botón */
        .btn {
            background-color: #3f51b5;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Paginación */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        .page-item .page-link {
            color: #333;
            border: 1px solid #ddd;
            padding: 0.5rem 0.8rem;
            margin-left: 0.5rem;
        }

        .page-item.active .page-link {
            background-color: #3f51b5;
            color: #fff;
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
            <h1 style="font-size: 18px;">SAP Customer Checkout Manager - Consulter les reçus</h1>
        </div>

        <!-- Formulario de Filtro -->
        <form method="GET" action="query_tickets.php" class="filters row">
            <div class="form-group col-md-3">
                <label for="fecha_inicio">Date de début :</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio) ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_fin">Date de fin :</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="pos_group">Groupe POS :</label>
                <select id="pos_group" name="pos_group" class="form-control">
                    <option value="">Sélectionnez une option</option>
                    <?php foreach ($pos_options as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= $pos_group === $option ? 'selected' : '' ?>>
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="items_per_page">Afficher par page :</label>
                <select id="items_per_page" name="items_per_page" class="form-control">
                    <option value="10" <?= $items_per_page === 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $items_per_page === 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= $items_per_page === 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-4 col-md-12">Filtrer</button>
        </form>

        <!-- Resúmen -->
        <h3 class="my-4">Reçus (<?= $total_rows ?>)</h3>

        <!-- Resultados de la Consulta -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID du reçu</th>
                    <th>Date de la transaction commerciale</th>
                    <th>Groupe POS</th>
                    <th>Total net revenue</th>
                    <th>Total TVA</th>
                    <th>Montant brut de paiement</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ID']) ?></td>
                            <td><?= htmlspecialchars($row['Date'] . ' ' . $row['Time']) ?></td>
                            <td><?= htmlspecialchars($row['POS group']) ?></td>
                            <td><?= htmlspecialchars($row['Total net revenue']) ?></td>
                            <td><?= htmlspecialchars($row['Total VAT']) ?></td>
                            <td><?= htmlspecialchars($row['Total due amount']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Aucun reçu trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Botón de descarga de archivo Excel -->
        <form method="GET" action="download_excel.php">
            <input type="hidden" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
            <input type="hidden" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
            <input type="hidden" name="pos_group" value="<?= htmlspecialchars($pos_group) ?>">
            <input type="hidden" name="items_per_page" value="<?= $items_per_page ?>">
            <button type="submit" class="btn btn-success mt-4">Télécharger le fichier Excel</button>
        </form>

        <!-- Paginación -->
        <nav aria-label="Page navigation" class="pagination mt-4">
            <ul class="pagination">
                <?php if ($page_number > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page_number=1&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&pos_group=<?= $pos_group ?>&items_per_page=<?= $items_per_page ?>">Première</a></li>
                    <li class="page-item"><a class="page-link" href="?page_number=<?= $page_number - 1 ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&pos_group=<?= $pos_group ?>&items_per_page=<?= $items_per_page ?>">Précédente</a></li>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= $page_number === $i ? 'active' : '' ?>"><a class="page-link" href="?page_number=<?= $i ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&pos_group=<?= $pos_group ?>&items_per_page=<?= $items_per_page ?>"><?= $i ?></a></li>
                <?php endfor; ?>

                <?php if ($page_number < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page_number=<?= $page_number + 1 ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&pos_group=<?= $pos_group ?>&items_per_page=<?= $items_per_page ?>">Suivante</a></li>
                    <li class="page-item"><a class="page-link" href="?page_number=<?= $total_pages ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>&pos_group=<?= $pos_group ?>&items_per_page=<?= $items_per_page ?>">Dernière</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>