<html>
<head>
    <title>Загрузка купонов</title>
</head>
<body>
<form action="#" method="post" enctype="multipart/form-data">
    <input type="file" name="file">
    <br>
    <label style="display: none">Enter the folder name:</label>
    <input style="display: none" type="text" name="foldername">
    <br>
    <input type="submit" name="submit" value="Upload">

</form>
</body>
</html>

<?php
if(isset($_POST['submit'])) {
    /* $foldername=$_POST['foldername'];*/

    $foldername = $_SERVER["DOCUMENT_ROOT"] . "/bonus";

    $filename = $_FILES['file']['name'];

    $tmpname = $_FILES['file']['tmp_name'];

    if (!file_exists($foldername)) {
        $result = mkdir($foldername, 0777);
    }
    /*   if($result)
       {
           echo "created folder";
       }
       else
       {
           echo "not created folder";
       }*/

    $row = move_uploaded_file($tmpname, "$foldername/$filename");

    if ($row) {
        echo "succesffully uploaded";
    } else {
        echo "failed to upload";
    }

   /* if ($filename != "") {
        $file_path = $foldername . "/" . $filename;

        $file_handle = fopen($file_path, "r");

        if ($file_handle !== FALSE) {

            $column_headers = fgetcsv($file_handle);
            foreach ($column_headers as $key => $header) {
                $result[$header] = array();

            }
            while (($data = fgetcsv($file_handle)) !== FALSE) {
                $i = 0;
                foreach ($result as &$column) {
                    $column[] = $data[$i++];
                }
            }
            fclose($file_handle);
        }

// print_r($result); // I see all data(s) except the header

        $json = json_encode($result);
        echo "<pre>";
        print_r($json);
        echo "<pre>";


    }*/
    $file_path = $foldername . "/" . $filename;
    $fh = fopen( $file_path, "r");

//Setup a PHP array to hold our CSV rows.
    $csvData = array();

//Loop through the rows in our CSV file and add them to
//the PHP array that we created above.
    while (($row = fgetcsv($fh, 0, ",")) !== FALSE) {
        $csvData[] = $row;
    }

//Finally, encode our array into a JSON string format so that we can print it out.
    echo "<pre>";
    print_r(json_encode($csvData));
    echo "<pre>";


    $host = 'localhost'; // адрес сервера
    $database = 'u1332589_ezooby_shop'; // имя базы данных
    $user = 'u1332589_default'; // имя пользователя
    $password = '3zo7H_bO8Kxf'; // пароль

    // подключаемся к серверу
    $link = mysqli_connect($host, $user, $password, $database)
    or die("Ошибка " . mysqli_error($link));

// выполняем операции с базой данных
    $query ="SELECT * FROM s_bonus_cods";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    if($result)
    {
        echo "Выполнение запроса прошло успешно";


    }

// закрываем подключение
    mysqli_close($link);
}
?>