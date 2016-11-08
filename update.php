<html>
<head>

</head>
<body>
    
    
     <?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "android_quiz";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error){
        die("Konekcija nije uspela:".$conn->connect_error);
    }
   
   
    $pitanje = $_REQUEST["pitanje"];
    $odgovor1 =  $_REQUEST["odg1"];
    $odgovor2 =  $_REQUEST["odg2"];
    $odgovor3 =  $_REQUEST["odg3"];
    $odgovor4 =   $_REQUEST["odg4"];
    $tacan =   $_REQUEST["tac"];
    
        $zamena = "INSERT INTO pitanja (pitanje, odgovori1, odgovori2, odgovori3, odgovori4, tacan_odgovor)
        VALUES ( '$pitanje', '$odgovor1', '$odgovor2','$odgovor3','$odgovor4', '$tacan')";

            if ($conn->query($zamena) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $zamena . "<br>" . $conn->error;
        }
    
    

        $sql_read =  "SELECT pitanje, odgovori1,odgovori2,odgovori3,odgovori4, tacan_odgovor FROM pitanja";
            $result = $conn->query($sql_read);
            if ($result->num_rows > 0) {
 
            while($row = $result->fetch_assoc()) {
                echo "id: " . $row["pitanje"]. " - Name: " . $row["odgovori1"]. " " . $row["odgovori2"]. "<br>";
            }
        } else {
            echo "0 results";
        }

    

$conn->close();
?>

<form action="update.php" method="post" >
Pitanje <input name="pitanje" type="text"> <br>
Odgovor1 <input name="odg1" type="text"><br>
Odgovor2 <input name="odg2" type="text"> <br>
Odgovor3 <input name="odg3" type="text"> <br>
Odgovor4 <input name="odg4" type="text"><br>
Tacan odgovor <input name="tac" type="text"><br>
<input type="submit">

</form>
    

    
    
</body>
</html>
