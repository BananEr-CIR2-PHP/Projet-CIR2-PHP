<?php

include_once('constants.php');
include_once('time.php');

// Connect to database.
// Use this function before any other listed in this file
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

// Get fullname from a firstname and a lastname in format:
// Firstname LASTNAME
function getFullName($firstname, $lastname) {
    return ucfirst($firstname)." ".strtoupper($lastname);
}

// Get ids of all appointments in database, as a generator
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

// Get doctor id, given an appointment id
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

// Get full name of a doctor, given its id
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

// Get speciality of a doctor, given its id
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

// Get an array of informations on an appointment, given its id
// use ['place'], ['start'], ['end']
function dbGetRDVInfo($conn, $rdv_id) {
    $stmt = $conn->prepare('SELECT e.nom AS "place", r.debut AS "start", r.fin AS "end" FROM rdv r JOIN etablissement e ON e.id=r.id_etablissement WHERE r.id=:id;');
    $stmt->bindParam(':id', $rdv_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all doctors corresponding to given parameters
// Works even if name, spe, or place are given partially
// Return a generator of all doctors corresponding to given parameters
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

// Return a generator of all places a doctor has ever made an appointment, given the doctor id
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

// Return a generator of all doctors names
function dbGetAllDocNames($conn) {
    $stmt = $conn->query('SELECT prenom AS "firstname", nom AS "lastname" FROM medecin;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield getFullName($result['firstname'], $result['lastname']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Return a generator of all doctors specialities
function dbGetAllSpe($conn) {
    $stmt = $conn->query('SELECT specialite AS "spe" FROM specialite;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result['spe'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Return a generator of all doctors places
function dbGetAllPlaces($conn) {
    $stmt = $conn->query('SELECT nom AS "place" FROM etablissement;');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($result !== false) {
        yield $result['place'];
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Get all available appointment slots, of the given doctor (id),
// from the given start timestamp to the given end timestamp
// Are accessible: ['start_tmstmp'], ['end_tmstmp'], ['place'], ['slot_id']
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

// Return true if the given appointment id exists in the database, false otherwise
function dbRDVExists($conn, $rdv_id) {
    $stmt = $conn->prepare("SELECT id FROM rdv WHERE id=:rdv_id;");
    $stmt->bindParam(':rdv_id', $rdv_id);
    $stmt->execute();
    
    return ($stmt->fetch(PDO::FETCH_ASSOC) !== false);
}

// Return true if someone already booked the given appointment, false otherwise
function dbIsRDVTaken($conn, $rdv_id) {
    $stmt = $conn->prepare("SELECT id_patient FROM rdv WHERE id=:rdv_id;");
    $stmt->bindParam(':rdv_id', $rdv_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result === false || $result['id_patient'] !== null;
}

// Return true if given person already has an appointment during the given period, false otherwise
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

// Take an appointment at the given slot (rdv_id), for id_patient.
// error_msg outputs result as an error or success message.
// Return true if succeded, false otherwise
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

// Return id of person given its email
function dbGetPatientIdByMail($conn, $email){
    $stmt = $conn->prepare('SELECT id FROM patient WHERE email=:email');

    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        return false;
    }
    return $result['id'];
}

// Check if given password if correct for the given person
function dbCheckPatientPwd($conn, $id_patient, $pwd) {
    $stmt = $conn->prepare('SELECT mdp_hash AS "pwd_hash" FROM patient WHERE id=:id');
    $stmt->bindParam(':id', $id_patient);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return false;
    }
    return check_pwd($result['pwd_hash'], $pwd);
}

// Return true if email is already present in patient table, false otherwise
function dbPatientEmailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM patient WHERE email=:email;");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return ($stmt->fetch(PDO::FETCH_ASSOC) !== false);
}

// Return true if email is already present in medecin table, false otherwise
function dbDocEmailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM medecin WHERE email=:email;");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return ($stmt->fetch(PDO::FETCH_ASSOC) !== false);
}

// Return hash of given password
function hash_pwd($pwd) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($pwd, PASSWORD_BCRYPT, $options);
}

// Check if hash corresponds to given password
function check_pwd($pwd_hash, $pwd) {
    return password_verify($pwd, $pwd_hash);
}

// Add a new person to patient. msg outputs state of request as a sentence.
// Return true if succeded, false otherwise
function dbNewPatient($conn, $name, $firstname, $tel, $email, $mdp, &$msg){
    if (dbPatientEmailExists($conn, $email) || dbDocEmailExists($conn, $email)) {
        $msg = "Ce compte existe déjà.";
        return false;
    }

    $pwd_hash = hash_pwd($mdp);
    
    $stmt = $conn->prepare("INSERT INTO patient (nom, prenom, tel, email, mdp_hash)
    VALUES (:lastname, :firstname, :tel, :email, :mdp_hash);");
    
    $stmt->bindParam(':lastname', $name);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mdp_hash', $pwd_hash);

    $stmt->execute();
    return true;
}

?>