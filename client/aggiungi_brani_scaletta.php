<?php
include("../server/config/connessione.php");

$id_scaletta_selezionata = isset($_GET["id_scaletta"]) ? (int)$_GET["id_scaletta"] : 0;
$id_artista_scaletta = 0;
$nome_scaletta = "";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Brani alla Scaletta - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Aggiungi Brano a una Scaletta</h2>

            <form method="GET" action="aggiungi_brani_scaletta.php" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Seleziona scaletta</label>
                        <select name="id_scaletta" class="form-select" required onchange="this.form.submit()">
                            <option value="">Seleziona scaletta</option>
                            <?php
                            $query_scalette = "SELECT s.id_scaletta, s.nome_scaletta, a.nome_arte
                                               FROM scalette s
                                               JOIN artisti a ON s.id_artista = a.id_artista
                                               ORDER BY s.nome_scaletta";
                            $result_scalette = $connessione->query($query_scalette);

                            while ($row_scaletta = $result_scalette->fetch_assoc()) {
                                $selected = ($id_scaletta_selezionata == $row_scaletta["id_scaletta"]) ? "selected" : "";
                                echo "<option value='{$row_scaletta['id_scaletta']}' $selected>
                                        {$row_scaletta['nome_scaletta']} - {$row_scaletta['nome_arte']}
                                      </option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php
            if ($id_scaletta_selezionata > 0) {
                $query_info_scaletta = "SELECT s.id_artista, s.nome_scaletta, a.nome_arte
                                        FROM scalette s
                                        JOIN artisti a ON s.id_artista = a.id_artista
                                        WHERE s.id_scaletta = $id_scaletta_selezionata";
                $result_info_scaletta = $connessione->query($query_info_scaletta);

                if ($result_info_scaletta && $result_info_scaletta->num_rows > 0) {
                    $info_scaletta = $result_info_scaletta->fetch_assoc();
                    $id_artista_scaletta = (int)$info_scaletta["id_artista"];
                    $nome_scaletta = $info_scaletta["nome_scaletta"];
                    $nome_artista = $info_scaletta["nome_arte"];

                    echo "<p class='mb-4'><strong>Scaletta selezionata:</strong> $nome_scaletta - $nome_artista</p>";
                }
            }
            ?>

            <?php if ($id_scaletta_selezionata > 0 && $id_artista_scaletta > 0) { ?>
                <form method="POST" action="../server/api/scaletta_brani.php">
                    <input type="hidden" name="id_scaletta" value="<?php echo $id_scaletta_selezionata; ?>">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Brano</label>
                            <select name="id_brano" class="form-select" required>
                                <option value="">Seleziona brano</option>
                                <?php
                                $query_brani = "SELECT b.id_brano, b.titolo, a.nome_arte
                                                FROM brani b
                                                JOIN artisti a ON b.id_artista = a.id_artista
                                                WHERE b.id_artista = $id_artista_scaletta
                                                ORDER BY b.titolo";
                                $result_brani = $connessione->query($query_brani);

                                while ($row_brano = $result_brani->fetch_assoc()) {
                                    echo "<option value='{$row_brano['id_brano']}'>
                                            {$row_brano['titolo']} - {$row_brano['nome_arte']}
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Posizione</label>
                            <input type="number" class="form-control" name="posizione" min="1" placeholder="Es. 1" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" rows="3" placeholder="Note sul brano nella scaletta"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-dark">Aggiungi brano</button>
                        </div>

                    </div>
                </form>

                <hr class="my-5">

                <h4 class="mb-3">Brani già presenti nella scaletta</h4>

                <?php
                $query_brani_scaletta = "SELECT 
                                            sb.posizione,
                                            b.titolo,
                                            ap.nome_arte AS artista_principale,
                                            GROUP_CONCAT(af.nome_arte SEPARATOR ', ') AS feat,
                                            sb.note
                                         FROM scaletta_brani sb
                                         JOIN brani b ON sb.id_brano = b.id_brano
                                         JOIN artisti ap ON b.id_artista = ap.id_artista
                                         LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
                                         LEFT JOIN artisti af ON fb.id_artista = af.id_artista
                                         WHERE sb.id_scaletta = $id_scaletta_selezionata
                                         GROUP BY sb.posizione, b.id_brano, b.titolo, ap.nome_arte, sb.note
                                         ORDER BY sb.posizione";

                $result_brani_scaletta = $connessione->query($query_brani_scaletta);

                if ($result_brani_scaletta && $result_brani_scaletta->num_rows > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-striped table-bordered'>";
                    echo "<thead class='table-dark'>
                            <tr>
                                <th>Posizione</th>
                                <th>Titolo</th>
                                <th>Artista</th>
                                <th>Feat</th>
                                <th>Note</th>
                            </tr>
                          </thead><tbody>";

                    while ($row = $result_brani_scaletta->fetch_assoc()) {
                        $feat = !empty($row["feat"]) ? $row["feat"] : "-";
                        $note = !empty($row["note"]) ? $row["note"] : "-";

                        echo "<tr>
                                <td>{$row['posizione']}</td>
                                <td>{$row['titolo']}</td>
                                <td>{$row['artista_principale']}</td>
                                <td>{$feat}</td>
                                <td>{$note}</td>
                              </tr>";
                    }

                    echo "</tbody></table></div>";
                } else {
                    echo "<p class='text-muted'>Nessun brano presente in questa scaletta.</p>";
                }
                ?>

            <?php } else { ?>
                <p class="text-muted">Seleziona una scaletta per vedere i brani disponibili.</p>
            <?php } ?>
        </div>
    </div>

</body>
</html>