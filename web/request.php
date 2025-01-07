<?php
include_once('database.php');
session_start();

$conn=dbConnect();

// Happens when user confirms their appointment
if (isset($_POST['rdv'])) {
    if (isset($_SESSION["user_id"]) && isset($_POST['slot'])) {
        // Take appointment (if allowed)
        $state = dbTakeRDV($conn, $_POST['slot'], $_SESSION['user_id'], $error_msg);
        
        header("location:historique.php?valid=$state&msg=$error_msg");
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
    if ($_REQUEST["btn"]=="login") {
        $id_pat = dbGetPatientIdByMail($conn, $_GET['mail']);
        if($id_pat !== false){
            $_SESSION["user_id"] = $id_pat;
            $_SESSION['timezone'] = new DateTimeZone('Europe/Paris');
            header("location:historique.php");
            exit;
        }
        header("location:login.php?msg=Identifiant ou mot de passe incorrect");
        exit;
    }
}


header("location:login.php");
exit;

?>