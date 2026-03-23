<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Etichetta - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include("components/navbar.php"); ?>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Etichetta</h2>

            <form method="POST" action="../server/api/etichette.php">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nome etichetta</label>
                        <input type="text" class="form-control" name="nome_etichetta" placeholder="Nome etichetta" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Referente</label>
                        <input type="text" class="form-control" name="referente" placeholder="Nome referente">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-control" name="telefono" placeholder="Telefono">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sede</label>
                        <input type="text" class="form-control" name="sede" placeholder="Sede">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" name="note" placeholder="Note" rows="3"></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-dark">Salva etichetta</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</body>
</html>