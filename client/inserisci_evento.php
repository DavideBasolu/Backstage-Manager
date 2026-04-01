<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Evento - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Inserisci evento</h3>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET["success"])) { ?>
                        <div class="alert alert-success">
                            Evento salvato correttamente.
                        </div>
                    <?php } ?>

                    <?php if (isset($_GET["errore"])) { ?>
                        <div class="alert alert-danger">
                            Si è verificato un errore durante il salvataggio dell'evento.
                        </div>
                    <?php } ?>

                    <form action="../server/api/eventi.php" method="POST">
                        <div class="mb-3">
                            <label for="id_artista" class="form-label">Artista principale</label>
                            <select name="id_artista" id="id_artista" class="form-select" required>
                                <option value="">Seleziona artista</option>
                                <?php
                                $query_artisti = "SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC";
                                $result_artisti = $connessione->query($query_artisti);

                                if ($result_artisti && $result_artisti->num_rows > 0) {
                                    while ($artista = $result_artisti->fetch_assoc()) {
                                        echo "<option value='{$artista["id_artista"]}'>{$artista["nome_arte"]}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nome_evento" class="form-label">Nome evento</label>
                            <input type="text" name="nome_evento" id="nome_evento" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="data_evento" class="form-label">Data evento</label>
                                <input type="date" name="data_evento" id="data_evento" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="luogo" class="form-label">Luogo</label>
                                <input type="text" name="luogo" id="luogo" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cachet" class="form-label">Cachet</label>
                                <input type="number" step="0.01" name="cachet" id="cachet" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_scaletta" class="form-label">Scaletta associata</label>
                            <select name="id_scaletta" id="id_scaletta" class="form-select">
                                <option value="">Nessuna scaletta</option>
                                <?php
                                $query_scalette = "
                                    SELECT s.id_scaletta, s.nome_scaletta, a.nome_arte
                                    FROM scalette s
                                    JOIN artisti a ON s.id_artista = a.id_artista
                                    ORDER BY a.nome_arte ASC, s.nome_scaletta ASC
                                ";
                                $result_scalette = $connessione->query($query_scalette);

                                if ($result_scalette && $result_scalette->num_rows > 0) {
                                    while ($scaletta = $result_scalette->fetch_assoc()) {
                                        echo "<option value='{$scaletta["id_scaletta"]}' data-artista='{$scaletta["nome_arte"]}'>
                                                {$scaletta["nome_arte"]} - {$scaletta["nome_scaletta"]}
                                              </option>";
                                    }
                                }
                                ?>
                            </select>
                            <small class="text-muted">Puoi associare una scaletta già esistente all'evento.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Artisti ospiti</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 260px; overflow-y: auto;">
                                <?php
                                $result_ospiti = $connessione->query("SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC");

                                if ($result_ospiti && $result_ospiti->num_rows > 0) {
                                    while ($ospite = $result_ospiti->fetch_assoc()) {
                                        echo "<div class='form-check mb-2'>
                                                <input class='form-check-input checkbox-ospite' type='checkbox' name='artisti_ospiti[]' value='{$ospite["id_artista"]}' id='ospite{$ospite["id_artista"]}'>
                                                <label class='form-check-label' for='ospite{$ospite["id_artista"]}'>
                                                    {$ospite["nome_arte"]}
                                                </label>
                                              </div>";
                                    }
                                }
                                ?>
                            </div>
                            <small class="text-muted">L'artista principale non verrà salvato come ospite anche se selezionato.</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark">Salva evento</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>