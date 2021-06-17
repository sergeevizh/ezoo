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

<form method="post" action="/simpla/design/html/imgform/save_reviews.php" enctype='multipart/form-data'>
    <h3>Отправить отзыв:</h3>
    <div class="form-row">
        <label>Ваше имя:</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-row">
        <label>Комментарий:</label>
        <input type="text" name="text" required>
    </div>
    <div class="form-row">
        <label>Изображения:</label>
        <div class="img-list" id="js-file-list"></div>
        <input id="fileToUpload" type="file" name="fileToUpload" multiple accept=".jpg,.jpeg,.png,.gif">
    </div>
    <div class="form-submit">
        <input type="submit" name="send" value="Отправить">
    </div>
</form>



<style>
    .form-row {
        margin-bottom: 15px;
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