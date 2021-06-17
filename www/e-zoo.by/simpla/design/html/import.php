<?php


if(isset($_POST["id"]))
{
    $connect = new PDO("mysql:host=localhost;dbname=u1332589_ezooby_shop", "u1332589_default", "3zo7H_bO8Kxf");
    $id = $_POST["id"];
    $cod = $_POST["cod"];
    $active = $_POST["active"];
    $bonus_id = $_POST["bonus_id"];
    for($count = 0; $count < count($id); $count++)
    {
        $query .= "
  INSERT INTO s_bonus_cods(id, cod, active, bonus_id) 
  VALUES ('".$id[$count]."','".$cod[$count]."', '".$active[$count]."', '".$bonus_id[$count]."');
  
  ";
    }
    $statement = $connect->prepare($query);
    $statement->execute();
}

?>