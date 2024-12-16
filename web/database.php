<?php

include_once('constants.php');

function dbConnect() {
    $dsn = "pgsql:dbname=".DB_NAME.";host=".DB_SERVER.";port=".DB_PORT;
    try {
        $conn = new PDO($dsn, DB_USER, DB_PASSWORD);
    }
    catch (PDOException $e) {
        echo 'La connexion à la BDD a échoué : ' . $e->getMessage();
        $conn = false;
    }
    return $conn;
}

?>