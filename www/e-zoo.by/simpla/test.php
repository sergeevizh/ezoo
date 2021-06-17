<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">

    <h2>Таблица продукта</h2>

    <div>
    <form action="test2.php" method="post">
        <button style="background: turquoise; border: none;width: 122px;height: 35px;margin-bottom: 17px;color: #fff;">Скачать CSV</button>
    </form>
    </div>
    <table class="table table-bordered" style="width: 1200px">
        <thead>
        <tr>
            <th class="col-md-3">product_id</th>
            <th class="col-md-3">name</th>
            <th class="col-md-3">body</th>
            <th class="col-md-3">sku</th>
        </tr>
        </thead>
        <tbody>

<?php

//Устанавливаем доступы к базе данных:
$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
$user = 'u1332589_default'; //имя пользователя, по умолчанию это root
$password = '3zo7H_bO8Kxf'; //пароль, по умолчанию пустой
$db_name = 'u1332589_ezooby_shop'; //имя базы данных

//Соединяемся с базой данных используя наши доступы:
$link = mysqli_connect($host, $user, $password, $db_name);

//Устанавливаем кодировку (не обязательно, но поможет избежать проблем):
mysqli_query($link, "SET NAMES 'utf8'");

//Формируем тестовый запрос:
$query = "SELECT s_products.id, s_variants.product_id, s_products.name, s_products.body, s_variants.sku  FROM s_products INNER JOIN s_variants ON s_products.id = s_variants.product_id";

//Делаем запрос к БД, результат запроса пишем в $result:
$result = mysqli_query($link, $query) or die(mysqli_error($link));

for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

foreach ($data as $value){
    ?>
<tr>
    <td class="col-md-3"><?=$value['id']?></td>
    <td class="col-md-3"><?=$value['name']?></td>
    <td class="col-md-3"><?=$value['body']?></td>
    <td class="col-md-3"><?=$value['sku']?></td>
</tr>
<?}

?>
        </tbody>
    </table>
</div>

</body>
</html>



<?