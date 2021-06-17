<?php

include ("bd.php"); //подключение к базе данных

if (isset($_POST['img']))

// Если поле выбора картинки не пустое -

$maxwidth = "600"; // максимальная ширина

$foto_dir = "img/"; // Директория для фотографий

$foto_name = $foto_dir.time()."_".basename
    ($_FILES['myfile']['name']); // Полное имя

$foto_light_name = time()."_".basename
    ($_FILES['myfile']['name']);
// Имя файла исключая путь
$foto_tag = "<img src=\"$foto_name\" 
border=\"0\">"; // Готовый тэг для вставки

$foto_tag_preview = "<img src=\"$foto_name\" 
border=\"0\" width=\"$maxwidth\">";
// Тот же тэг, но для превью

// Текст ошибок
$error_by_mysql = "<label class=\"label\">
Ошибка при добавлении данных в базу</span>";
$error_by_file = "<label class=\"label\">Невозможно 
загрузить файл в директорию. Возможно её не 
существует</span>";


// Начало
if(isset($_FILES["myfile"]))
{
    $myfile = $_FILES["myfile"]["tmp_name"];
    $myfile_name = $_FILES["myfile"]["name"];
    $myfile_size = $_FILES["myfile"]["size"];
    $myfile_type = $_FILES["myfile"]["type"];
    $error_flag = $_FILES["myfile"]["error"];

// Если ошибок не было
    if($error_flag == 0)
    {
        $DOCUMENT_ROOT = $_SERVER['DOCMENT_ROOT'];
        $upfile = getcwd()."/img/" . time()."_".basename
            ($_FILES["myfile"]["name"]);
        if ($_FILES['myfile']['tmp_name'])
        {

//Если не удалось загрузить файл

            if (!move_uploaded_file($_FILES['myfile']
            ['tmp_name'], $upfile))
            {
                echo "$error_by_file";
                exit;
            }

        }
        else
        {
            echo 'Проблема: возможна атака через загрузку файла. ';
            echo $_FILES['myfile']['name'];
            exit;
        }

        $q = "INSERT INTO 3_images (imgSmall) VALUES 
('$foto_name')";
        $query = mysql_query($q);

// Данные успешно внесены в базу данных
if ($query == 'true') {
    echo "
<div class='text'>
<p>Картинка успешно добавлена на сервер!</p>
<br><br>
<table>
<tr>
<td>
<a href='add_images_form.php' 
class='add_images'><div class='add_images_text'>
ДОБАВИТЬ ЕЩЕ КАРТИНКУ</div></a>
</td>
<td>
<a href='index.php' class='add_images'>
<div class='add_images_text'>НА ГЛАВНУЮ</div></a>
</td>
</tr>
</table>
</div>
";
}

// В противном случае, выводим ошибку при
else {
        echo "$error_by_mysql";
    }
}
    elseif ($myfile_size == 0) {
        echo "<br><label class='label'>
Картинка не выбрана!<br><br>
Вернитесь и выберите картинку!</label><br><br>
<a href='add_images_form.php' class='add_images'>
<div class='add_images_text'>ВЫБРАТЬ КАРТИНКУ</div>
</a>";
    }
}
?>