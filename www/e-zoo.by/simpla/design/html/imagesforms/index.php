<?php
echo"<a href='add_images_form.php' class='add_images'>
<div class='add_images_text'>ДОБАВИТЬ КАРТИНКУ</div>
</a><br><br>";
include ("bd.php"); //подключение к базе данных

$sql = mysql_query("SELECT id, title, textSmall, textBig, imgSmall FROM 3_images");
// Выбор из базы данных полей id и title и textSmall и textBig и imgSmall и imgBig

if (!$sql)
{
    exit();
}
if (mysql_num_rows($sql) > 0)
{

    @$row=mysql_fetch_array($sql);

    $i=1;

    do
    {

        echo "<table><tr><td valign='top'>";
        echo $i++;
        echo "<td>";
        echo "<img src=$row[imgSmall] class='img'/>";
        echo "</td></tr></table><br>";
    }
    while (@$row = mysql_fetch_array($sql));
}
else
{
    echo "<label class='label'>В базе данных нет 
добавленных картинок!</label>";
    exit();
}
?>