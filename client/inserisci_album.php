<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Progetto - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Inserisci Progetto Discografico</h3>
                </div>
                <div class="card-body">
                    <form action="../server/api/album.php" method="POST">

                        <div class="mb-3">
                            <label for="id_artista" class="form-label">Artista</label>
                            <select name="id_artista" id="id_artista" class="form-select" required>
                                <option value="">Seleziona artista</option>
                                <?php
                                $query_artisti = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC";
                                $result_artisti = $connessione->query($query_artisti);

                                if ($result_artisti && $result_artisti->num_rows > 0) {
                                    while ($artista = $result_artisti->fetch_assoc()) {
                                        echo "<option value='{$artista['id_artista']}'>{$artista['nome_arte']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="titolo_album" class="form-label">Titolo progetto</label>
                            <input type="text" name="titolo_album" id="titolo_album" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="data_uscita" class="form-label">Data uscita</label>
                            <input type="date" name="data_uscita" id="data_uscita" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="tipo_album" class="form-label">Tipo progetto</label>
                            <select name="tipo_album" id="tipo_album" class="form-select" required>
                                <option value="">Seleziona tipo</option>
                                <option value="album">Album</option>
                                <option value="ep">EP</option>
                                <option value="mixtape">Mixtape</option>
                                <option value="singolo">Singolo</option>
                                <option value="deluxe">Deluxe</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="genere" class="form-label">Genere</label>
                            <input type="text" name="genere" id="genere" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="stato_album" class="form-label">Stato progetto</label>
                            <select name="stato_album" id="stato_album" class="form-select" required>
                                <option value="in lavorazione">In lavorazione</option>
                                <option value="programmato">Programmato</option>
                                <option value="pubblicato">Pubblicato</option>
                                <option value="sospeso">Sospeso</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea name="note" id="note" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark">Salva progetto</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>