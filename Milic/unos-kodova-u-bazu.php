<?php
$codes = array();
$hashes = array();
$key = "apps";

for ($i = 0; $i < 100; $i++) {
    $codes[] = $key . $i;
}

foreach ($codes as $code) {
    $hashes[] = md5($code);
}

$hostname = 'localhost';
$username = 'vtsnised_alib';
$password = 'pJI+Xq7ue8Ig';
$dbname = 'vtsnised_appsteam_library';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ));
} catch (PDOException $e) {
    $output[] = $e;
}

$query_log = $dbh->prepare("
        INSERT INTO `ieeglasanje_kodovi` (`code`, `hash`)
        VALUES (:code, :hash)
        ");


for ($i = 0; $i < 100; $i++) {
    $query_log->execute(array(
        ':code' => $codes[$i],
        ':hash' => $hashes[$i]
    ));
    echo $i . " done...<br />";
}
