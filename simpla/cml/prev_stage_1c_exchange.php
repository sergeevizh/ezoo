<?php

// Папка для хранения временных файлов синхронизации

$dir = 'simpla/cml/temp/';

// Обновлять все данные при каждой синхронизации

$full_update = false;
$category_id = 766641;
$id_price_type = 'a2c7f3df-06ee-11e6-a1ba-541f3004fd0a';


// Название параметра товара, используемого как бренд

$brand_option_name = 'Производитель';

$start_time = microtime(true);

$max_exec_time = min(30, @ini_get("max_execution_time"));

if (empty($max_exec_time)) {

    $max_exec_time = 30;

}

session_start();

chdir('../..');

include 'api/Simpla.php';

$simpla = new Simpla();

if ($simpla->request->get('type') == 'sale' && $simpla->request->get('mode') == 'checkauth') {

    print "success\n";

    print session_name() . "\n";

    print session_id();

}

if ($simpla->request->get('type') == 'sale' && $simpla->request->get('mode') == 'init') {

    $tmp_files = glob($dir . '*.*');

    if (is_array($tmp_files)) {

        foreach ($tmp_files as $v) {

            //unlink($v);

        }

    }

    print "zip=no\n";

    print "file_limit=1000000\n";

}



if ($simpla->request->get('type') == 'catalog' && $simpla->request->get('mode') == 'checkauth') {
    print "success\n";
    print session_name() . "\n";
    print session_id();

}

if ($simpla->request->get('type') == 'catalog' && $simpla->request->get('mode') == 'init') {
    $tmp_files = glob($dir . '*.*');
    if (is_array($tmp_files)) {
        foreach ($tmp_files as $v) {
            unlink($v);
        }
    }

    unset($_SESSION['last_1c_imported_variant_num']);
    unset($_SESSION['last_1c_imported_product_num']);
    unset($_SESSION['features_mapping']);
    unset($_SESSION['categories_mapping']);
    unset($_SESSION['brand_id_option']);
    print "zip=no\n";
    print "file_limit=1000000\n";

}

if ($simpla->request->get('type') == 'catalog' && $simpla->request->get('mode') == 'file') {
    $filename = basename($simpla->request->get('filename'));
    $f = fopen($dir . $filename, 'ab');
    fwrite($f, file_get_contents('php://input'));
    fclose($f);
    print "success\n";
}

if ($simpla->request->get('type') == 'catalog' && $simpla->request->get('mode') == 'import') {

    $filename = basename($simpla->request->get('filename'));

    if (stristr($filename, 'import') !== false) {
/*        // Категории и свойства (только в первом запросе пакетной передачи)
        if (!isset($_SESSION['last_1c_imported_product_num'])) {
            $z = new XMLReader;
            $z->open($dir . $filename);
            while ($z->read() && $z->name !== 'Классификатор');
            $xml = new SimpleXMLElement($z->readOuterXML());
            $z->close();
            import_categories($xml);
            import_features($xml);
        }
*/
        // Товары
        $z = new XMLReader;
        $z->open($dir . $filename);
        while ($z->read() && $z->name !== 'Товар');
        // Последний товар, на котором остановились
        $last_product_num = 0;
        if (isset($_SESSION['last_1c_imported_product_num'])) {
            $last_product_num = $_SESSION['last_1c_imported_product_num'];
        }

        // Номер текущего товара

        $current_product_num = 0;

        while ($z->name === 'Товар') {
            if ($current_product_num >= $last_product_num) {
                $xml = new SimpleXMLElement($z->readOuterXML());

                // Товары

                import_product($xml);
                $exec_time = microtime(true) - $start_time;
                if ($exec_time + 1 >= $max_exec_time) {
                    header("Content-type: text/xml; charset=utf-8");
                    print "\xEF\xBB\xBF";
                    print "progress\r\n";
                    print "Выгружено товаров: $current_product_num\r\n";
                    $_SESSION['last_1c_imported_product_num'] = $current_product_num;
                    exit();
                }
            }

            $z->next('Товар');
            $current_product_num++;
        }

        $z->close();
        print "success";
        //unlink($dir.$filename);
        unset($_SESSION['last_1c_imported_product_num']);

    } elseif (stristr($filename, 'offers') !== false) {

        // Варианты

        $z = new XMLReader;

        $z->open($dir . $filename);

        while ($z->read() && $z->name !== 'Предложение');

        // Последний вариант, на котором остановились

        $last_variant_num = 0;

        if (isset($_SESSION['last_1c_imported_variant_num'])) {

            $last_variant_num = $_SESSION['last_1c_imported_variant_num'];

        }

        // Номер текущего товара

        $current_variant_num = 0;

        while ($z->name === 'Предложение') {

            if ($current_variant_num >= $last_variant_num) {

                $xml = new SimpleXMLElement($z->readOuterXML());

                // Варианты

                import_variant($xml);

                $exec_time = microtime(true) - $start_time;

                if ($exec_time + 1 >= $max_exec_time) {

                    header("Content-type: text/xml; charset=utf-8");

                    print "\xEF\xBB\xBF";

                    print "progress\r\n";

                    print "Выгружено ценовых предложений: $current_variant_num\r\n";

                    $_SESSION['last_1c_imported_variant_num'] = $current_variant_num;

                    exit();

                }

            }

            $z->next('Предложение');

            $current_variant_num++;

        }

        $z->close();

        print "success";

        //unlink($dir.$filename);

        unset($_SESSION['last_1c_imported_variant_num']);

    }

}


function import_product($xml_product)
{

    global $simpla;

    global $dir;

    global $brand_option_name;

    global $full_update;
    global $category_id;

    // Товары

    //  Id товара и варианта (если есть) по 1С

    @list($product_1c_id, $variant_1c_id) = explode('#', $xml_product->Ид);
    if (empty($variant_1c_id)) {
        $variant_1c_id = '';
    }

/*    // Ид категории

    if (isset($xml_product->Группы->Ид)) {
        $category_id = $_SESSION['categories_mapping'][strval($xml_product->Группы->Ид)];
    }
*/
    // Подгатавливаем вариант

    $variant_id = null;
    $variant = new stdClass;
    $values = array();
    if (isset($xml_product->ХарактеристикиТовара->ХарактеристикаТовара)) {
        foreach ($xml_product->ХарактеристикиТовара->ХарактеристикаТовара as $xml_property) {
            $values[] = $xml_property->Значение;
        }
    }

    if (!empty($values)) {
        $variant->name = implode(', ', $values);
    }

    $variant->sku = (string) $xml_product->Артикул;
    $variant->external_id = $variant_1c_id;

    // Ищем товар
    $simpla->db->query('SELECT id FROM __products WHERE external_id=?', $product_1c_id);
    $product_id = $simpla->db->result('id');

    if (empty($product_id) && !empty($variant->sku)) {
        $simpla->db->query('SELECT product_id, id FROM __variants WHERE sku=?', $variant->sku);
        $res = $simpla->db->result();
        if (!empty($res)) {
            $product_id = $res->product_id;
            $variant_id = $res->id;
        }
    }

    // Если такого товара не нашлось

    if (empty($product_id)) {

        // Добавляем товар

        $description = '';
        if (!empty($xml_product->Описание)) {
            $description = $xml_product->Описание;
        }

        $product_id = $simpla->products->add_product(
            array(
                'external_id'      => $product_1c_id,
                'url'              => translit($xml_product->Наименование),
                'name'             => $xml_product->Наименование,
                'meta_title'       => $xml_product->Наименование,
                'meta_keywords'    => $xml_product->Наименование,
                'meta_description' => $description,
                'visible'          => 0,
                'annotation'       => $description,
                'body'             => $description,
            )
        );

        // Добавляем товар в категории

        if (isset($category_id)) {
            $simpla->categories->add_product_category($product_id, $category_id);
        }

        // Добавляем изображение товара

        if (isset($xml_product->Картинка)) {
            foreach ($xml_product->Картинка as $img) {
                $image = basename($img);
                if (!empty($image) && is_file($dir . $image) && is_writable($simpla->config->original_images_dir)) {
                    rename($dir . $image, $simpla->config->original_images_dir . $image);
                    $simpla->products->add_image($product_id, $image);
                }
            }
        }
    }

    //Если нашелся товар

    else {

        if (empty($variant_id) && !empty($variant_1c_id)) {

            $simpla->db->query('SELECT id FROM __variants WHERE external_id=? AND product_id=?', $variant_1c_id, $product_id);

            $variant_id = $simpla->db->result('id');

        } elseif (empty($variant_id) && empty($variant_1c_id)) {

            $simpla->db->query('SELECT id FROM __variants WHERE product_id=?', $product_id);

            $variant_id = $simpla->db->result('id');

        }

        // Обновляем товар


        // Обновляем изображение товара

        if (isset($xml_product->Картинка)) {
            foreach ($xml_product->Картинка as $img) {
                $image = basename($img);
                if (!empty($image) && is_file($dir . $image) && is_writable($simpla->config->original_images_dir)) {
                    $simpla->db->query('SELECT id FROM __images WHERE product_id=? ORDER BY position LIMIT 1', $product_id);
                    $img_id = $simpla->db->result('id');
                    if (!empty($img_id)) {
                        $simpla->products->delete_image($img_id);
                    }
                    rename($dir . $image, $simpla->config->original_images_dir . $image);
                    $simpla->products->add_image($product_id, $image);
                }
            }
        }
    }

    // Если не найден вариант, добавляем вариант один к товару

    if (empty($variant_id)) {
        $variant->product_id = $product_id;
        $variant->stock = 0;
        $variant_id = $simpla->variants->add_variant($variant);
    } elseif (!empty($variant_id)) {
        $simpla->variants->update_variant($variant_id, $variant);
    }

    // Свойства товара



    // Если нужно - удаляем вариант или весь товар

    if ($xml_product->Статус == 'Удален') {

        $simpla->variants->delete_variant($variant_id);

        $simpla->db->query('SELECT count(id) as variants_num FROM __variants WHERE product_id=?', $product_id);

        if ($simpla->db->result('variants_num') == 0) {

            $simpla->products->delete_product($product_id);

        }

    }

}

function import_variant($xml_variant)
{

    global $simpla;

    global $dir;
    global $id_price_type;

    $variant = new stdClass;

    //  Id товара и варианта (если есть) по 1С

    @list($product_1c_id, $variant_1c_id) = explode('#', $xml_variant->Ид);

    if (empty($variant_1c_id)) {

        $variant_1c_id = '';

    }

    if (empty($product_1c_id)) {

        return false;

    }

    $simpla->db->query('SELECT v.id FROM __variants v WHERE v.external_id=? AND product_id=(SELECT p.id FROM __products p WHERE p.external_id=? LIMIT 1)', $variant_1c_id, $product_1c_id);

    $variant_id = $simpla->db->result('id');

    $simpla->db->query('SELECT p.id FROM __products p WHERE p.external_id=?', $product_1c_id);

    $variant->external_id = $variant_1c_id;

    $variant->product_id = $simpla->db->result('id');

    if (empty($variant->product_id)) {

        return false;

    }

    foreach ($xml_variant->Цены->Цена as $key => $value) {
    	if( $value->ИдТипаЦены == $id_price_type) {
    		$variant->price = $value->ЦенаЗаЕдиницу;
    		break;
    	}
    }



    if (isset($xml_variant->ХарактеристикиТовара->ХарактеристикаТовара)) {

        foreach ($xml_variant->ХарактеристикиТовара->ХарактеристикаТовара as $xml_property) {

            $values[] = $xml_property->Значение;

        }

    }

    if (!empty($values)) {

        $variant->name = implode(', ', $values);

    }

    $sku = (string) $xml_variant->Артикул;

    if (!empty($sku)) {

        $variant->sku = $sku;

    }

    // Конвертируем цену из валюты 1С в базовую валюту магазина

    if (!empty($xml_variant->Цены->Цена->Валюта)) {

        // Ищем валюту по коду

        $simpla->db->query("SELECT id, rate_from, rate_to FROM __currencies WHERE code like ?", $xml_variant->Цены->Цена->Валюта);

        $variant_currency = $simpla->db->result();

        // Если не нашли - ищем по обозначению

        if (empty($variant_currency)) {

            $simpla->db->query("SELECT id, rate_from, rate_to FROM __currencies WHERE sign like ?", $xml_variant->Цены->Цена->Валюта);

            $variant_currency = $simpla->db->result();

        }

        // Если нашли валюту - конвертируем из нее в базовую

        if ($variant_currency && $variant_currency->rate_from > 0 && $variant_currency->rate_to > 0) {

            $variant->price = floatval($variant->price) * $variant_currency->rate_to / $variant_currency->rate_from;

        }

    }

    $variant->stock = $xml_variant->Количество;

    if (empty($variant_id)) {

        $simpla->variants->add_variant($variant);

    } else {

        $simpla->variants->update_variant($variant_id, $variant);

    }

}

function translit($text)
{

    $ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я");

    $en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

    $res = str_replace($ru, $en, $text);

    $res = preg_replace("/[\s]+/ui", '-', $res);

    $res = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $res));

    return $res;

}
