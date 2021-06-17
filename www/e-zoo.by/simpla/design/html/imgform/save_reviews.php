<?php
//ini_set('display_errors', 1);

$target_dir = $_SERVER["DOCUMENT_ROOT"]. "/simpla/design/html/imgform/uploads/";

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);


// Временная директория.
$tmp_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tmp/';

// Постоянная директория.
/*$path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';*/

/*$target_file = $path . basename($_FILES["fileToUpload"]["name"]);*/
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image




// Подключение к БД.
$dbh = new PDO('mysql:dbname=u1332589_ezooby_shop;host=localhost', 'u1332589_default', '3zo7H_bO8Kxf');

if (isset($_POST['send'])) {



    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);


    $text = htmlspecialchars($_POST['text'], ENT_QUOTES);

    $sth = $dbh->prepare("INSERT INTO `reviews` SET `name` = ?, `text` = ?, `date_add` = UNIX_TIMESTAMP()");
    $sth->execute(array($name, $text));

    // Получаем id вставленной записи.
    $insert_id = $dbh->lastInsertId();

    // Сохранение изображений в БД и перенос в постоянную папку.


            $filename = preg_replace("/[^a-z0-9\.-]/i", '', $row);

                $sth = $dbh->prepare("INSERT INTO `reviews_images` SET `reviews_id` = ?, `filename` = ?");
                $sth->execute(array($insert_id, $target_file));

                // Перенос оригинального файла
              /*  rename($tmp_path . $filename, $path . $filename);

                // Перенос превью
                $file_name = pathinfo($filename, PATHINFO_FILENAME);
                $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                $thumb = $file_name . '-thumb.' . $file_ext;
                rename($tmp_path . $thumb, $path . $thumb);*/



}




$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
/*
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }*/


// Check if file already exists
if (file_exists($target_file)) {
   /* echo "Sorry, file already exists.";*/
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 800000) {
   /* echo "Sorry, your file is too large.";*/
    $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
  /*  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";*/
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   /* echo "Sorry, your file was not uploaded.";*/
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
// Редирект, чтобы предотвратить повторную отправку по F5.
/*header('Location: /simpla/design/html/imgform/reviews.php', true, 301);*/
exit();