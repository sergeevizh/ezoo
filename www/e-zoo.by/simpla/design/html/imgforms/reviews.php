<?php
// Подключение к БД.
$dbh = new PDO('mysql:dbname=u1332589_ezooby_shop;host=localhost', 'u1332589_default', '3zo7H_bO8Kxf');

$sth = $dbh->prepare("SELECT * FROM `s_bonus` ORDER BY `id` ASC ");
$sth->execute();
$items = $sth->fetchAll(PDO::FETCH_ASSOC);

if (!empty($items)) {
    ?>
    <h2>Отзывы</h2>
    <div class="reviews">
        <?php
        foreach ($items as $row) {
            $sth = $dbh->prepare("SELECT * FROM `s_bonus_images` WHERE `bonus_id` = ?");
            $sth->execute(array($row['id']));
            $images = $sth->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="reviews_item">
                <div class="reviews_item-name"><?php echo $row['title']; ?></div>
                <div class="reviews_item-text"><?php echo $row['textSmall']; ?></div>
                <div class="reviews_item-text"><?php echo $row['textBig']; ?></div>
                <?php if (!empty($images)): ?>
                    <div class="reviews_item-images">
                        <?php foreach($images as $img): ?>
                            <div class="reviews_item-img">
                                <?php
                                $name = pathinfo($img['filename'], PATHINFO_FILENAME);
                                $ext = pathinfo($img['filename'], PATHINFO_EXTENSION);
                                $names = pathinfo($img['filenames'], PATHINFO_FILENAME);
                                $exts = pathinfo($img['filenames'], PATHINFO_EXTENSION);

                                ?>
                                <a href="<?php echo $img['filename']; ?>" target="_blank">
                                    <!--<img src="<?php /*echo $img['filename']; */?>">-->
                                    <img width="300" height="450" src="uploads/<?=$name.".".$ext?>">
                                </a>
                                <a href="<?php echo $img['filenames']; ?>" target="_blank">
                                    <!--<img src="<?php /*echo $img['filename']; */?>">-->
                                    <img width="300" height="450" src="uploads/<?=$names.".".$exts?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
?>