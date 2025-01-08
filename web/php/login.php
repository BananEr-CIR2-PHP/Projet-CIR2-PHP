<?php
    session_start();
    include_once('database.php');
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg d-flex justify-content-between">
            <img src="../img/logo_small.png" srcset="../img/logo_medium 900w, ../img/logo_large.png 1900w, ../img/logo_xlarge.png 3000w" alt="Logo">
            <form method="get" action="request.php">
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout" name="destroy">Se déconnecter</button>
            </form>
        </nav>
        <?php
        if ( isset($_GET['msg'])) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">";
            echo "{$_GET['msg']}</div>";
        }
        ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5 justify-content-center">
                    <div class="card-header text-center">
                        <h3>Connexion</h3>
                    </div>
                    <div class="card-body justify-content-center">
                        <form action="request.php" method="get">
                            <div class="form-group justify-content-center">
                                <label for="mail">Email</label>
                                <input type="email" class="form-control" id="mail" name="mail" required>
                            </div>
                            <div class="form-group justify-content-center">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" value="login" name="btn" class="btn btn-primary btn-block">Se connecter</button>
                        </form>
                        <br>
                        <form action="register.php">
                            <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>