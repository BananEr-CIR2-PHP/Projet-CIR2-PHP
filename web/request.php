<?php
include_once('database.php');
session_start();

// TEMPORAIRE : Garder svp tant que le login n'est pas terminé
if(isset($_REQUEST["btn"])) {
    if ($_REQUEST["btn"]=="temp_login") {
        $_SESSION["user_id"] = 3;
        header("location:historique.php");
        exit;
    }
}

?>