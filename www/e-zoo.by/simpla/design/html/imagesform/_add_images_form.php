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
<form name='form' enctype='multipart/form-data'
      method='post' action='add_images.php'>
    <p>
        <label class='label'>Выберите картинку</label>
        <br>
        <input type='file' name='myfile' id='myfile'
               class='input'/>
    </p>
    <br>
    <p>
    <table>
        <tr>
            <td>
                <input type='image'
                       src='img/add_images_save.png'
                       title='Сохранить'> /
            </td>
            <td>
                <a href='index.php' class='add_images'>
                    <div class='add_images_text'>НАЗАД</div></a>
            </td>
        </tr>
    </table>
    </p>
</form>
</body>
</html>