<?php

/*
 * FUNCTIONS
 * */
function clearMyString($string) {
    return preg_replace("/[^A-Za-z0-9 ]/", '', $string);
}

/*
 * DECLARATIONS
 * */
$output = array();
$errors = array();
$hostname = 'localhost';
$username = 'vtsnised_alib';
$password = 'pJI+Xq7ue8Ig';
$dbname = 'vtsnised_appsteam_library';

//date("Y-m-d H:i:s")

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$request = clearMyString($input['request']);
$kod = clearMyString($input['kod']);
$idrada = clearMyString($input['idrada']);
$prviGlas = clearMyString($input['prviglas']);
$drugiGlas = clearMyString($input['drugiglas']);
$datumVreme = clearMyString($input['datumvreme']);
$preDownloadLink = "http://appsteam.vtsnis.edu.rs/ieeestec_glasanje/download/";


/*
$request = "rezultati"
$kod = "f86793d63a8cf04d21066b51461356bc";
*/



// Database connection
try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ));
} catch (PDOException $e) {
    $output[] = $e;
}

$query_status = $dbh->query("
    SELECT * FROM `ieeglasanje_podesavanja`
    WHERE `key` = 'status'
");
foreach ($query_status as $row) {
    $status = $row['value'];
}


// Queries
$query_spisakProjekata = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti` ORDER BY `id` ASC");
$query_prijava = $dbh->prepare("SELECT * FROM `ieeglasanje_kodovi` WHERE `hash` = :kod");
$query_daLiJeGlasao = $dbh->prepare("SELECT * FROM `ieeglasanje_glasovi` WHERE `kod` = :kod");
$query_pregledRada = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti` WHERE `id` = :idrada");
$query_rezultati = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti` ORDER BY `broj_glasova` DESC");
$query_glasanje = $dbh->prepare("
    INSERT INTO `ieeglasanje_glasovi`
    (`kod`, `projekat_id`, `datum_vreme`)
    VALUES (:kod, :projekat_id, :datum_vreme)
");
$query_updateSettings = $dbh->prepare("
    UPDATE `ieeglasanje_podesavanja`
    SET `value` = :value
    WHERE `key` = :key
");
$query_kolikoRadImaGlasova = $dbh->prepare("
    SELECT * FROM `ieeglasanje_glasovi`
    WHERE `projekat_id` = :projekat_id
");
$query_updateBrojGlasova = $dbh->prepare("
    UPDATE `ieeglasanje_projekti`
    SET `broj_glasova` = :broj_glasova
    WHERE `id` = :myId
");






/*
 * --- LOGING...
*/
$query_log = $dbh->prepare("
        INSERT INTO `ieeglasanje_log` (`log_date`, `log_info`, `log_request`)
        VALUES (:log_date, :log_info, :log_request)
        ");
$query_log->bindValue(':log_date', date("Y-m-d H:i:s"), PDO::PARAM_INT);
$query_log->bindValue(':log_info', json_encode($_SERVER), PDO::PARAM_INT);
$query_log->bindValue(':log_request', $inputJSON, PDO::PARAM_INT);
$query_log->execute();


// Request handling
if($request != '') {
    switch ($request) {
        case 'sviprojekti':
            $query_spisakProjekata->execute();
            $results = $query_spisakProjekata->fetchAll();

            foreach($results as $result) {
                $projekat_id = $result['id'];
                $projekat_naziv = $result['naziv'];
                $projekat_opis = $result['opis'];
                $projekat_autori = explode(", ", $result['autori']);
                $projekat_download_link = $result['download_link'];
                $projekat_brojGlasova = $result['broj_glasova'];
                $output[] = array(
                    'id' => $projekat_id,
                    'naziv' => $projekat_naziv,
                    'autori' => $projekat_autori

                   // 'broj_glasova' => $projekat_brojGlasova

                );

            }

            break;
        case 'prijava':
            if ($kod == 'f86793d63a8cf04d21066b51461356bc') {
                $query_updateSettings->execute(array(
                    ':key' => 'status',
                    ':value' => 'true'
                ));
                $output['success'] = "superadmin";
            } elseif ($kod == "b6e92b4beed48a4d2bd42b6ce5f2c4bd") {
                $query_updateSettings->execute(array(
                    ':key' => 'status',
                    ':value' => 'false'
                ));
            } else {
                $query_prijava->execute(array(
                    ':kod' => $kod
                ));
                if($query_prijava->rowCount() == 1) {
                    $output['success'] = "true";
                } else {
                    $output['success'] = "false";
                }
                // handle null code
                if($kod == '') {
                    $errors[] = "Vote code is null!";
                }
            }
            break;
        case 'pregledrada':
            $query_pregledRada->execute(array(
                ':idrada' => $idrada
            ));
            $results = $query_pregledRada->fetchAll();
            foreach($results as $result) {
                $projekat_id = $result['id'];
                $projekat_naziv = $result['naziv'];
                $projekat_opis = $result['opis'];
                $projekat_autori = explode(", ", $result['autori']);
                $projekat_download_link = $result['download_link'];
                $projekat_brojGlasova = $result['broj_glasova'];
                $output = array(
                    'naziv' => $projekat_naziv,
                    'opis' => $projekat_opis,
                    'autori' => $projekat_autori,
                    'download_link' => $preDownloadLink . $projekat_download_link
                );
            }
            break;
        case 'rezultati':
            if($status == "true") {
                $query_rezultati->execute();
                $results = $query_rezultati->fetchAll();
                $output['status'] = $status;
                foreach($results as $result) {
                    $projekat_id = $result['id'];
                    $projekat_naziv = $result['naziv'];
                    $projekat_autori = explode(", ", $result['autori']);
                    $projekat_brojGlasova = $result['broj_glasova'];
                    $output['lista'][] = array(
                        'naziv' => $projekat_naziv,
                        'autori' => $projekat_autori,
                        'broj_glasova' => $projekat_brojGlasova
                    );
                }
            } else {
                $output['status'] = $status;
            }
            break;
        case 'glasanje':
            $results = $query_daLiJeGlasao->execute(array(
                ':kod' => $kod
            ));
            if ($query_daLiJeGlasao->rowCount() >= 1) {
                $output['status'] = 'fail';
                break;
            }
            $query_glasanje->execute(array(
                ':kod' => $kod,
                ':projekat_id' => $prviGlas,
                ':datum_vreme' => $datumVreme
            ));
            $query_glasanje->execute(array(
                ':kod' => $kod,
                ':projekat_id' => $drugiGlas,
                ':datum_vreme' => $datumVreme
            ));
            $output['status'] = 'success';
            break;
        default:
            $errors[] = "Request unhandled!";
            break;
    }
} else {
    $errors[] = "Nothing requested!";
}

// brojanje glasova
for($i = 1; $i <= 65; $i++) {
    $query_kolikoRadImaGlasova->execute(array(
        ':projekat_id' => $i
    ));

    $query_updateBrojGlasova->execute(array(
        ':broj_glasova' => $query_kolikoRadImaGlasova->rowCount(),
        ':myId' => $i
    ));
}


/*
 * --- OUTPUT
 * */

header('Content-Type: application/json');

if (empty($errors)) {
    echo json_encode($output);
} else {
    echo json_encode($errors);

}
