<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scalette - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Scalette</h2>

            <?php
            $query_scalette = "SELECT s.id_scaletta, s.nome_scaletta, s.descrizione, s.durata_totale, s.note, a.nome_arte
                               FROM scalette s
                               JOIN artisti a ON s.id_artista = a.id_artista
                               ORDER BY s.nome_scaletta";

            $result_scalette = $connessione->query($query_scalette);

            if ($result_scalette && $result_scalette->num_rows > 0) {

                while ($scaletta = $result_scalette->fetch_assoc()) {

                    echo "<div class='card mb-4'>";
                    echo "<div class='card-body'>";

                    echo "<h4 class='card-title'>" . $scaletta['nome_scaletta'] . "</h4>";
                    echo "<h6 class='card-subtitle mb-3 text-muted'>Artista: " . $scaletta['nome_arte'] . "</h6>";

                    if (!empty($scaletta['descrizione'])) {
                        echo "<p><strong>Descrizione:</strong> " . $scaletta['descrizione'] . "</p>";
                    }

                    if (!empty($scaletta['durata_totale'])) {
                        echo "<p><strong>Durata totale:</strong> " . $scaletta['durata_totale'] . "</p>";
                    }

                    if (!empty($scaletta['note'])) {
                        echo "<p><strong>Note:</strong> " . $scaletta['note'] . "</p>";
                    }

                    $id_scaletta = (int)$scaletta['id_scaletta'];

                    $query_brani = "SELECT 
                                        sb.posizione,
                                        b.titolo,
                                        ap.nome_arte AS artista_principale,
                                        GROUP_CONCAT(af.nome_arte SEPARATOR ', ') AS feat
                                    FROM scaletta_brani sb
                                    JOIN brani b ON sb.id_brano = b.id_brano
                                    JOIN artisti ap ON b.id_artista = ap.id_artista
                                    LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
                                    LEFT JOIN artisti af ON fb.id_artista = af.id_artista
                                    WHERE sb.id_scaletta = $id_scaletta
                                    GROUP BY sb.posizione, b.id_brano, b.titolo, ap.nome_arte
                                    ORDER BY sb.posizione";

                    $result_brani = $connessione->query($query_brani);

                    echo "<h5 class='mt-4'>Brani</h5>";

                    if ($result_brani && $result_brani->num_rows > 0) {
                        echo "<ol class='list-group list-group-numbered'>";

                        while ($brano = $result_brani->fetch_assoc()) {
                            echo "<li class='list-group-item'>";

                            echo "<div class='d-flex justify-content-between align-items-start'>";
                            echo "<div>";
                            echo "<strong>" . $brano['titolo'] . "</strong><br>";
                            echo "<small class='text-muted'>Artista: " . $brano['artista_principale'];

                            if (!empty($brano['feat'])) {
                                echo " feat. " . $brano['feat'];
                            }

                            echo "</small>";
                            echo "</div>";

                            echo "<span class='badge bg-dark rounded-pill'>Pos. " . $brano['posizione'] . "</span>";
                            echo "</div>";

                            echo "</li>";
                        }

                        echo "</ol>";
                    } else {
                        echo "<p class='text-muted'>Nessun brano inserito in questa scaletta.</p>";
                    }

                    echo "</div>";
                    echo "</div>";
                }

            } else {
                echo "<p>Nessuna scaletta trovata.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>