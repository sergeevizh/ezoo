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
     $folder = "$foldername/$filename";

    $row = move_uploaded_file($tmpname, "$foldername/$filename");

    if ($row) {
        echo "succesffully uploaded";
    } else {
        echo "failed to upload";
    }

    $file = fopen($folder, "r");

    $row=0;
    $productData = array();
    while (($data = fgetcsv($file, 0, ",")) !== FALSE) {
        if($row > 0){
            $productData[] = $data;
        }
        $row++;
    }

/*    echo "<pre>";
    print_r(json_encode($productData));
    echo "<pre>";*/

   // echo json_encode($productData);

  //  header('Content-type: application/json');

//Set your file path here
    $filePath = $folder;

// define two arrays for storing values
    $keys = array();
    $newArray = array();

//PHP Function to convert CSV into array
    function convertCsvToArray($file, $delimiter) {
        if (($handle = fopen($file, 'r')) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
                for ($j = 0; $j < count($lineArray); $j++) {
                    $arr[$i][$j] = $lineArray[$j];
                }
                $i++;
            }
            fclose($handle);
        }
        return $arr;
    }
// Call the function convert csv To Array
    $data = convertCsvToArray($filePath, ',');

// Set number of elements (minus 1 because we shift off the first row)
    $count = count($data) - 1;

//First row for label or name
    $labels = array_shift($data);
    foreach ($labels as $label) {
        $keys[] = $label;

    }

// assign keys value to ids, we add new parameter id here
    $keys[] = 'id';

    for ($i = 0; $i < $count; $i++) {
        $data[$i][] = $i;
    }

// combine both array
    for ($j = 0; $j < $count; $j++) {
        $d = array_combine($keys, $data[$j]);
        $newArray[$j] = $d;
    }


// convert array to json php using the json_encode()
    $arrayToJson = json_encode($newArray);

// print converted csv value to json
    echo $arrayToJson;
}
?>