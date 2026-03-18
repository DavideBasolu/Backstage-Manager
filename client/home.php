<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">Backstage Manager</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="artisti.php">Artisti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="eventi.php">Eventi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inserisci_artista.php">Inserisci artista</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inserisci_evento.php">Inserisci evento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="p-5 bg-white rounded shadow-sm">
            <h1 class="mb-3">Benvenuto in Backstage Manager</h1>
            <p class="lead">
                Gestionale per manager di artisti musicali.
            </p>
            <p>
                Da questa area puoi visualizzare artisti ed eventi e inserire nuovi dati nel sistema.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>