<?php

require_once('api/Simpla.php');
$simpla = new Simpla();


if( isset( $_POST['clear'] ) ) {
    $simpla->cache->clearall();
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
