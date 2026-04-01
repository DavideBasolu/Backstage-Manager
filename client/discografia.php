<?php
include("../server/config/connessione.php");

$ricerca = isset($_GET["ricerca"]) ? trim($_GET["ricerca"]) : "";
$filtro_artista = isset($_GET["id_artista"]) ? $_GET["id_artista"] : "";
$filtro_tipo = isset($_GET["tipo_album"]) ? $_GET["tipo_album"] : "";
$filtro_stato = isset($_GET["stato_album"]) ? $_GET["stato_album"] : "";

$query_artisti = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC";
$result_artisti = $connessione->query($query_artisti);

$condizioni = [];

if (!empty($ricerca)) {
    $ricerca_sql = mysqli_real_escape_string($connessione, $ricerca);
    $condizioni[] = "al.titolo_album LIKE '%$ricerca_sql%'";
}

if (!empty($filtro_artista)) {
    $id_artista_sql = (int)$filtro_artista;
    $condizioni[] = "al.id_artista = $id_artista_sql";
}

if (!empty($filtro_tipo)) {
    $tipo_sql = mysqli_real_escape_string($connessione, $filtro_tipo);
    $condizioni[] = "al.tipo_album = '$tipo_sql'";
}

if (!empty($filtro_stato)) {
    $stato_sql = mysqli_real_escape_string($connessione, $filtro_stato);
    $condizioni[] = "al.stato_album = '$stato_sql'";
}

$where = "";
if (count($condizioni) > 0) {
    $where = "WHERE " . implode(" AND ", $condizioni);
}

$query = "
    SELECT 
        al.id_album,
        al.titolo_album,
        al.data_uscita,
        al.tipo_album,
        al.genere,
        al.stato_album,
        al.note,
        a.nome_arte,
        COUNT(ab.id_brano) AS numero_brani,
        SEC_TO_TIME(SUM(TIME_TO_SEC(b.durata))) AS durata_totale
    FROM album al
    JOIN artisti a ON al.id_artista = a.id_artista
    LEFT JOIN album_brani ab ON al.id_album = ab.id_album
    LEFT JOIN brani b ON ab.id_brano = b.id_brano
    $where
    GROUP BY 
        al.id_album,
        al.titolo_album,
        al.data_uscita,
        al.tipo_album,
        al.genere,
        al.stato_album,
        al.note,
        a.nome_arte
    ORDER BY 
        al.data_uscita DESC,
        al.id_album DESC
";

$result = $connessione->query($query);

function badgeStato($stato) {
    switch ($stato) {
        case "pubblicato":
            return "success";
        case "programmato":
            return "warning text-dark";
        case "in lavorazione":
            return "primary";
        case "sospeso":
            return "secondary";
        default:
            return "dark";
    }
}

function badgeTipo($tipo) {
    switch ($tipo) {
        case "album":
            return "dark";
        case "ep":
            return "info text-dark";
        case "mixtape":
            return "secondary";
        case "singolo":
            return "light text-dark border";
        case "deluxe":
            return "danger";
        default:
            return "dark";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discografia - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Discografia</h2>
        <a href="inserisci_album.php" class="btn btn-dark">Nuovo progetto</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="discografia.php" class="row g-3">
                <div class="col-md-3">
                    <label for="ricerca" class="form-label">Cerca titolo</label>
                    <input type="text" name="ricerca" id="ricerca" class="form-control" value="<?php echo htmlspecialchars($ricerca); ?>">
                </div>

                <div class="col-md-3">
                    <label for="id_artista" class="form-label">Artista</label>
                    <select name="id_artista" id="id_artista" class="form-select">
                        <option value="">Tutti</option>
                        <?php
                        if ($result_artisti && $result_artisti->num_rows > 0) {
                            while ($artista = $result_artisti->fetch_assoc()) {
                                $selected = ($filtro_artista == $artista["id_artista"]) ? "selected" : "";
                                echo "<option value='{$artista["id_artista"]}' $selected>{$artista["nome_arte"]}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="tipo_album" class="form-label">Tipo</label>
                    <select name="tipo_album" id="tipo_album" class="form-select">
                        <option value="">Tutti</option>
                        <option value="album" <?php if ($filtro_tipo == "album") echo "selected"; ?>>Album</option>
                        <option value="ep" <?php if ($filtro_tipo == "ep") echo "selected"; ?>>EP</option>
                        <option value="mixtape" <?php if ($filtro_tipo == "mixtape") echo "selected"; ?>>Mixtape</option>
                        <option value="singolo" <?php if ($filtro_tipo == "singolo") echo "selected"; ?>>Singolo</option>
                        <option value="deluxe" <?php if ($filtro_tipo == "deluxe") echo "selected"; ?>>Deluxe</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="stato_album" class="form-label">Stato</label>
                    <select name="stato_album" id="stato_album" class="form-select">
                        <option value="">Tutti</option>
                        <option value="in lavorazione" <?php if ($filtro_stato == "in lavorazione") echo "selected"; ?>>In lavorazione</option>
                        <option value="programmato" <?php if ($filtro_stato == "programmato") echo "selected"; ?>>Programmato</option>
                        <option value="pubblicato" <?php if ($filtro_stato == "pubblicato") echo "selected"; ?>>Pubblicato</option>
                        <option value="sospeso" <?php if ($filtro_stato == "sospeso") echo "selected"; ?>>Sospeso</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-dark w-100">Filtra</button>
                    <a href="discografia.php" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0) { ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1"><?php echo htmlspecialchars($row["titolo_album"]); ?></h4>
                                    <p class="mb-2 text-muted">
                                        <?php echo htmlspecialchars($row["nome_arte"]); ?>
                                        <?php if (!empty($row["data_uscita"])) { ?>
                                            · <?php echo date("d/m/Y", strtotime($row["data_uscita"])); ?>
                                        <?php } else { ?>
                                            · Data non definita
                                        <?php } ?>
                                    </p>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-<?php echo badgeTipo($row["tipo_album"]); ?>">
                                            <?php echo ucfirst(htmlspecialchars($row["tipo_album"])); ?>
                                        </span>

                                        <span class="badge bg-<?php echo badgeStato($row["stato_album"]); ?>">
                                            <?php echo ucfirst(htmlspecialchars($row["stato_album"])); ?>
                                        </span>

                                        <span class="badge bg-light text-dark border">
                                            <?php echo !empty($row["genere"]) ? htmlspecialchars($row["genere"]) : "Genere non specificato"; ?>
                                        </span>

                                        <span class="badge bg-light text-dark border">
                                            <?php echo $row["numero_brani"]; ?> brani
                                        </span>

                                        <span class="badge bg-light text-dark border">
                                            <?php echo !empty($row["durata_totale"]) ? $row["durata_totale"] : "Durata n/d"; ?>
                                        </span>
                                    </div>

                                    <?php if (!empty($row["note"])) { ?>
                                        <p class="mb-0">
                                            <strong>Note:</strong> <?php echo htmlspecialchars($row["note"]); ?>
                                        </p>
                                    <?php } ?>
                                </div>

                                <div class="d-flex align-items-start">
                                    <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#tracklist<?php echo $row["id_album"]; ?>">
                                        Visualizza tracklist
                                    </button>
                                </div>
                            </div>

                            <div class="collapse mt-4" id="tracklist<?php echo $row["id_album"]; ?>">
                                <?php
                                $id_album = (int)$row["id_album"];

                                $query_tracklist = "
                                    SELECT 
                                        ab.numero_traccia,
                                        ab.disco,
                                        ab.note AS note_album_brano,
                                        b.id_brano,
                                        b.titolo,
                                        b.durata,
                                        b.stato_brano,
                                        GROUP_CONCAT(DISTINCT af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
                                    FROM album_brani ab
                                    JOIN brani b ON ab.id_brano = b.id_brano
                                    LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
                                    LEFT JOIN artisti af ON fb.id_artista = af.id_artista
                                    WHERE ab.id_album = $id_album
                                    GROUP BY 
                                        ab.numero_traccia,
                                        ab.disco,
                                        ab.note,
                                        b.id_brano,
                                        b.titolo,
                                        b.durata,
                                        b.stato_brano
                                    ORDER BY ab.disco ASC, ab.numero_traccia ASC
                                ";

                                $result_tracklist = $connessione->query($query_tracklist);
                                ?>

                                <?php if ($result_tracklist && $result_tracklist->num_rows > 0) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered align-middle mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Disco</th>
                                                    <th>Traccia</th>
                                                    <th>Titolo</th>
                                                    <th>Feat</th>
                                                    <th>Durata</th>
                                                    <th>Stato</th>
                                                    <th>Note</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($traccia = $result_tracklist->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo !empty($traccia["disco"]) ? $traccia["disco"] : 1; ?></td>
                                                        <td><?php echo !empty($traccia["numero_traccia"]) ? $traccia["numero_traccia"] : "-"; ?></td>
                                                        <td><?php echo htmlspecialchars($traccia["titolo"]); ?></td>
                                                        <td><?php echo !empty($traccia["feat"]) ? htmlspecialchars($traccia["feat"]) : "-"; ?></td>
                                                        <td><?php echo htmlspecialchars($traccia["durata"]); ?></td>
                                                        <td><?php echo htmlspecialchars($traccia["stato_brano"]); ?></td>
                                                        <td><?php echo !empty($traccia["note_album_brano"]) ? htmlspecialchars($traccia["note_album_brano"]) : "-"; ?></td>
                                                        <td>
                                                            <a href="modifica_brano.php?id_brano=<?php echo (int)$traccia['id_brano']; ?>&return=discografia" 
                                                               class="btn btn-sm btn-outline-dark">Modifica</a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-warning mb-0">
                                        Nessun brano collegato a questo progetto.
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-info">
            Nessun progetto trovato con i filtri selezionati.
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>