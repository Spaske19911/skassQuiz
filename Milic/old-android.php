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
$status = false;
//date("Y-m-d H:i:s")

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$request = clearMyString($input['request']);
$kod = clearMyString($input['kod']);
$idrada = clearMyString($input['idrada']);

/*
$request = "pregledrada";
$idrada = "123456";
*/
// Database connection
try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    $output[] = $e;
}

// Queries
$query_spisakProjekata = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti`");
$query_prijava = $dbh->prepare("SELECT * FROM `ieeglasanje_kodovi` WHERE `kod` = :kod");
$query_daLiJeGlasao = $dbh->prepare("SELECT * FROM `ieeglasanje_glasovi` WHERE `kod` = :kod");
$query_pregledRada = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti` WHERE `id` = :idrada");
$query_rezultati = $dbh->prepare("SELECT * FROM `ieeglasanje_projekti`");

/*
 * --- LOGING...
*/
$query_log = $dbh->prepare("
        INSERT INTO `ieeglasanje_log` (`log_date`, `log_info`, `request`, `code`, `id_rada`)
        VALUES (:log_date, :log_info, :request, :code, :id_rada)
        ");
$query_log->bindValue(':log_date', date("Y-m-d H:i:s"), PDO::PARAM_INT);
$query_log->bindValue(':log_info', json_encode($_SERVER), PDO::PARAM_INT);
$query_log->bindValue(':request', $request, PDO::PARAM_INT);
$query_log->bindValue(':code', $kod, PDO::PARAM_INT);
$query_log->bindValue(':id_rada', $idrada, PDO::PARAM_INT);
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

                /*,
                    'broj_glasova' => $projekat_brojGlasova
                */
                );
            }
            break;
        case 'prijava':
            $query_prijava->execute(array(
                ':kod' => $kod
            ));
            if($query_prijava->rowCount() == 1) {
                $output['success'] = true;
            } else {
                $output['success'] = false;
            }
            // handle null code
            if($kod == '') {
                $errors[] = "Vote code is null!";
            }
            break;
        case 'dalijeglasao':
            $query_daLiJeGlasao->execute(array(
                ':kod' => $kod
            ));
            if($query_daLiJeGlasao->rowCount() != 0) {
                $output['glasao'] = true;
            } else {
                $output['glasao'] = false;
            }
            // handle null code
            if($kod == '') {
                $errors[] = "Vote code is null!";
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
                    'download_link' => $projekat_download_link
                );
            }
            break;
        case 'rezultati':
            if($status) {
                $query_rezultati->execute();
                $results = $query_rezultati->fetchAll();
                foreach($results as $result) {
                    $projekat_id = $result['id'];
                    $projekat_naziv = $result['naziv'];
                    $projekat_autori = explode(", ", $result['autori']);
                    $projekat_brojGlasova = $result['broj_glasova'];
                    $output[] = array(
                        'status' => $status,
                        'naziv' => $projekat_naziv,
                        'autori' => $projekat_autori,
                        'broj_glasova' => $projekat_brojGlasova
                    );
                }
            } else {
                $output['status'] = $status;
            }
            break;
        default:
            $errors[] = "Request unhandled!";
            break;
    }
} else {
    $errors[] = "Nothing requested!";
}
/*
 * OUTPUT
 * */

header('Content-Type: application/json');

if (empty($errors)) {
    echo json_encode($output);
} else {
    echo json_encode($errors);

}
