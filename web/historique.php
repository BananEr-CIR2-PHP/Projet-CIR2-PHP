<?php
session_start();
include_once('database.php');
if (! isset($_SESSION["user_id"])) {
    header("location:login.php?msg=WRONG");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title> Historique des RDV </title>
    </head>
    <body>
        
    </body>
</html>