<?php

$error = '';

if(isset($_POST["upload_file"]))
{
    if($_FILES['file']['name'])
    {
        $file_array = explode(".", $_FILES['file']['name']);

        $file_name = $file_array[0];

        $extension = end($file_array);

        if($extension == 'csv')
        {
            $column_name = array();

            $final_data = array();

            $file_data = file_get_contents($_FILES['file']['tmp_name']);

            $data_array = array_map("str_getcsv", explode("\n", $file_data));

            $labels = array_shift($data_array);

            $foldername = $_SERVER["DOCUMENT_ROOT"] . "/bonus";

            $tmpname = $_FILES['file']['tmp_name'];
            $filename = $_FILES['file']['name'];


            foreach($labels as $label)
            {
                $column_name[] = $label;
            }

            $count = count($data_array) - 1;

            for($j = 0; $j < $count; $j++)
            {
                $data = array_combine($column_name, $data_array[$j]);

                $final_data[$j] = $data;
            }

            if (!file_exists($foldername)) {
                $result = mkdir($foldername, 0777);
            }
                $row = move_uploaded_file($tmpname, "$foldername/$filename");

            if ($row) {
                echo "succesffully uploaded";
            } else {
                echo "failed to upload";
            }
            // $row = move_uploaded_file($tmpname, "$foldername/$filename");
          /*  header('Content-disposition: attachment; filename='.$file_name.'.json');

            header('Content-type: application/json');*/

            echo "<pre>";
            print_r($final_data);
            echo "<pre>";


            exit;
        }
        else
        {
            $error = 'Only <b>.csv</b> file allowed';
        }
    }
    else
    {
        $error = 'Please Select CSV File';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Добавить файл</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://code.jquery.com/jquery.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <br />
    <br />
    <h1 align="center">Convert CSV to JSON using PHP</h1>
    <br />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Select CSV File</h3>
        </div>
        <div class="panel-body">
            <?php
            if($error != '')
            {
                echo '<div class="alert alert-danger">'.$error.'</div>';
            }
            ?>
            <form method="post" enctype="multipart/form-data">
                <div class="col-md-6" align="right">Select File</div>
                <div class="col-md-6">
                    <input type="file" name="file" />
                </div>
                <br /><br /><br />
                <div class="col-md-12" align="center">
                    <input type="submit" name="upload_file" class="btn btn-primary" value="Upload" />
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>