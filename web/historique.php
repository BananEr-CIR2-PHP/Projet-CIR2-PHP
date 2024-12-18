<?php
session_start();
include_once('database.php');
if (! isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}

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
                <button class="btn border-light rounded-5 text-light" type="submit" value="logout">Se déconnecter</button>
            </form>
        </nav>

        <div class="mx-auto" style="width:98%">
            <div class="d-flex justify-content-between">
                <h1>Mes rendez-vous</h1>
                <form action="" method="get" class="align-self-center">
                    <button type="submit" class="btn bg-primary border-light rounded-5 text-light">Prendre rendez-vous</button>
                </form>
            </div>

<?php
// Show each RDV
echo "<div class=\"rounded-2 border border-dark d-grid gap-3 p-1\">";
foreach (dbGetAllRDVIds($conn, $_SESSION['user_id']) as $rdv_id) {
    $doc_id = dbGetDocId($conn, $rdv_id);
    $doc_fullname = dbGetDocFullName($conn, $doc_id);
    $doc_spe = dbGetDocSpe($conn, $doc_id);
    $doc_place = dbGetRDVPlace($conn, $rdv_id);

    echo "
    <div class=\"rounded-2 border border-dark d-flex flex-column\">
        <div class=\"d-flex justify-content-between bg-primary text-light\">
            <p class=\"my-auto\">RDV du ... à ...</p>
            <form action=\"\" method=\"post\">
                <button type=\"submit\" class=\"btn bg-primary border-light rounded-5 text-light m-1\" value=\"\">Prendre un autre rendez-vous avec Dr. $doc_fullname</button>
            </form>
        </div>
        <div class=\"d-flex justify-content-between\">
            <p class=\"my-auto p-2\">Dr. $doc_fullname</p>
            <p class=\"my-auto p-2\">$doc_spe</p>
            <p class=\"my-auto p-2\">$doc_place</p>
        </div>
    </div>";
}
echo "</div>";
?>
            
            <!-- Example line
            <div class="rounded-2 border border-dark d-grid gap-3 p-1">
                <div class="rounded-2 border border-dark d-flex flex-column">
                    <div class="d-flex justify-content-between bg-primary text-light">
                        <p class="my-auto">RDV du ... à ...</p>
                        <form action="" method="post">
                            <button type="submit" class="btn bg-primary border-light rounded-5 text-light m-1" value="">Prendre un autre rendez-vous avec ...</button>
                        </form>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="my-auto p-2">Dr. Pierre LEMOINE</p>
                        <p class="my-auto p-2">Neurologie</p>
                        <p class="my-auto p-2">CHU de Nantes</p>
                    </div>
                </div>
            </div> -->
        </div>
    </body>
</html>