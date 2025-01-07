<?php
session_start();
if (! isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}
include_once('database.php');
include_once('time.php');
$conn=dbConnect();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title> Historique des RDV </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg d-flex justify-content-between">
            <img src="img/logo_small.png" srcset="img/logo_medium 900w, img/logo_large.png 1900w, img/logo_xlarge.png 3000w" alt="Logo">
            <form method="get" action="request.php">
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout" name="destroy">Se déconnecter</button>
            </form>
        </nav>
<?php
// Message
if (isset($_GET['valid']) && isset($_GET['msg'])) {
    if ($_GET['valid'] == 1) {
        echo "<div class=\"alert alert-success\" role=\"alert\">";
    }
    else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">";
    }
    echo "{$_GET['msg']}</div>";  
}
?>
        <div class="mx-auto" style="width:98%">
            <div class="d-flex justify-content-between m-3">
                <h1>Mes rendez-vous</h1>
                <form action="medecins.php" method="get" class="align-self-center">
                    <button type="submit" class="btn bg-primary border-light rounded-5 text-light">Prendre rendez-vous</button>
                </form>
            </div>

<?php
// Show each RDV
echo "<div class=\"rounded-2 border border-dark d-grid gap-3 p-2\">";
foreach (dbGetAllRDVIds($conn, $_SESSION['user_id']) as $rdv_id) {
    $doc_id = dbGetDocId($conn, $rdv_id);
    $doc_fullname = dbGetDocFullName($conn, $doc_id);
    $doc_spe = dbGetDocSpe($conn, $doc_id);
    $rdv_info = dbGetRDVInfo($conn, $rdv_id);
    $doc_place = $rdv_info['place'];        // TODO : gestion des erreurs

    $rdv_start_date = getLocalDate($rdv_info['start']);
    $rdv_start_time = getLocalTime($rdv_info['start']);

    echo "
    <div class=\"rounded-2 border border-dark d-flex flex-column\">
        <div class=\"d-flex justify-content-between bg-primary text-light\">
            <p class=\"my-auto\">RDV du $rdv_start_date à $rdv_start_time</p>
            <form action=\"creneaux_rdv.php\" method=\"get\">
                <button type=\"submit\" name=\"medecin\" class=\"btn bg-primary border-light rounded-5 text-light m-1\" value=\"$doc_id\">Prendre un autre rendez-vous avec Dr. $doc_fullname</button>
            </form>
        </div>
        <div class=\"d-flex justify-content-between\">
            <p class=\"my-auto p-2 text-start\" style=\"width:33%;\">Dr. $doc_fullname</p>
            <p class=\"my-auto p-2 text-center\" style=\"width:33%;\">$doc_spe</p>
            <p class=\"my-auto p-2 text-end\" style=\"width:33%;\">$doc_place</p>
        </div>
    </div>";
}
echo "</div>";
?>
        </div>
    </body>
</html>