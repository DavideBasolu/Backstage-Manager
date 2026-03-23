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

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Evento</h2>

            <form method="POST" action="../server/api/eventi.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="id_artista" class="form-select" required>
                            <option value="">Seleziona artista</option>
                            <?php
                            $query = "SELECT id_artista, nome_arte FROM artisti";
                            $result = $connessione->query($query);

                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_artista']}'>{$row['nome_arte']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nome_evento" placeholder="Nome evento" required>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" name="data_evento" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="luogo" placeholder="Luogo" required>
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" class="form-control" name="cachet" placeholder="Cachet">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark">Salva evento</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>