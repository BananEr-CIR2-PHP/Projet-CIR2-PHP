<?php
session_start();
if (! isset($_SESSION["user_id"])) {
    header("location:login.php");
    exit;
}
include_once('database.php');
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

<?php
// Create datalist for list of specialities
echo "<datalist id=specialites>";
foreach (dbGetAllSpe($conn) as $spe) {
    echo "<option value=\"$spe\">";
}
echo "</datalist>";
?>
        <div class="mx-auto" style="width:98%">
            <form action="" method="post" class="d-flex justify-content-between m-3">
                <div>
                    <label for="medecin" class="px-3">Docteur</label>
                    <input type="text" name="medecin"></input>
                </div>
                <div>
                    <label for="specialite" class="px-3">Spécialité</label>
                    <input type="search" name="specialite" list="specialites"></input>
                </div>
                <div>
                    <label for="etablissement" class="px-3">Établissement</label>
                    <input type="text" name="etablissement"></input>
                </div>
                <button type="submit" value="search" class="btn btn-primary rounded-3">Rechercher</button>
            </form>
<?php
// Show each doctor corresponding to search
$search_doc_name = isset($_POST['medecin']) ? $_POST['medecin'] : "";
$search_spe = isset($_POST['specialite']) ? $_POST['specialite'] : "";
$search_place = isset($_POST['etablissement']) ? $_POST['etablissement'] : "";

echo "<div class=\"rounded-2 border border-dark d-grid gap-3 p-2\">";
foreach (dbGetDocs($conn, $search_doc_name, $search_spe, $search_place) as $doc) {
    $fullname = getFullName($doc['firstname'], $doc['lastname']);
    $spe = ucfirst($doc['speciality']);

    echo "
    <div class=\"bg-primary text-light rounded-2\">
        <div class=\"d-flex justify-content-between\">
            <div>
                <p class=\"my-auto p-2\">Dr. $fullname, <b>$spe</b></p>
                <p class=\"my-auto p-2\"><i>";
    $isfirst = true;
    foreach (dbGetDocPlaces($conn, $doc['doc_id']) as $place) {
        if ($isfirst) {
            $isfirst = false;
        }
        else {
            echo ", ";
        }
        echo $place;
    }

    echo "
                </i></p>
            </div>
            <form action=\"\" method=\"post\" class=\"align-self-center mx-2\">
                <button type=\"submit\" class=\"btn bg-primary border-light rounded-5 text-light m-1\" value=\"\">Prendre rendez-vous</button>
            </form>
        </div>
    </div>";
}
echo "</div>";
?>


        </div>
    </body>
</html>