<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Artista - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Artista</h2>

            <form method="POST" action="../server/api/artisti.php">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" placeholder="Nome" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cognome</label>
                        <input type="text" class="form-control" name="cognome" placeholder="Cognome" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nome d'arte</label>
                        <input type="text" class="form-control" name="nome_arte" placeholder="Nome d'arte" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Genere musicale</label>
                        <input type="text" class="form-control" name="genere_musicale" placeholder="Genere musicale">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-control" name="telefono" placeholder="Telefono">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Città</label>
                        <input type="text" class="form-control" name="citta" placeholder="Città">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Etichetta</label>
                        <select name="id_etichetta" class="form-select">
                            <option value="">Nessuna etichetta</option>
                            <?php
                            $query = "SELECT id_etichetta, nome_etichetta FROM etichette ORDER BY nome_etichetta";
                            $result = $connessione->query($query);

                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id_etichetta']}'>{$row['nome_etichetta']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" placeholder="Note" rows="4"></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-dark">Salva artista</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</body>
</html>