<!DOCTYPE html>

<html>
<head>
<?php

$data = file_get_contents('php://input');
$json = json_decode($data);

//$code = $json->code;
//$flag = $json->flag;
$flag = 'login';
$code = 'sega';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "android_quiz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("connection failed: ".$conn->connect_error);
}


        


    
$sql_read =  "SELECT `id` FROM `login` WHERE code = '$code' AND status = 'true'";
$result = mysqli_query($conn, $sql_read);
if (!$result) {
    printf("Error: %s\n", mysqli_error($conn));
    exit();
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$count = mysqli_num_rows($result);

//ako se poklapaju login kodovi, rezultat ce biti 1

if($count == 1){
    
    if($flag == 'login') {
        $conn->set_charset("utf8");
        $sql_read_pitanja = "SELECT * FROM pitanja";
        $result_pitanja = mysqli_query($conn, $sql_read_pitanja);
        if (!$result_pitanja) {
            printf("Error: %s\n", mysqli_error($conn));
            exit();
        }

        while($row_pit = mysqli_fetch_assoc($result_pitanja)) {

            $pitanja[] = $row_pit['pitanje'];
            $odgovori1[] = $row_pit['odgovori1'];
            $odgovori2[] = $row_pit['odgovori2'];
            $odgovori3[] = $row_pit['odgovori3'];
            $odgovori4[] = $row_pit['odgovori4'];
            $t_odgovor[] = $row_pit['tacan_odgovor'];
        }

        $duzina = sizeof($pitanja);
        $range = array_rand(range(1, $duzina-1), 16);

        for ($i=0; $i<16; $i++){
            $random = $range[$i] + 1;
            $arr_odgovori1[] = $odgovori1[$random];
            $arr_odgovori2[] = $odgovori2[$random];
            $arr_odgovori3[] = $odgovori3[$random];
            $arr_odgovori4[] = $odgovori4[$random];
            $arr_t_odgovor[] = $t_odgovor[$random];
            $arr_pitanja[] = $pitanja[$random];

            $json_obj = array(
                "status" => "true",
                "questions" =>$arr_pitanja,
                "answers" => array(
                    $arr_odgovori1,
                    $arr_odgovori2,
                    $arr_odgovori3,
                    $arr_odgovori4
                ),
                "correct_answers" => $arr_t_odgovor
            );
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($json_obj, JSON_UNESCAPED_UNICODE);
    }





    if($flag == 'highscore') {
        $conn->set_charset("utf8");
        $sql_read_score = "SELECT * FROM skor";

        $result_score = mysqli_query($conn, $sql_read_score);
        if (!$result_score) {
            printf("Error: %s\n", mysqli_error($conn));
            exit();
        }
        while($row_score = mysqli_fetch_assoc($result_score)) {
            $ime[] = $row_score['ime'];
            $ustanova[] = $row_score['ustanova'];
            $tacno[] = $row_score['tacno'];
            $netacno[] = $row_score['netacno'];
            $vreme[] = $row_score['vreme'];
        }

        $json_obj_score = array(
            "name" => $ime,
            "institution" => $ustanova,
            "correct" => $tacno,
            "incorrect" => $netacno,
            "time" => $vreme
        );

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($json_obj_score, JSON_UNESCAPED_UNICODE);
    };
/*
    if ($flag == 'hash'){
        $hash = $json ->hash;
        foreach ($hash as $indexi => $has){
            $sql_query_hash = "INSERT INTO login(code,status) VALUES('$has','')";
            if ($conn->query($sql_query_hash) === TRUE) {
                echo 'uspelo';
            }
        }
        var_dump($has);


    }
*/




    if($flag == 'end'){
        $name[] = $json ->name;
        $institution[] = $json ->institution;
        $correct[] = $json->correct;
        $incorrect[] = $json->incorrect;
        $time[] = $json->time;

        $name_i = implode("",$name);
        $ins_i = implode("",$institution);
        $corr_i = implode("",$correct);
        $inc_i = implode("",$incorrect);
        $time_i = implode("",$time);

        //$conn->set_charset("utf8");
        $sql_query_end = "INSERT INTO skor(ime,ustanova,tacno,netacno,vreme) VALUES('$name_i','$ins_i','$corr_i','$inc_i','$time_i')";
        if ($conn->query($sql_query_end) === TRUE) {
            $json_obj_end = array(
                "response"=>'true'
            );
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($json_obj_end, JSON_UNESCAPED_UNICODE);
        } else {
            $json_obj_end = array(
                "response"=>'false'
            );
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($json_obj_end, JSON_UNESCAPED_UNICODE);
            echo "Error: " . $sql_query_end . "<br>" . $conn->error;
        }

        if($code == 'sega'){
            $status = 'true';
        }else{
            $status = 'false';
        }

        $sql_update_status = "UPDATE login SET status='$status' WHERE code = '$code'";
        $conn->query($sql_update_status);
    }

}

else{
    echo "Login code is invalid!";
}
$conn->close();
?>
    
    
</head>

<body>
    
   <p>lkzsmdkamsf</p> 
    
</body>


</html>






