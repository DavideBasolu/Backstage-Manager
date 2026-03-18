<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Artista - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">Backstage Manager</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="artisti.php">Artisti</a>
                <a class="nav-link" href="eventi.php">Eventi</a>
                <a class="nav-link" href="inserisci_artista.php">Inserisci artista</a>
                <a class="nav-link" href="inserisci_evento.php">Inserisci evento</a>
                <a class="nav-link text-danger" href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Inserisci Artista</h2>

            <form method="POST" action="../server/api/artisti.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nome" placeholder="Nome" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="cognome" placeholder="Cognome" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nome_arte" placeholder="Nome d'arte" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="genere" placeholder="Genere musicale">
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="telefono" placeholder="Telefono" pattern="[0-9]+">
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="citta" placeholder="Città">
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