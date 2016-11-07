<?php
/*
	define('DB_HOST','');
	define('DB_USER','');
	define('DB_PASS','');
	define('DB_NAME','vtsnis_edu_rs_db_3');
*/
/*
	$connection = mysql_connect("localhost", "vtsnised_alib", "pJI+Xq7ue8Ig") or die(mysql_error());
	mysql_select_db("vtsnised_appsteam_library") or die(mysql_error());
*/
header('Content-Type: application/json');
echo json_encode($_SERVER);

?>
