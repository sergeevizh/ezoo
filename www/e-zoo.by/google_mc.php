<?php

/**
 * Simpla CMS
 *
 * @copyright	2016 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */
require_once('api/Simpla.php');
$simpla = new Simpla();


//Создает XML-строку и XML-документ при помощи DOM
$dom = new DomDocument('1.0', 'utf-8');
//добавление корня - <books>
$rss = $dom->appendChild($dom->createElement('rss'));
$rss->setAttribute('version', '2.0');
$rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
//добавление элемента <book> в <books>
$channel = $rss->appendChild($dom->createElement('channel'));
// добавление элемента <title> в <book>
// добавление элемента текстового узла <title> в <title>
// Валюты

$title = $channel->appendChild($dom->createElement('title'));
$title->appendChild($dom->createTextNode($simpla->settings->site_name));
$link = $channel->appendChild($dom->createElement('link'));
//$url->appendChild($dom->createTextNode($simpla->config->root_url));
$link->appendChild($dom->createTextNode('https://e-zoo.by/'));
$descr = $channel->appendChild($dom->createElement('description'));
$descr->appendChild($dom->createTextNode($simpla->settings->company_name));


$currencies_val = $simpla->money->get_currencies(array('enabled'=>1));
$main_currency = reset($currencies_val);


// Товары
$simpla->db->query("SET SQL_BIG_SELECTS=1");

$simpla->db->query("SELECT v.price, v.compare_price, v.id AS variant_id, p.pickup,  p.name AS product_name, p.name_yan_market AS name_yan_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, cat.name AS cat_name, cat.name_cat_market AS name_cat_market, i.filename AS image, b.name AS brand, b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __categories cat ON cat.id = pc.category_id
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) AND p.pickup IS NULL GROUP BY v.id ORDER BY p.id, v.position ");

$currency_code = reset($currencies_val)->code;

// В цикле мы используем не results(), a result(), то есть выбираем из базы товары по одному,
// так они нам одновременно не нужны - мы всё равно сразу же отправляем товар на вывод.
// Таким образом используется памяти только под один товар
$prev_product_id = null;
$countProd = 0;
$products = $simpla->db->results();
foreach ($products as $p) {
    $countProd++;
    $variant_url = '';
    $variants = $simpla->variants->get_variants(array('product_id' => $p->product_id,'in_stock'=>"Y"));
    if (count($variants)>1) {
        $variant_url = '?variant='.$p->variant_id;
    }
    $prev_product_id = $p->product_id;

    $item = $channel->appendChild($dom->createElement('item'));

    $brandName = '';
    if($p->brand) {$brandName = $p->brand;}
    if($p->brand_clear_name) {$brandName = $p->brand_clear_name;}

    $cat_type = '';
    if ($p->name_cat_market){ $cat_type = $p->name_cat_market.' ';}



    $id = $item->appendChild($dom->createElement('g:id'));
    $id->appendChild($dom->createTextNode($p->variant_id));

    $name = $item->appendChild($dom->createElement('g:title'));
    $nameModel = ($p->name_yan_market) ? htmlspecialchars($p->name_yan_market) : htmlspecialchars($p->product_name);
    $nameModel = $cat_type.($brandName?$brandName.' ':'').$nameModel.($p->variant_name?' '.htmlspecialchars($p->variant_name):'');
    $name->appendChild($dom->createTextNode($nameModel));

    $description = $item->appendChild($dom->createElement('g:description'));
    $description->appendChild($dom->createTextNode('<![CDATA[ '.$p->body.']]>'));

    $url = $item->appendChild($dom->createElement('g:link'));
    //$url->appendChild($dom->createTextNode($simpla->config->root_url.'/products/'.$p->url.$variant_url));
    $url->appendChild($dom->createTextNode('https://e-zoo.by/products/'.$p->url));

    $picture = $item->appendChild($dom->createElement('g:image_link'));
    $imgUrl = $simpla->design->resize_modifier($p->image, 600, 600);
    $imgUrl = str_replace("http:///public_html", "https://e-zoo.by", $imgUrl);
    $picture->appendChild($dom->createTextNode($imgUrl));

    $price_val = $p->price;
    if ($price_val>$p->compare_price) {
        $productElem = $simpla->products->get_product(intval($p->product_id));
        $varintProd = $simpla->variants->get_variant($p->variant_id);
        if ($productElem && $varintProd){
            $variantSale = $simpla->cart->GetPriceForPiceList($productElem, $varintProd);
            if ($variantSale) {
                if ($variantSale['delivery']) {
                    $price_val = $variantSale['delivery'];
                }
            }
        }
    }
    $price = $item->appendChild($dom->createElement('g:price'));
    $price->appendChild($dom->createTextNode($price_val.' '.$currency_code));

    $availability = $item->appendChild($dom->createElement('g:availability'));
    $availability->appendChild($dom->CreateTextNode('in stock'));

    $brand = $item->appendChild($dom->createElement('g:brand'));
    $brand->appendChild($dom->createTextNode($brandName));

    $gtin = $item->appendChild($dom->createElement('g:gtin'));
    $gtin->appendChild($dom->createTextNode($p->variant_sku));
}

//генерация xml
$dom->formatOutput = true; // установка атрибута formatOutput
// domDocument в значение true
// save XML as string or file
$test1 = $dom->saveXML(); // передача строки в test1
$dom->save(dirname(__FILE__).'/google_mc_feed.xml'); // сохранение файла
echo "Обработано ".$countProd.' товара';
echo '<br>Экспорт в файл выполнен';
echo '<br>Ссылка на файл : <a href="/google_mc_feed.xml" download>google_mc_feed</a>';
