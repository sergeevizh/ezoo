<?php
//Устанавливаем доступы к базе данных:
$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
$user = 'u1332589_default'; //имя пользователя, по умолчанию это root
$password = '3zo7H_bO8Kxf'; //пароль, по умолчанию пустой
$db_name = 'u1332589_ezooby_shop'; //имя базы данных

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="export.csv";');

/*$conn = new mysqli($host, $user, $password, $db_name);  // Создаем коннект к БД*/
//Соединяемся с базой данных используя наши доступы:
$link = mysqli_connect($host, $user, $password, $db_name);

//Устанавливаем кодировку (не обязательно, но поможет избежать проблем):
mysqli_query($link, "SET NAMES 'CP1251'");

$sql = "SELECT s_products.id,s_products.name,s_products.body,s_variants.sku  FROM s_products INNER JOIN s_variants ON s_products.id = s_variants.product_id";  // SQL-запрос
$result = $link->query($sql);  // Выполняем запрос

$fp = fopen('php://output', 'w');  // Открываем поток для записи

while($row = $result->fetch_assoc()) {  // Перебираем строки
    fputcsv($fp, $row, ";");  // Записываем строки в поток
}

$link->close();  // Закрываем коннект к БД
?>