<?php
include_once('database.php');
session_start();

// Happens when user confirms their appointment
if (isset($_POST['rdv'])) {
    echo "test";
    if (isset($_SESSION["user_id"]) && isset($_POST['slot'])) {
        // TODO: prendre rdv
        // /!\ Tester si autorisé: si rdv existe, si rdv pas déjà pris, si utilisateur n'a pas déjà rdv
        // $_POST['slot'] contient l'id du rdv à prendre dans la table rdv
        header("location:historique.php");
        exit;
    }
}

// TEMPORAIRE : Garder svp tant que le login n'est pas terminé
if(isset($_REQUEST["btn"])) {
    if ($_REQUEST["btn"]=="temp_login") {
        $_SESSION["user_id"] = 3;
        $_SESSION['timezone'] = new DateTimeZone('Europe/Paris');
        header("location:historique.php");
        exit;
    }
}

?>