<?php
session_start();
if (! isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}
if (! isset($_GET['medecin'])) {
    header("location:medecins.php");
}
include_once('database.php');
include_once('time.php');
$conn=dbConnect();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title> Créneaux du Dr. ... </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg d-flex justify-content-between">
            <img src="img/logo_small.png" srcset="img/logo_medium 900w, img/logo_large.png 1900w, img/logo_xlarge.png 3000w" alt="Logo">
            <form method="get" action="request.php">
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout">Se déconnecter</button>
            </form>
        </nav>

        <div class="mx-auto" style="width:98%">

        </div>
    </body>
</html>