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

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Scalette</h2>
        <a href="inserisci_scaletta.php" class="btn btn-dark">Nuova scaletta</a>
    </div>

    <?php
    $query_scalette = "
        SELECT 
            s.id_scaletta,
            s.nome_scaletta,
            s.descrizione,
            s.durata_totale,
            s.note,
            a.nome_arte
        FROM scalette s
        JOIN artisti a ON s.id_artista = a.id_artista
        ORDER BY s.id_scaletta DESC
    ";

    $result_scalette = $connessione->query($query_scalette);

    if ($result_scalette && $result_scalette->num_rows > 0) {
        while ($scaletta = $result_scalette->fetch_assoc()) {
            $id_scaletta = (int)$scaletta["id_scaletta"];
            ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                        <div>
                            <h4 class="mb-1"><?php echo htmlspecialchars($scaletta["nome_scaletta"]); ?></h4>
                            <p class="text-muted mb-2">
                                Artista: <?php echo htmlspecialchars($scaletta["nome_arte"]); ?>
                            </p>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-dark">
                                    ID <?php echo $id_scaletta; ?>
                                </span>

                                <span class="badge bg-secondary">
                                    Durata totale: 
                                    <?php echo !empty($scaletta["durata_totale"]) ? htmlspecialchars($scaletta["durata_totale"]) : "n/d"; ?>
                                </span>
                            </div>

                            <?php if (!empty($scaletta["descrizione"])) { ?>
                                <p class="mb-2">
                                    <strong>Descrizione:</strong>
                                    <?php echo htmlspecialchars($scaletta["descrizione"]); ?>
                                </p>
                            <?php } ?>

                            <?php if (!empty($scaletta["note"])) { ?>
                                <p class="mb-0">
                                    <strong>Note:</strong>
                                    <?php echo htmlspecialchars($scaletta["note"]); ?>
                                </p>
                            <?php } ?>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <a href="aggiungi_brani_scaletta.php?id_scaletta=<?php echo $id_scaletta; ?>" class="btn btn-dark">
                                Gestisci scaletta
                            </a>

                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#tracklist<?php echo $id_scaletta; ?>">
                                Visualizza brani
                            </button>
                        </div>
                    </div>

                    <div class="collapse mt-4" id="tracklist<?php echo $id_scaletta; ?>">
                        <?php
                        $query_brani = "
                            SELECT
                                sb.posizione,
                                sb.note,
                                b.titolo,
                                a.nome_arte AS artista_principale,
                                GROUP_CONCAT(DISTINCT af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
                            FROM scaletta_brani sb
                            JOIN brani b ON sb.id_brano = b.id_brano
                            JOIN artisti a ON b.id_artista = a.id_artista
                            LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
                            LEFT JOIN artisti af ON fb.id_artista = af.id_artista
                            WHERE sb.id_scaletta = $id_scaletta
                            GROUP BY
                                sb.posizione,
                                sb.note,
                                b.titolo,
                                a.nome_arte
                            ORDER BY sb.posizione ASC
                        ";

                        $result_brani = $connessione->query($query_brani);

                        if ($result_brani && $result_brani->num_rows > 0) {
                            echo "<ol class='list-group list-group-numbered'>";

                            while ($brano = $result_brani->fetch_assoc()) {
                                echo "<li class='list-group-item'>";

                                echo "<div class='d-flex justify-content-between align-items-start'>";

                                echo "<div>";
                                echo "<strong>" . htmlspecialchars($brano['titolo']) . "</strong><br>";
                                echo "<small class='text-muted'>Artista: " . htmlspecialchars($brano['artista_principale']);

                                if (!empty($brano['feat'])) {
                                    echo " feat. " . htmlspecialchars($brano['feat']);
                                }

                                echo "</small>";

                                if (!empty($brano["note"])) {
                                    echo "<br><small class='text-secondary'>Note: " . htmlspecialchars($brano["note"]) . "</small>";
                                }

                                echo "</div>";

                                echo "<span class='badge bg-dark rounded-pill'>Pos. " . (int)$brano['posizione'] . "</span>";

                                echo "</div>";

                                echo "</li>";
                            }

                            echo "</ol>";
                        } else {
                            echo "<div class='alert alert-warning mb-0'>Nessun brano nella scaletta.</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
    } else {
        echo "<div class='alert alert-info'>Nessuna scaletta trovata.</div>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>