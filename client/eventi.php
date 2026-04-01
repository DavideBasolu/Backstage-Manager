<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventi - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Eventi</h2>
        <a href="inserisci_evento.php" class="btn btn-dark">Nuovo evento</a>
    </div>

    <?php
    $query = "
        SELECT
            e.id_evento,
            e.nome_evento,
            e.data_evento,
            e.luogo,
            e.cachet,
            a.nome_arte AS artista_principale,
            s.nome_scaletta,
            GROUP_CONCAT(DISTINCT ao.nome_arte ORDER BY ao.nome_arte SEPARATOR ', ') AS ospiti
        FROM eventi e
        JOIN artisti a ON e.id_artista = a.id_artista
        LEFT JOIN scalette s ON e.id_scaletta = s.id_scaletta
        LEFT JOIN eventi_artisti ea ON e.id_evento = ea.id_evento
        LEFT JOIN artisti ao ON ea.id_artista = ao.id_artista
        GROUP BY
            e.id_evento,
            e.nome_evento,
            e.data_evento,
            e.luogo,
            e.cachet,
            a.nome_arte,
            s.nome_scaletta
        ORDER BY e.data_evento DESC, e.id_evento DESC
    ";

    $result = $connessione->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<div class='row g-4'>";

        while ($row = $result->fetch_assoc()) {
            echo "<div class='col-12'>";
            echo "<div class='card shadow-sm'>";
            echo "<div class='card-body'>";

            echo "<div class='d-flex justify-content-between align-items-start flex-column flex-lg-row gap-3'>";

            echo "<div>";
            echo "<h4 class='mb-1'>" . htmlspecialchars($row["nome_evento"]) . "</h4>";
            echo "<p class='text-muted mb-2'>Artista principale: " . htmlspecialchars($row["artista_principale"]) . "</p>";

            echo "<div class='d-flex flex-wrap gap-2 mb-3'>";
            echo "<span class='badge bg-dark'>" . date("d/m/Y", strtotime($row["data_evento"])) . "</span>";
            echo "<span class='badge bg-secondary'>" . htmlspecialchars($row["luogo"]) . "</span>";

            if (!empty($row["cachet"])) {
                echo "<span class='badge bg-success'>Cachet: € " . htmlspecialchars($row["cachet"]) . "</span>";
            }

            if (!empty($row["nome_scaletta"])) {
                echo "<span class='badge bg-primary'>Scaletta: " . htmlspecialchars($row["nome_scaletta"]) . "</span>";
            } else {
                echo "<span class='badge bg-light text-dark border'>Nessuna scaletta</span>";
            }

            echo "</div>";

            if (!empty($row["ospiti"])) {
                echo "<p class='mb-0'><strong>Ospiti:</strong> " . htmlspecialchars($row["ospiti"]) . "</p>";
            } else {
                echo "<p class='mb-0'><strong>Ospiti:</strong> Nessuno</p>";
            }

            echo "</div>";

            echo "</div>";

            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>Nessun evento trovato.</div>";
    }
    ?>
</div>

</body>
</html>