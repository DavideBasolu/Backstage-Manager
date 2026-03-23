<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Brano - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Brano</h2>

            <form method="POST" action="../server/api/brani.php">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Artista principale</label>
                        <select name="id_artista" class="form-select" required>
                            <option value="">Seleziona artista</option>
                            <?php
                            $query = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte";
                            $result = $connessione->query($query);

                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_artista']}'>{$row['nome_arte']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titolo</label>
                        <input type="text" class="form-control" name="titolo" placeholder="Titolo brano" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Durata</label>
                        <input type="time" class="form-control" name="durata" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">BPM</label>
                        <input type="number" class="form-control" name="bpm" placeholder="Es. 120">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tonalità</label>
                        <input type="text" class="form-control" name="tonalita" placeholder="Es. Do maggiore">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Anno pubblicazione</label>
                        <input type="number" class="form-control" name="anno_pubblicazione" placeholder="Es. 2025">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Genere brano</label>
                        <input type="text" class="form-control" name="genere_brano" placeholder="Es. Pop, Rap, Rock">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Stato brano</label>
                        <select name="stato_brano" class="form-select" required>
                            <option value="">Seleziona stato</option>
                            <option value="pubblicato">Pubblicato</option>
                            <option value="inedito">Inedito</option>
                            <option value="demo">Demo</option>
                            <option value="cover">Cover</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Testo</label>
                        <textarea class="form-control" name="testo" placeholder="Testo del brano" rows="4"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" placeholder="Note" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Feat (facoltativo)</label>
                        <select name="feat[]" class="form-select" multiple size="5">
                            <?php
                            $query_feat = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte";
                            $result_feat = $connessione->query($query_feat);

                            while ($row_feat = $result_feat->fetch_assoc()) {
                                echo "<option value='{$row_feat['id_artista']}'>{$row_feat['nome_arte']}</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">Tieni premuto Ctrl per selezionare più artisti.</small>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-dark">Salva brano</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</body>
</html>