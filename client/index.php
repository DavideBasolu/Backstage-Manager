<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="width: 400px; border-radius: 20px;">
        <div class="text-center">
            <h1 class="h3 mb-3">Backstage Manager</h1>
            <p class="text-muted">Accesso area manager</p>
        </div>

        <form action="home.php" method="GET">
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Username" disabled>
            </div>

            <div class="mb-3">
                <input type="password" class="form-control" placeholder="Password" disabled>
            </div>

            <button type="submit" class="btn btn-dark w-100">Login</button>
        </form>
    </div>

</body>
</html>