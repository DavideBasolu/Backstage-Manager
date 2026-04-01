<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brani - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Lista Brani</h2>

            <?php
            $query = "SELECT 
                        b.id_brano,
                        b.titolo,
                        b.durata,
                        b.bpm,
                        b.tonalita,
                        b.anno_pubblicazione,
                        b.genere_brano,
                        b.stato_brano,
                        b.note,
                        a.nome_arte AS artista_principale,
                        GROUP_CONCAT(af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
                      FROM brani b
                      JOIN artisti a ON b.id_artista = a.id_artista
                      LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
                      LEFT JOIN artisti af ON fb.id_artista = af.id_artista
                      GROUP BY 
                        b.id_brano,
                        b.titolo,
                        b.durata,
                        b.bpm,
                        b.tonalita,
                        b.anno_pubblicazione,
                        b.genere_brano,
                        b.stato_brano,
                        b.note,
                        a.nome_arte
                      ORDER BY b.id_brano DESC";

            $result = $connessione->query($query);

            if ($result && $result->num_rows > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped table-bordered align-middle'>";
                echo "<thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Titolo</th>
                            <th>Artista</th>
                            <th>Feat</th>
                            <th>Durata</th>
                            <th>BPM</th>
                            <th>Tonalità</th>
                            <th>Anno</th>
                            <th>Genere</th>
                            <th>Stato</th>
                            <th>Note</th>
                        </tr>
                      </thead><tbody>";

                while ($row = $result->fetch_assoc()) {
                    $feat = !empty($row['feat']) ? $row['feat'] : '-';
                    $bpm = !empty($row['bpm']) ? $row['bpm'] : '-';
                    $tonalita = !empty($row['tonalita']) ? $row['tonalita'] : '-';
                    $anno = !empty($row['anno_pubblicazione']) ? $row['anno_pubblicazione'] : '-';
                    $genere = !empty($row['genere_brano']) ? $row['genere_brano'] : '-';
                    $note = !empty($row['note']) ? $row['note'] : '-';

                    echo "<tr>
                            <td>{$row['id_brano']}</td>
                            <td>{$row['titolo']}</td>
                            <td>{$row['artista_principale']}</td>
                            <td>{$feat}</td>
                            <td>{$row['durata']}</td>
                            <td>{$bpm}</td>
                            <td>{$tonalita}</td>
                            <td>{$anno}</td>
                            <td>{$genere}</td>
                            <td>{$row['stato_brano']}</td>
                            <td>{$note}</td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<p>Nessun brano trovato.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>