<?php
// Подключение к БД.
$dbh = new PDO('mysql:dbname=u1332589_ezooby_shop;host=localhost', 'u1332589_default', '3zo7H_bO8Kxf');
mysqli_query($dbh, "SET NAMES 'utf8'");
$sth = $dbh->prepare("SELECT * FROM `s_reviews` ORDER BY `date_add` DESC");
$sth->execute();
$items = $sth->fetchAll(PDO::FETCH_ASSOC);

if (!empty($items)) {
    ?>
    <h2>Отзывы</h2>
    <div class="reviews">
        <?php
        foreach ($items as $row) {
            $sth = $dbh->prepare("SELECT * FROM `s_reviews_images` WHERE `reviews_id` = ?");
            $sth->execute(array($row['id']));
            $images = $sth->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="reviews_item">
                <div class="reviews_item-name"><?php echo $row['name']; ?></div>
                <div class="reviews_item-text"><?php echo $row['text']; ?></div>
                <?php if (!empty($images)): ?>
                    <div class="reviews_item-images">
                        <?php foreach($images as $img): ?>
                            <div class="reviews_item-img">
                                <?php
                                $name = pathinfo($img['filename'], PATHINFO_FILENAME);
                                $ext = pathinfo($img['filename'], PATHINFO_EXTENSION);
                                ?>
                                <a href="/uploads_test/<?php echo $img['filename']; ?>" target="_blank">
                                    <img src="/uploads_test/<?php echo $name . '-thumb.' . $ext; ?>">
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
<style>
    .reviews_item {
        background: #efefef;
        padding: 15px 30px 0px 30px;
        margin-bottom: 20px;
    }
    .reviews_item-name {
        font-weight: 900;
        font-size: 18px;
        margin-bottom: 5px;
    }
    .reviews_item-text {
        margin-bottom: 15px;
        font-size: 15px;
        line-height: 1.5;
    }
    .reviews_item-img {
        display: inline-block;
        margin: 0 15px 15px 0;
    }
</style>
