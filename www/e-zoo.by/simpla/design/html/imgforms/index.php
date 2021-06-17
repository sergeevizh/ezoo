<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<?
//Устанавливаем доступы к базе данных:
$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
$user = 'u1332589_default'; //имя пользователя, по умолчанию это root
$password = '3zo7H_bO8Kxf'; //пароль, по умолчанию пустой
$db_name = 'u1332589_ezooby_shop'; //имя базы данных

//Соединяемся с базой данных используя наши доступы:
$link = mysqli_connect($host, $user, $password, $db_name);
$link1 = mysqli_connect($host, $user, $password, $db_name);
//Устанавливаем кодировку (не обязательно, но поможет избежать проблем):
mysqli_query($link, "SET NAMES 'utf8'");
mysqli_query($link1, "SET NAMES 'utf8'");
//Формируем тестовый запрос:
$query = "SELECT s_regions.id, s_regions.name  FROM s_regions";
$query1 = "SELECT s_brands.id, s_brands.name  FROM s_brands";
//Делаем запрос к БД, результат запроса пишем в $result:
$result = mysqli_query($link, $query) or die(mysqli_error($link));
$result1 = mysqli_query($link1, $query1) or die(mysqli_error($link1));


for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
for ($data1 = []; $row1 = mysqli_fetch_assoc($result1); $data1[] = $row1);
/*echo "<pre>";
print_r($data);
echo "</pre>";
echo "<pre>";
print_r($data1);
echo "</pre>";*/



?>


<form method="post" action="/simpla/design/html/imgforms/save_reviews.php" enctype='multipart/form-data'>
    <h3>форма заполнения бонуса:</h3>
    <div class="form-row">
        <label>title:</label>
        <input type="text" name="title" required>
    </div>
    <div class="form-row">
        <label>textSmall:</label>
        <input type="text" name="textSmall" required>
    </div>
    <div class="form-row">
        <label>textBig:</label>
        <input type="text" name="textBig" required>
    </div>
    <div class="form-row">
        <label>Изображения 1:</label>
        <div class="img-list" id="js-file-list"></div>
        <input id="fileToUpload" type="file" name="fileToUpload" multiple accept=".jpg,.jpeg,.png,.gif">
    </div>
    <div class="form-row">
        <label>Изображения 2:</label>
        <div class="img-list" id="js-file-list"></div>
        <input id="fileToUploads" type="file" name="fileToUploads" multiple accept=".jpg,.jpeg,.png,.gif">
    </div>
    <h3>Условие бонуса:</h3>
    <div style="display: flex">
    <div class="form-row">
        <label>Дата заказа:</label>
        <input type="date" id="date_zakaz" name="date_zakaz">
    </div>
    <div class="form-row">
        <label>Выбор города:</label>
        <select name="city[]" multiple="multiple" size="20">
           <?
           foreach ($data as $key => $value){?>
               <option value="<?=$value["id"]?>" aria-setsize="4"><?=$value["name"]?></option>
           <?}
           ?>
        </select>
    </div>

        <div class="form-row">
            <label>Бренды:</label>
            <select name="brend[]" multiple="multiple" size="20">
                <?
                foreach ($data1 as $kay => $valie){?>
                    <option value="<?=$valie["id"]?>"><?=$valie["name"]?></option>
                <?}
                ?>
            </select>
        </div>

        <div class="form-row">
            <label>Акция:</label>
            с <input type="date" id="aktia_at" name="aktia_at">
            по  <input type="date" id="aktia_to" name="aktia_to">

        </div>
        <div class="form-row">
            <label>Срок действия бонуса:</label>
            с <input type="date" id="bonus_at" name="bonus_at">
            по  <input type="date" id="bonus_to" name="bonus_to">

        </div>
    </div>
    <div class="form-submit">
        <input type="submit" name="send" value="Отправить">
    </div>
</form>



<style>
    .form-row {
        margin-bottom: 15px;
        padding-right: 15px;
    }
    .form-row label {
        display: block;
        color: #777;
        margin-bottom: 5px;
    }
    .form-row input[type="text"] {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
    }

    /* Стили для вывода превью */
    .img-item {
        display: inline-block;
        margin: 0 20px 20px 0;
        position: relative;
        user-select: none;
    }
    .img-item img {
        border: 1px solid #767676;
    }
    .img-item a {
        display: inline-block;
        background: url(/remove.png) 0 0 no-repeat;
        position: absolute;
        top: -5px;
        right: -9px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
</body>
</html>