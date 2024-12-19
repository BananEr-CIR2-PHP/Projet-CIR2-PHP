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

function dbGetAllRDVIds($conn, $id_patient) {
    $stmt = $conn->prepare('SELECT id FROM rdv WHERE id_patient=:id;');
    $stmt->bindParam(':id', $id_patient);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generator of rdv ids, to avoid using fetchAll
    while ($result !== false) {
        yield $result['id'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetDocId($conn, $rdv_id) {
    $stmt = $conn->prepare('SELECT id_medecin FROM rdv WHERE id=:id;');
    $stmt->bindParam(':id', $rdv_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return false;
    }
    return $result['id_medecin'];
}

function dbGetDocFullName($conn, $doc_id) {
    $stmt = $conn->prepare('SELECT nom, prenom FROM medecin WHERE id=:id;');
    $stmt->bindParam(':id', $doc_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return false;
    }
    return ucfirst($result['prenom'])." ".strtoupper($result['nom']);
}

function dbGetDocSpe($conn, $doc_id) {
    $stmt = $conn->prepare('SELECT s.specialite FROM medecin m JOIN specialite s ON s.id=m.specialite_id WHERE m.id=:id;');
    $stmt->bindParam(':id', $doc_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return false;
    }
    return ucfirst($result['specialite']);
}

function dbGetRDVInfo($conn, $rdv_id) {
    $stmt = $conn->prepare('SELECT e.nom AS "place", r.debut AS "start", r.fin AS "end" FROM rdv r JOIN etablissement e ON e.id=r.id_etablissement WHERE r.id=:id;');
    $stmt->bindParam(':id', $rdv_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>