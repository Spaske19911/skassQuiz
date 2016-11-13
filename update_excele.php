
    
<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "android_quiz";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error){
       die("connection failed: ".$conn->connect_error);
    }
   
   
    
    include ("PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load('pitanja_film.xls');
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
    {
        
        $highestRow = $worksheet->getHighestRow();
        for($row=2; $row<=$highestRow;$row++)
        {
          
            $pitanje =       mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(0,$row)->getValue());
            $odgovor1 =      mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(1,$row)->getValue());
            $odgovor2 =      mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(2,$row)->getValue());
            $odgovor3 =      mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(3,$row)->getValue());
            $odgovor4 =      mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(4,$row)->getValue());
            $tacan =         mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(5,$row)->getValue());
            $kategorije =    mysqli_real_escape_string($conn, $worksheet->getCellByColumnAndRow(6,$row)->getValue());
            $sql = "INSERT INTO pitanja (pitanje, odgovori1, odgovori2, odgovori3, odgovori4, tacan_odgovor, kategorija)
            VALUES ( '$pitanje', '$odgovor1', '$odgovor2','$odgovor3','$odgovor4', '$tacan', '$kategorije')";
            mysqli_query($conn, $sql);
        }
    }
    
 

    if ($conn->query($sql) === TRUE) 
        {
            echo "New record created successfully";
        } 
    else 
        {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
    
    

    $conn->close();
?>


 
