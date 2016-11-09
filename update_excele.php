
<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "android_quiz";


   
   
    $connect = mysqli_connect("$servername", "$username","","$dbname");
    include ("PHPExcel/IOFactory.php");
    $html = "<table border='1'";
    $objPHPExcel = PHPExcel_IOFactory::load('pitanja_film.xls');
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
    {
        
        $highestRow = $worksheet->getHighestRow();
        for($row=2; $row<=$highestRow;$row++)
        {
            $html.="<tr>";
            $pitanje =  mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(0,$row)->getValue());
            $odgovor1 = mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(1,$row)->getValue());
            $odgovor2 = mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(2,$row)->getValue());
            $odgovor3 = mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(3,$row)->getValue());
            $odgovor4 = mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(4,$row)->getValue());
            $tacan =    mysqli_real_escape_string($connect, $worksheet->getCellByColumnAndRow(5,$row)->getValue());
            $sql = "INSERT INTO pitanja (pitanje, odgovori1, odgovori2, odgovori3, odgovori4, tacan_odgovor)
            VALUES ( '$pitanje', '$odgovor1', '$odgovor2','$odgovor3','$odgovor4', '$tacan')";
            mysqli_query($connect, $sql);
            $html.='<td>'.$pitanje.'</td>';
            $html.='<td>'.$odgovor1.'</td>';
            $html.='<td>'.$odgovor2.'</td>';
            $html.='<td>'.$odgovor3.'</td>';
            $html.='<td>'.$odgovor4.'</td>';
            $html.='<td>'.$tacan.'</td>';
            $html.="</tr>";
        }
    }
    $html.='</table>';
    echo $html;
    

?>
