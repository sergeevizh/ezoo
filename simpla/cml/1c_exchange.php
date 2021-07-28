<?php

// Папка для хранения временных файлов синхронизации

$dir = 'simpla/cml/temp/';

// Обновлять все данные при каждой синхронизации

$full_update = false;
$category_id = 766641;
$id_price_type = '9e4023ee-b2d8-11e6-bc49-f46d04020c71';
//основной склад
$main_sklad = '536c9e66-7b36-11ea-898d-782bcb4982f1';

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

if ($simpla->request->get('type') == 'sale' && $simpla->request->get('mode') == 'query') {
    $no_spaces = '<?xml version="1.0" encoding="utf-8"?>
							<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . date('Y-m-d') . '"></КоммерческаяИнформация>';
    $xml = new SimpleXMLElement($no_spaces);

    $orders = $simpla->orders->get_orders(array('modified_since' => $simpla->settings->last_1c_orders_export_date));
    if (isset($_GET['debug'])) {
        $orders = $simpla->orders->get_orders(array('id' => array(832, 827)));
    } else {
        $orders = $simpla->orders->get_orders();
    }


    $currency = $simpla->money->get_currency();
    foreach ($orders as $order) {
        $date = new DateTime($order->date);

        $doc = $xml->addChild("Документ");
        $doc->addChild("Ид", $order->id);
        $doc->addChild("Номер", $order->id);
        $doc->addChild("Дата", $date->format('Y-m-d'));
        $doc->addChild("ХозОперация", "Заказ товара");
        $doc->addChild("Роль", "Продавец");
        $doc->addChild("Валюта", $currency->code);
        $doc->addChild("Курс", "1");
        /*        if($order->coupon_discount > 0) {
                    $doc->addChild("Скидка",  $order->coupon_discount);
                }*/
        $doc->addChild("Сумма", $order->total_price);
        $doc->addChild("Время", $date->format('H:i:s'));
        $doc->addChild("Комментарий", $order->comment);


        // Контрагенты
        $k1 = $doc->addChild('Контрагенты');
        $k1_1 = $k1->addChild('Контрагент');
        $k1_2 = $k1_1->addChild("Ид", $order->name);
        $k1_2 = $k1_1->addChild("Наименование", $order->name);
        $k1_2 = $k1_1->addChild("Роль", "Покупатель");
        $k1_2 = $k1_1->addChild("ПолноеНаименование", $order->name);

        // Доп параметры
        $addr = $k1_1->addChild('АдресРегистрации');
        $addr->addChild('Представление', $order->address);
        $addrField = $addr->addChild('АдресноеПоле');
        $addrField->addChild('Тип', 'Страна');
        $addrField->addChild('Значение', 'RU');
        $addrField = $addr->addChild('АдресноеПоле');
        $addrField->addChild('Тип', 'Регион');
        $addrField->addChild('Значение', $order->address);

        $contacts = $k1_1->addChild('Контакты');
        $cont = $contacts->addChild('Контакт');
        $cont->addChild('Тип', 'Телефон');
        $cont->addChild('Значение', $order->phone);
        $cont = $contacts->addChild('Контакт');
        $cont->addChild('Тип', 'Почта');
        $cont->addChild('Значение', $order->email);


        $purchases = $simpla->orders->get_purchases(array('order_id' => intval($order->id)));
        $total_purchases = 0;
        $total_without_discount = 0;
        foreach ($purchases as $purchase) {
            $total_purchases += $purchase->amount;
            $total_without_discount += $purchase->price * $purchase->amount;
        }

        $coupon_discount = 0;
        $coupon_discount_for_purchase = 0;

        if ($order->coupon_discount > 0) {

            $coupon_discount = $order->coupon_discount;
            $coupon_discount_for_purchase = $coupon_discount / $total_purchases;
            $coupon_discount_percent = round($coupon_discount / ($total_without_discount / 100), 2);

            $diff_total = $order->total_price - ($total_without_discount * (100 - $coupon_discount_percent) / 100);
            if (isset($_GET['debug'])) {

                // echo ( $total_without_discount*(100-$coupon_discount_percent)/100). PHP_EOL;
                /*           echo $order->total_price. PHP_EOL;
                           echo $coupon_discount_percent. PHP_EOL;
                           echo ( $total_without_discount*(100-$coupon_discount_percent)/100). PHP_EOL;*/
            }

        }


        $t1 = $doc->addChild('Товары');
        foreach ($purchases as $purchase) {

            if (!empty($purchase->product_id) && !empty($purchase->variant_id)) {
                $simpla->db->query('SELECT external_id FROM __products WHERE id=?', $purchase->product_id);
                $id_p = $simpla->db->result('external_id');
                $simpla->db->query('SELECT external_id FROM __variants WHERE id=?', $purchase->variant_id);
                $id_v = $simpla->db->result('external_id');

                // Если нет внешнего ключа товара - указываем наш id
                if (!empty($id_p)) {
                    $id = $id_p;
                } else {
                    $simpla->db->query('UPDATE __products SET external_id=id WHERE id=?', $purchase->product_id);
                    $id = $purchase->product_id;
                }

                // Если нет внешнего ключа варианта - указываем наш id
                if (!empty($id_v)) {
                    $id = $id . '#' . $id_v;
                } else {
                    $simpla->db->query('UPDATE __variants SET external_id=id WHERE id=?', $purchase->variant_id);
                    $id = $id . '#' . $purchase->variant_id;
                }

                $t1_1 = $t1->addChild('Товар');

                if ($id) {
                    $t1_2 = $t1_1->addChild("Ид", $id);
                }

                $t1_2 = $t1_1->addChild("Артикул", $purchase->sku);

                $name = $purchase->product_name;
                if ($purchase->variant_name) {
                    $name .= " $purchase->variant_name $id";
                }
                $t1_2 = $t1_1->addChild("Наименование", htmlspecialchars($name));
                $t1_2 = $t1_1->addChild("ЦенаЗаЕдиницу", ($purchase->price * (100 - $order->discount) / 100));
                //$t1_2 = $t1_1->addChild("ЦенаЗаЕдиницу", ($purchase->price*(100-$order->discount)/100) -$coupon_discount_for_purchase );
                $t1_2 = $t1_1->addChild("Количество", $purchase->amount);
                $t1_2 = $t1_1->addChild("Сумма",
                    ($purchase->amount * $purchase->price * (100 - $order->discount) / 100));
                //$t1_2 = $t1_1->addChild("Сумма", ($purchase->amount*$purchase->price*(100-$order->discount)/100) - ($coupon_discount_for_purchase*$purchase->amount) ) ;

                $d = $t1_1->addChild('Скидки');
                if ($coupon_discount_percent > 0) {
                    $discount_num = round($simpla->money->convert($purchase->price, $currency->id,
                            false) * (1 - (100 - $coupon_discount_percent) / 100), 2);
                    $discount = $d->addChild('Скидка');
                    $discount->addChild('Наименование', 'Скидка ' . $coupon_discount_percent . '%');
                    $discount->addChild('Сумма', $discount_num);
                    $discount->addChild('Процент', $coupon_discount_percent);
                    $discount->addChild('УчтеноВСумме', 'false');
                }
                /*
                if($coupon_discount_for_purchase > 0) {
                    $discount = $d->addChild('Скидка');
                    $discount->addChild('Наименование', 'Скидка '.$coupon_discount_for_purchase*$purchase->amount. $currency->sign);
                    $discount->addChild('Сумма',  $coupon_discount_for_purchase*$purchase->amount);
                    //$discount->addChild('Процент', $order->discount);
                    $discount->addChild('УчтеноВСумме', 'false');
                }*/

                /*                $d   = $t1_1->addChild('Скидки');
                                $discount = 0;
                                // скидка в %
                                if($order->discount > 0) {
                                    $discount_num = round($simpla->money->convert($purchase->price, $currency->id, false), 2) * (1-(100 - $order->discount) / 100);
                                    $discount = $d->addChild('Скидка');
                                    $discount->addChild('Наименование', 'Скидка '.$order->discount. '%');
                                    $discount->addChild('Сумма',  $discount_num);
                                    $discount->addChild('Процент', $order->discount);
                                    $discount->addChild('УчтеноВСумме', 'true');
                                }*/
                /*
                $t1_2 = $t1_1->addChild ( "Скидки" );
                $t1_3 = $t1_2->addChild ( "Скидка" );
                $t1_4 = $t1_3->addChild ( "Сумма", $purchase->amount*$purchase->price*(100-$order->discount)/100);
                $t1_4 = $t1_3->addChild ( "УчтеноВСумме", "true" );
                */

                $t1_2 = $t1_1->addChild("ЗначенияРеквизитов");
                $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
                $t1_4 = $t1_3->addChild("Наименование", "ВидНоменклатуры");
                $t1_4 = $t1_3->addChild("Значение", "Товар");

                $t1_2 = $t1_1->addChild("ЗначенияРеквизитов");
                $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
                $t1_4 = $t1_3->addChild("Наименование", "ТипНоменклатуры");
                $t1_4 = $t1_3->addChild("Значение", "Товар");
            }
        }

        // Доставка
        if ($order->delivery_price > 0 && !$order->separate_delivery) {
            $t1 = $t1->addChild('Товар');
            $t1->addChild("Ид", 'ORDER_DELIVERY');
            $t1->addChild("Наименование", 'Доставка');
            $t1->addChild("ЦенаЗаЕдиницу", $order->delivery_price);
            $t1->addChild("Количество", 1);
            $t1->addChild("Сумма", $order->delivery_price);
            $t1_2 = $t1->addChild("ЗначенияРеквизитов");
            $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
            $t1_4 = $t1_3->addChild("Наименование", "ВидНоменклатуры");
            $t1_4 = $t1_3->addChild("Значение", "Услуга");

            $t1_2 = $t1->addChild("ЗначенияРеквизитов");
            $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
            $t1_4 = $t1_3->addChild("Наименование", "ТипНоменклатуры");
            $t1_4 = $t1_3->addChild("Значение", "Услуга");
        }

        // Способ оплаты и доставки
        $s1_2 = $doc->addChild("ЗначенияРеквизитов");

        $payment_method = $simpla->payment->get_payment_method($order->payment_method_id);
        $delivery = $simpla->delivery->get_delivery($order->delivery_id);

        if ($payment_method) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Метод оплаты");
            $s1_3->addChild("Значение", $payment_method->name);
        }
        if ($delivery) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Способ доставки");
            $s1_3->addChild("Значение", $delivery->name);
        }
        $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
        $s1_3->addChild("Наименование", "Заказ оплачен");
        $s1_3->addChild("Значение", $order->paid ? 'true' : 'false');


        // Статус
        if ($order->status == 0) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Статус заказа");
            $s1_3->addChild("Значение", "Новый");
        }
        if ($order->status == 1) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Статус заказа");
            $s1_3->addChild("Значение", "[N] Принят");
        }
        if ($order->status == 2) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Статус заказа");
            $s1_3->addChild("Значение", "[F] Доставлен");
        }
        if ($order->status == 3) {
            $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
            $s1_3->addChild("Наименование", "Отменен");
            $s1_3->addChild("Значение", "true");
        }
    }

    header("Content-type: text/xml; charset=utf-8");
    print "\xEF\xBB\xBF";

    print $xml->asXML();

    //$simpla->settings->last_1c_orders_export_date = date("Y-m-d H:i:s");
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
    unset($_SESSION['VAR']);
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


        // обработка костылями варианты
        if (!empty($_SESSION['VAR'])) {

            foreach ($_SESSION['VAR'] as $group_id => $xml_variants) {

                $external_ids = array_keys($xml_variants);
                // нужно попробовать найти по набору external_ids товар или варинат
                $simpla->db->query('SELECT id FROM __products WHERE external_id IN(?@) LIMIT 1', (array)$external_ids);
                $product_id = $simpla->db->result('id');


                // Если такого товара не нашлось, добавим его
                if (empty($product_id)) {
                    $xml_product = reset($xml_variants);
                    @list($product_1c_id, $variant_1c_id) = explode('#', $xml_product->Ид);
                    $description = '';
                    if (!empty($xml_product->Описание)) {
                        $description = $xml_product->Описание;
                    }
                    $product_id = $simpla->products->add_product(
                        array(
                            'external_id' => $product_1c_id,
                            'url' => translit($xml_product->Наименование),
                            'name' => $xml_product->Наименование,
                            'meta_title' => $xml_product->Наименование,
                            'meta_keywords' => $xml_product->Наименование,
                            'meta_description' => $description,
                            'visible' => 0,
                            'annotation' => $description,
                            'body' => $description,
                        )
                    );


                    // добавляем в категорию новая номенклатура
                    $simpla->categories->add_product_category($product_id, $category_id);
                }

                foreach ($xml_variants as $product_1c_id => $xml_variant) {
                    $variant = new stdClass();

                    $simpla->db->query('SELECT id FROM __variants WHERE external_id=? LIMIT 1', $product_1c_id);
                    $variant_id = $simpla->db->result('id');

                    if (empty($variant_id)) {
                        $variant = new stdClass();
                        $variant->stock = 0;
                        if (!empty($xml_variant->Штрихкод)) {
                            $variant->sku = trim($xml_variant->Штрихкод);
                        }

                        $variant->name = trim($xml_variant->Наименование);
                        $variant->product_id = $product_id;
                        $variant->external_id = $product_1c_id;


                        $variant_id = $simpla->variants->add_variant($variant);
                        $simpla->variants->update_variant($variant_id, array('position' => $variant_id));
                    } else {
                        $variant = new stdClass();
                        $variant->stock = 0;
                        if (!empty($xml_variant->Штрихкод)) {
                            $variant->sku = trim($xml_variant->Штрихкод);
                        }
                        $variant->product_id = $product_id;
                        $variant->external_id = $product_1c_id;

                        $simpla->variants->update_variant($variant_id, $variant);
                    }
                }
            }
            unset($_SESSION['VAR']);
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
    $variant_id = null;
    $product_id = null;

    // Если мы точно видим что это товар с вариантами
    if (!empty($xml_product->VAR) && trim($xml_product->VAR) == '999') {
        return;
    } elseif (!empty($xml_product->VAR) && trim($xml_product->VAR) != '999') {
        $_SESSION['VAR'][trim($xml_product->VAR)][$product_1c_id] = $xml_product;
        return;
    } else {
        // Ищем товар, этот товар без вариантов
        $simpla->db->query('SELECT id FROM __products WHERE external_id=?', $product_1c_id);
        $product_id = $simpla->db->result('id');
        // Если такого товара не нашлось, добавим его
        if (empty($product_id)) {
            $description = '';
            if (!empty($xml_product->Описание)) {
                $description = $xml_product->Описание;
            }
            $product_id = $simpla->products->add_product(
                array(
                    'external_id' => $product_1c_id,
                    'url' => translit($xml_product->Наименование),
                    'name' => $xml_product->Наименование,
                    'meta_title' => $xml_product->Наименование,
                    'meta_keywords' => $xml_product->Наименование,
                    'meta_description' => $description,
                    'visible' => 0,
                    'annotation' => $description,
                    'body' => $description,
                )
            );
            // добавляем в категорию новая номенклатура
            $simpla->categories->add_product_category($product_id, $category_id);
        }

        $simpla->db->query('SELECT id FROM __variants WHERE external_id=? LIMIT 1', $product_1c_id);
        $variant_id = $simpla->db->result('id');

        if (empty($variant_id)) {
            $variant = new stdClass();
            $variant->stock = 0;
            if (!empty($xml_product->Штрихкод)) {
                $variant->sku = trim($xml_product->Штрихкод);
            }
            //Акционный

            $variant->product_id = $product_id;
            $variant->external_id = $product_1c_id;
            $variant_id = $simpla->variants->add_variant($variant);
            $simpla->variants->update_variant($variant_id, array('position' => $variant_id));
        } else {
            $variant = new stdClass();
            $variant->stock = 0;
            $variant->compare_price = null;

            if (!empty($xml_product->Штрихкод)) {
                $variant->sku = trim($xml_product->Штрихкод);
            }

            $variant->product_id = $product_id;
            $variant->external_id = $product_1c_id;
            $simpla->variants->update_variant($variant_id, $variant);
        }

    }

    /*    if ($xml_product->Статус == 'Удален') {
    $simpla->variants->delete_variant($variant_id);
    $simpla->db->query('SELECT count(id) as variants_num FROM __variants WHERE product_id=?', $product_id);
    if ($simpla->db->result('variants_num') == 0) {
    $simpla->products->delete_product($product_id);
    }
    }*/

}

function import_variant($xml_variant)
{

    global $simpla;

    global $dir;
    global $id_price_type;
    global $main_sklad;

    $variant = new stdClass;

    //  Id товара и варианта (если есть) по 1С

    @list($product_1c_id, $variant_1c_id) = explode('#', $xml_variant->Ид);

    if (empty($variant_1c_id)) {

        $variant_1c_id = '';

    }

    if (empty($product_1c_id)) {
        return false;
    }


    //регионы
    /* update 26.08.2020 */
    $query = "SELECT * FROM __regions ORDER BY id";
    $simpla->db->query($query);

    foreach ($simpla->db->results() as $region) {
        $regions[$region->code_is][] = $region;
    }
    /* update 26.08.2020 */





    $simpla->db->query('SELECT id FROM __variants WHERE external_id=?', $product_1c_id);
    $variant_id = $simpla->db->result('id');
    // если нашли варинат, обновляем его
    if (!empty($variant_id)) {
        $compare_price = false;
        if (trim($xml_variant->Акционный) === 'Да') {
            $compare_price = true;


        }
        foreach ($xml_variant->Цены->Цена as $key => $value) {

            if ($value->ИдТипаЦены == $id_price_type) {
                $variant->compare_price = ($compare_price) ? (trim($value->ЦенаЗаЕдиницу) * 1.2) : null;
                $variant->price = trim($value->ЦенаЗаЕдиницу);
                /*                if($xml_variant->Ид == 'c77aedc6-7a42-11e6-a743-f46d04020c71'){
                                    print_r($variant);
                                    print_r($xml_variant);
                                }*/

                break;
            }
        }

        /* $variant->stock = trim($xml_variant->Количество); */
            /* обновляем количество по складам */
        unset($key);
        foreach ($xml_variant->ОстаткиПоСкладам->ОстатокПоСкладу as $key => $value_st) {

            //главный склад
            if( $value_st->Ид == $main_sklad) {
                $variant->stock = trim($value_st->Остаток);
            }
            else{
                //нужно по регионам
                /* update 26.08.2020 */
                foreach ($regions[(string)$value_st->Ид] as $region) {
                    unset($r_id);
                    $r_id = 'region_stock_' . $region->id;

                    if ($r_id != 'region_stock_') {
                        $variant->{$r_id} = trim($value_st->Остаток);
                    }

                }
                /* update 26.08.2020 */


            }
        }
        /* /обновляем количество по складам */

      /*   if( $variant_id == 332){
            print_r($variant);
        } */

        $simpla->variants->update_variant($variant_id, $variant);
    }


}

function translit($text)
{

    $ru = explode('-',
        "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я");

    $en = explode('-',
        "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

    $res = str_replace($ru, $en, $text);

    $res = preg_replace("/[\s]+/ui", '-', $res);

    $res = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $res));

    return $res;

}
