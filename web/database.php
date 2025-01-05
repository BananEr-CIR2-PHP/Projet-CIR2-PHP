<?php

include_once('constants.php');
include_once('time.php');

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

function getFullName($firstname, $lastname) {
    return ucfirst($firstname)." ".strtoupper($lastname);
}

function dbGetAllRDVIds($conn, $id_patient) {
    $stmt = $conn->prepare('SELECT id FROM rdv WHERE id_patient=:id ORDER BY debut DESC;');
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

function dbGetDocs($conn, $doc_name, $doc_spe, $place) {
    $spe = "%".strtolower($doc_spe)."%";
    $name = "%".strtolower($doc_name)."%";
    $placename = "%".strtolower($place)."%";

    $stmt = $conn->prepare('SELECT m.id AS "doc_id", m.prenom AS "firstname", m.nom "lastname", spe.specialite AS "speciality" FROM medecin m
    JOIN specialite spe ON m.specialite_id=spe.id
    JOIN rdv r ON r.id_medecin=m.id
    JOIN etablissement e ON r.id_etablissement=e.id
    WHERE r.id_patient IS NULL
    AND LOWER(spe.specialite) LIKE :docspe
    AND LOWER(CONCAT(m.prenom, \' \', m.nom)) LIKE :docname
    AND LOWER(e.nom) LIKE :placename
    GROUP BY m.id,m.prenom, m.nom, spe.specialite;'); 

    $stmt->bindParam(':docspe', $spe);
    $stmt->bindParam(':docname', $name);
    $stmt->bindParam(':placename', $placename);
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result;
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetDocPlaces($conn, $doc_id) {
    $stmt = $conn->prepare('SELECT e.nom AS "place" FROM medecin m
    JOIN rdv r ON r.id_medecin=m.id
    JOIN etablissement e ON r.id_etablissement=e.id
    WHERE r.id_patient IS NULL
    AND m.id=:docid
    GROUP BY e.nom');

    $stmt->bindParam(':docid', $doc_id);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result['place'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetAllDocNames($conn) {
    $stmt = $conn->query('SELECT prenom AS "firstname", nom AS "lastname" FROM medecin;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield getFullName($result['firstname'], $result['lastname']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetAllSpe($conn) {
    $stmt = $conn->query('SELECT specialite AS "spe" FROM specialite;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result['spe'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetAllPlaces($conn) {
    $stmt = $conn->query('SELECT nom AS "place" FROM etablissement;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result['place'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

function dbGetAvailableRDVSlots($conn, $id_doc, $start_tmstmp, $end_tmstmp) {
    $start_time = formatAlpha($start_tmstmp);
    $end_time = formatAlpha($end_tmstmp);

    $stmt = $conn->prepare('SELECT
    extract(epoch FROM r.debut)::int AS "start_tmstmp",
    extract(epoch FROM r.fin)::int AS "end_tmstmp",
    e.nom AS "place", r.id AS "slot_id" FROM rdv r
    JOIN etablissement e ON r.id_etablissement=e.id
    WHERE r.id_patient IS NULL
    AND r.id_medecin=:id_doc
    AND r.debut>NOW()
    AND (r.debut>=:start_time AND r.debut<=:end_time OR r.fin>=:start_time AND r.fin<=:start_time);');
    
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':id_doc', $id_doc);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function dbRDVExists($conn, $rdv_id) {
    $stmt = $conn->prepare("SELECT id FROM rdv WHERE id=:rdv_id;");
    $stmt->bindParam(':rdv_id', $rdv_id);
    $stmt->execute();
    
    return ($stmt->fetch(PDO::FETCH_ASSOC) !== false);
}

function dbIsRDVTaken($conn, $rdv_id) {
    $stmt = $conn->prepare("SELECT id_patient FROM rdv WHERE id=:rdv_id;");
    $stmt->bindParam(':rdv_id', $rdv_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result === false || $result['id_patient'] !== null;
}

function dbHasAlreadyRDV($conn, $id_patient, $start_time, $end_time) {
    $stmt = $conn->prepare("SELECT id FROM rdv
    WHERE id_patient=:id_patient
    AND (debut<=:start_time AND fin>=:start_time
    OR debut<=:end_time AND fin>=:end_time);");

    $stmt->bindParam(':id_patient', $id_patient);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($result !== false);
}

function dbTakeRDV($conn, $rdv_id, $id_patient, &$error_msg) {
    if (!dbRDVExists($conn, $rdv_id)) {
        $error_msg = "Le rendez-vous sélectionné n'existe pas.";
        return false;
    }

    if (dbIsRDVTaken($conn, $rdv_id)) {
        $error_msg = "Le rendez-vous sélectionné est déjà pris.";
        return false;
    }

    $rdv_info = dbGetRDVInfo($conn, $rdv_id);
    if (dbHasAlreadyRDV($conn, $id_patient, $rdv_info['start'], $rdv_info['end'])) {
        $error_msg = "Vous avez déjà pris un rendez-vous à la même heure.";
        return false;
    }

    // Everything is OK: we can take appointment!
    $stmt = $conn->prepare("UPDATE rdv SET id_patient=:id_patient WHERE id=:rdv_id;");
    $stmt->bindParam(':id_patient', $id_patient);
    $stmt->bindParam(':rdv_id', $rdv_id);
    $stmt->execute();

    $error_msg = "Le rendez-vous a bien été pris.";
    return true;
}

?>