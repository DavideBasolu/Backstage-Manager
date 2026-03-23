<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Scaletta - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

   <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Scaletta</h2>

            <form method="POST" action="../server/api/scalette.php">
                <div class="row g-3">

                    <!-- ARTISTA -->
                    <div class="col-md-6">
                        <label class="form-label">Artista</label>
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

                    <!-- NOME SCALETTA -->
                    <div class="col-md-6">
                        <label class="form-label">Nome scaletta</label>
                        <input type="text" class="form-control" name="nome_scaletta" placeholder="Es. Tour Estate 2025" required>
                    </div>

                    <!-- DURATA -->
                    <div class="col-md-6">
                        <label class="form-label">Durata totale</label>
                        <input type="time" class="form-control" name="durata_totale">
                    </div>

                    <!-- DESCRIZIONE -->
                    <div class="col-12">
                        <label class="form-label">Descrizione</label>
                        <textarea class="form-control" name="descrizione" rows="3" placeholder="Descrizione scaletta"></textarea>
                    </div>

                    <!-- NOTE -->
                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Note"></textarea>
                    </div>

                    <!-- SUBMIT -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark">Salva scaletta</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</body>
</html>