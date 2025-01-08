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

if(isset($_REQUEST["btn"])) {
    if ($_REQUEST["btn"]=="login") {
        if(!isset($_GET['mail'])){
            header("location:login.php?msg=Identifiant ou mot de passe incorrect");
        }
        $id_pat = dbGetPatientIdByMail($conn, $_GET['mail']);
        if($id_pat !== false && isset($_GET['password']) && dbCheckPatientPwd($conn, $id_pat, $_GET['password'])){
            $_SESSION["user_id"] = $id_pat;
            $_SESSION['timezone'] = new DateTimeZone('Europe/Paris');
            header("location:historique.php");
            exit;
        }
        header("location:login.php?msg=Identifiant ou mot de passe incorrect");
        exit;
    }
    if ($_REQUEST["btn"]=="register") {
        if($_GET['mail'] !== $_GET['cmail'] || !isset($_GET['mail'])){
            header("location:register.php?msg=Les adresses mails sont différentes");
            exit;
        }
        if(dbNewPatient($conn, $_GET['name'], $_GET['surname'], $_GET['tel'], $_GET['mail'], $_GET['password'], $msg) === false){
            header("location:register.php?msg=$msg");
            exit;
        }
        $id_pat = dbGetPatientIdByMail($conn, $_GET['mail']);
        if($id_pat !== false){
            $_SESSION["user_id"] = $id_pat;
            $_SESSION['timezone'] = new DateTimeZone('Europe/Paris');
            header("location:historique.php");
            exit;
        }
        header("location:request.php?msg=Identifiant ou mot de passe incorrect");
        exit;
    }
}

if(isset($_REQUEST["destroy"])){
    if(isset($_REQUEST['destroy'])){
        if($_REQUEST['destroy'] === "logout"){
            session_destroy();
            header("location:login.php");
            exit;
        }
    }
}

header("location:login.php");
exit;

?>