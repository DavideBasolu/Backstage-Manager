<?php
include("../server/config/connessione.php");

$id_brano = isset($_GET["id_brano"]) ? (int)$_GET["id_brano"] : 0;
$return = isset($_GET["return"]) ? $_GET["return"] : "discografia";

if ($id_brano <= 0) {
    header("Location: discografia.php");
    exit;
}

$query_brano = "
    SELECT 
        b.*,
        GROUP_CONCAT(fb.id_artista ORDER BY fb.id_artista SEPARATOR ',') AS feat_ids
    FROM brani b
    LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
    WHERE b.id_brano = $id_brano
    GROUP BY b.id_brano
";
$result_brano = $connessione->query($query_brano);

if (!$result_brano || $result_brano->num_rows === 0) {
    header("Location: discografia.php");
    exit;
}

$brano = $result_brano->fetch_assoc();
$feat_ids = !empty($brano["feat_ids"]) ? explode(",", $brano["feat_ids"]) : [];

$query_artisti = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC";
$result_artisti = $connessione->query($query_artisti);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Brano - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Modifica brano</h2>
                <a href="<?php echo htmlspecialchars($return); ?>.php" class="btn btn-outline-dark">← Torna indietro</a>
            </div>

            <?php if (isset($_GET["success"])) { ?>
                <div class="alert alert-success">Brano aggiornato correttamente.</div>
            <?php } ?>

            <?php if (isset($_GET["errore"])) { ?>
                <div class="alert alert-danger">Si è verificato un errore durante il salvataggio.</div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><?php echo htmlspecialchars($brano["titolo"]); ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="../server/api/modifica_brano.php">
                        <input type="hidden" name="id_brano" value="<?php echo $id_brano; ?>">
                        <input type="hidden" name="return" value="<?php echo htmlspecialchars($return); ?>">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Artista principale</label>
                                <select name="id_artista" class="form-select" required>
                                    <?php
                                    $result_artisti->data_seek(0);
                                    while ($artista = $result_artisti->fetch_assoc()) {
                                        $sel = ($artista["id_artista"] == $brano["id_artista"]) ? "selected" : "";
                                        echo "<option value='{$artista['id_artista']}' $sel>{$artista['nome_arte']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Titolo</label>
                                <input type="text" class="form-control" name="titolo"
                                       value="<?php echo htmlspecialchars($brano["titolo"]); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Durata</label>
                                <input type="time" class="form-control" name="durata"
                                       value="<?php echo htmlspecialchars($brano["durata"]); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">BPM</label>
                                <input type="number" class="form-control" name="bpm"
                                       value="<?php echo !empty($brano["bpm"]) ? $brano["bpm"] : ""; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tonalità</label>
                                <input type="text" class="form-control" name="tonalita"
                                       value="<?php echo htmlspecialchars($brano["tonalita"] ?? ""); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Anno pubblicazione</label>
                                <input type="number" class="form-control" name="anno_pubblicazione"
                                       value="<?php echo !empty($brano["anno_pubblicazione"]) ? $brano["anno_pubblicazione"] : ""; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Genere brano</label>
                                <input type="text" class="form-control" name="genere_brano"
                                       value="<?php echo htmlspecialchars($brano["genere_brano"] ?? ""); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stato brano</label>
                                <select name="stato_brano" class="form-select" required>
                                    <?php
                                    $stati = ["pubblicato", "inedito", "demo", "cover"];
                                    foreach ($stati as $s) {
                                        $sel = ($brano["stato_brano"] === $s) ? "selected" : "";
                                        echo "<option value='$s' $sel>" . ucfirst($s) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Testo</label>
                                <textarea class="form-control" name="testo" rows="5"><?php echo htmlspecialchars($brano["testo"] ?? ""); ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" name="note" rows="3"><?php echo htmlspecialchars($brano["note"] ?? ""); ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Feat (facoltativo)</label>
                                <select name="feat[]" class="form-select" multiple size="5">
                                    <?php
                                    $result_artisti->data_seek(0);
                                    while ($artista = $result_artisti->fetch_assoc()) {
                                        $sel = in_array($artista["id_artista"], $feat_ids) ? "selected" : "";
                                        echo "<option value='{$artista['id_artista']}' $sel>{$artista['nome_arte']}</option>";
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Tieni premuto Ctrl per selezionare più artisti.</small>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="<?php echo htmlspecialchars($return); ?>.php" class="btn btn-outline-secondary">Annulla</a>
                                <button type="submit" class="btn btn-dark">Salva modifiche</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
