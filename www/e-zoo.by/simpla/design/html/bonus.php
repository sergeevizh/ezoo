<!DOCTYPE html>
<html>
<head>
    <title>CSV File Editing and Importing in PHP</title>
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
    </style>
</head>
<body>
<div class="container">
    <br />
    <h3 align="center">Загрузите файл CSV</h3>
    <br />
    <form action="" id="upload_csv" method="post" enctype="multipart/form-data">
        <div class="col-md-3">
            <br />
            <label>Выберите файл CSV</label>
        </div>
        <div class="col-md-4">
            <input type="file" name="csv_file" id="csv_file" accept=".csv" style="margin-top:15px;" />
        </div>
        <div class="col-md-5">
            <input type="submit" name="upload" id="upload" value="Загрузить" style="margin-top:10px;" class="btn btn-info" />
        </div>
        <div style="clear:both"></div>
    </form>
    <br />
    <br />
    <div id="csv_file_data"></div>

</div>
</body>
</html>




<script>

    $(document).ready(function(){
        $('#upload_csv').on('submit', function(event){
            event.preventDefault();
            $.ajax({
                url:"fetch.php",
                method:"POST",
                data:new FormData(this),
                dataType:'json',
                contentType:false,
                cache:false,
                processData:false,
                success:function(data)
                {
                    var html = '<table class="table table-striped table-bordered">';
                    if(data.column)
                    {
                        html += '<tr>';
                        for(var count = 0; count < data.column.length; count++)
                        {
                            html += '<th>'+data.column[count]+'</th>';
                        }
                        html += '</tr>';
                    }

                    if(data.row_data)
                    {
                        for(var count = 0; count < data.row_data.length; count++)
                        {
                            html += '<tr>';
                            html += '<td class="id" contenteditable>'+data.row_data[count].id+'</td>';
                            html += '<td class="cod" contenteditable>'+data.row_data[count].cod+'</td>';
                            html += '<td class="active" contenteditable>'+data.row_data[count].active+'</td>';
                            html += '<td class="bonus_id" contenteditable>'+data.row_data[count].bonus_id+'</td></tr>';
                        }
                    }
                    html += '<table>';
                    html += '<div align="center"><button type="button" id="import_data" class="btn btn-success">Import</button></div>';

                    $('#csv_file_data').html(html);
                    $('#upload_csv')[0].reset();
                }
            })
        });

        $(document).on('click', '#import_data', function(){
            var id = [];
            var cod = [];
            var active = [];
            var bonus_id = [];
            $('.id').each(function(){
                id.push($(this).text());
            });
            $('.cod').each(function(){
                cod.push($(this).text());
            });
            $('.active').each(function(){
                active.push($(this).text());
            });
            $('.bonus_id').each(function(){
                bonus_id.push($(this).text());
            });

            $.ajax({
                url:"import.php",
                method:"post",
                data:{id:id, cod:cod, active:active, bonus_id:bonus_id},
                success:function(data)
                {
                    $('#csv_file_data').html('<div class="alert alert-success">Данные успешно загружены</div>');
                }
            })
        });
    });

</script>

