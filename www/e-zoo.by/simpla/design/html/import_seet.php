<?php


    $connect = new PDO("mysql:host=localhost;dbname=u1332589_ezooby_shop", "u1332589_default", "3zo7H_bO8Kxf");
    $id = $_POST["id"];
    $title = $_POST["title"];
    $textSmall = $_POST["textSmall"];
    $textBig = $_POST["textBig"];
    $imgSmall = $_POST["imgSmall"];
    $imgBig = $_POST["imgBig"];
        $query = "INSERT INTO s_bonus(id, title, textSmall, textBig, imgSmall, imgBig) 
         VALUES ('".$id."','".$title."', '".$textSmall."', '".$textBig."', '".$imgSmall."', '".$imgBig."');
  
  ";
    $statement = $connect->prepare($query);
    $statement->execute();

?>