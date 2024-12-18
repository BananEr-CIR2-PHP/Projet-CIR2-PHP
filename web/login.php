<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg d-flex justify-content-between">
            <img src="img/logo_small.png" srcset="img/logo_medium 900w, img/logo_large.png 1900w, img/logo_xlarge.png 3000w" alt="Logo">
            <form method="get" action="request.php">
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout">Se déconnecter</button>
            </form>
        </nav>
        <div class="justify-content-center container p-5 m-5 border border-black rounded-3 align-items-center">
            <form>
                <div class="row align-self-center">
                    <label for="inputEmail" class="col-auto col-form-label">Email</label>
                    <div class="col-auto">
                        <input type="email" class="form-control" id="inputEmail">
                    </div>
                </div>
                <div class="row align-self-center">
                    <label for="inputPassword" class="col-auto col-form-label">Mot de Passe</label>
                    <div class="col-auto">
                        <input type="password" class="form-control" id="inputPassword">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Se Connecter</button>
            </form>
        </div>
    </body>
</html>















<!-- TEMPORAIRE : Garder en bas de la page svp tant que le login n'est pas terminé -->
<a href="request.php?btn=temp_login">Connexion temporaire</a>