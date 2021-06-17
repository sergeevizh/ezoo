<?php
//ini_set('display_errors', 1);

$target_dir = $_SERVER["DOCUMENT_ROOT"]. "/simpla/design/html/imgforms/uploads/";

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$target_files = $target_dir . basename($_FILES["fileToUploads"]["name"]);



// Временная директория.
$tmp_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tmp/';

// Постоянная директория.
/*$path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';*/

/*$target_file = $path . basename($_FILES["fileToUpload"]["name"]);*/
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$imageFileTypes = strtolower(pathinfo($target_files,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image




// Подключение к БД.
$dbh = new PDO('mysql:dbname=u1332589_ezooby_shop;host=localhost', 'u1332589_default', '3zo7H_bO8Kxf');
if (isset($_POST['send'])) {

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $textSmall = htmlspecialchars($_POST['textSmall'], ENT_QUOTES);
    $textBig = htmlspecialchars($_POST['textBig'], ENT_QUOTES);
    $datezakaz = htmlspecialchars($_POST['date_zakaz'], ENT_QUOTES);
    $city = htmlspecialchars($_POST["city"], ENT_QUOTES);
    $brend = htmlspecialchars($_POST['brend'], ENT_QUOTES);
    $aktia_at = htmlspecialchars($_POST['aktia_at'], ENT_QUOTES);
    $aktia_to = htmlspecialchars($_POST['aktia_to'], ENT_QUOTES);
    $bonus_at = htmlspecialchars($_POST['bonus_at'], ENT_QUOTES);
    $bonus_to = htmlspecialchars($_POST['bonus_to'], ENT_QUOTES);



    $sth = $dbh->prepare("INSERT INTO `s_bonus` SET `title` = ?, `textSmall` = ?, `textBig` = ?, `date_zakaz` = ?, `city_name` = ?, `brand` = ?, `aktia_at` = ?, `aktia_to` = ?, `bonus_at` = ?, `bonus_to` = ?");
    $sth->execute(array($title, $textSmall, $textBig, $datezakaz, $city, $brend, $aktia_at, $aktia_to, $bonus_at, $bonus_to));

    // Получаем id вставленной записи.
    $insert_id = $dbh->lastInsertId();
    echo "<pre>";
    print_r($insert_id);
    echo "</pre>";


    // Сохранение изображений в БД и перенос в постоянную папку.


            $filename = preg_replace("/[^a-z0-9\.-]/i", '', $row);

                $sth = $dbh->prepare("INSERT INTO `s_bonus_images` SET `bonus_id` = ?, `filename` = ?,`filenames` = ?");
                $sth->execute(array($insert_id, $target_file, $target_files));

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
$imageFileTypes = strtolower(pathinfo($target_files, PATHINFO_EXTENSION));
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

// Check if file already exists
if (file_exists($target_files)) {
    /* echo "Sorry, file already exists.";*/
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUploads"]["size"] > 800000) {
    /* echo "Sorry, your file is too large.";*/
    $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileTypes != "jpg" && $imageFileTypes != "png" && $imageFileTypes != "jpeg"
    && $imageFileTypes != "gif") {
    /*  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";*/
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    /* echo "Sorry, your file was not uploaded.";*/
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUploads"]["tmp_name"], $target_files)) {
        echo "The file " . htmlspecialchars(basename($_FILES["fileToUploads"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
// Редирект, чтобы предотвратить повторную отправку по F5.
/*header('Location: /simpla/design/html/imgform/reviews.php', true, 301);*/
exit();