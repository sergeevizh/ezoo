<!DOCTYPE html>
<html>
<head>
    <title>Загрузка бонуса</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <style>
        .box
        {
            max-width:600px;
            width:100%;
            margin: 0 auto;;
        }
        .button{
            text-align: right;
        }
        .modal-title{
            font-weight: revert;
        }
    </style>
</head>
<body>
<div class="container">

        <div class="col-md-12 button">
            <input type="submit" name="modal"  value="Загрузить" data-toggle="modal" data-target="#exampleModal" style="margin-top:10px;" class="btn btn-info" />
        </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Создание бонуса</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -19px">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="upload_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="recipient-id" class="col-form-label">id:</label>
                            <input type="number" class="form-control" id="recipient-id">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">title:</label>
                            <textarea class="form-control" id="message-title"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">textSmall:</label>
                            <textarea class="form-control" id="message-textSmall"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">textBig:</label>
                            <textarea class="form-control" id="message-textBig"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">imgSmall :</label>
                            <input type="file" name="csv_file" id="img_file_small" accept=".txt,image/*" style="margin-top:15px;" />
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">imgBig :</label>
                            <input type="file" name="csv_file" id="img_file_big" accept=".txt,image/*" style="margin-top:15px;" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Создать бонус</button>
                </div>
            </div>
        </div>
    </div>

    <div id="form_file_data"></div>

</div>

<script>
    $(document).ready(function(){
        $(document).on('click', '.btn.btn-primary', function(){

            var id = $("#recipient-id").val();
            console.log(id);
            var title = $("#message-title").val();
            console.log(title);
            var textSmall = $("#message-textSmall").val();
            console.log(textSmall);
            var textBig = $("#message-textBig").val();
            console.log(textBig);
            var imgSmall = $("#img_file_small").val();
            console.log(imgSmall);
            var imgBig = $("#img_file_big").val();
            console.log(imgBig);

            $.ajax({
                url:"import_seet.php",
                method:"post",
                data:{id:id, title:title, textSmall:textSmall, textBig:textBig, imgSmall:imgSmall, imgBig:imgBig},
                success:function(data)
                {
                    $('#form_file_data').html('<div class="alert alert-success">Данные успешно загружены</div>');
                }
            })
    });

    });

</script>
</body>
</html>


