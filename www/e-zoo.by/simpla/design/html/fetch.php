<?php


if (isset($_POST['submit'])) {

    /* $foldername=$_POST['foldername'];*/

    $foldername = $_SERVER["DOCUMENT_ROOT"] . "/bonus";

    $filename = $_FILES['file']['name'];

    $tmpname = $_FILES['file']['tmp_name'];

    if (!file_exists($foldername)) {
        $result = mkdir($foldername, 0777);
    }

    $row = move_uploaded_file($tmpname, "$foldername/$filename");

    if ($row) {
        echo "succesffully uploaded";
    } else {
        echo "failed to upload";
    }

}


if(!empty($_FILES['csv_file']['name']))
{
    $file_data = fopen($_FILES['csv_file']['tmp_name'], 'r');
    $column = fgetcsv($file_data,0, ";");




    while($row = fgetcsv($file_data,0, ";"))
    {

        $row_data[] = array(
            'id' => $row[0],
            'cod'  => $row[1],
            'active'  => $row[2],
            'bonus_id' => $row[3]
        );



    }
    $output = array(
        'column'  => $column,
        'row_data'  => $row_data
    );

    echo json_encode($output);

}

?>
