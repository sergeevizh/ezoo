<?php  

$dbUser = 'ezooby_admin2019'; 
$dbPass = 'w0FoT}]#AS9s'; 
$dbName = 'ezooby_shop'; 


if( isset( $_POST['my_button'] ) ){
if ($conn = mysql_connect("localhost", $dbUser, $dbPass)) { 
    if (mysql_select_db($dbName)) { 
        $result=mysql_query("UPDATE s_variants SET `metka`='0' WHERE `metka`='286001'"); 
		$result=mysql_query("UPDATE s_variants SET `metka`=(SELECT `brand_id` FROM s_products WHERE `brand_id`='286001' AND s_products.id=s_variants.product_id)");
		$result=mysql_query("UPDATE s_variants SET `compare_price`='-1' WHERE `metka`='286001' AND `compare_price` IS NULL;"); 
		
        if (!$result) die(mysql_error()); else echo 'База данных успешно обновлена!'; 
    } else { 
        echo 'No select db'; 
    } 
} else { 
    echo 'No connect Base'; 
} 
?>
<br/><br/>
<input type="button" onclick="history.back();" value="Вернуться Назад в Админку" style="
    background-color: #459300;
    border: 1.5px solid #a4df70;
    border-radius: 3px;
    text-shadow: #f8f9f6;
    color: white;
"/>
<?php
}


?>