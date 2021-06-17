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

$catIscl=[766514,766823,766824,766825,766826,766827,766828,766516,766561,766832,766560,766562,766563,766830,766831,766833,766564,766835,766836,766887,766837,766838,766839,766840,766841,766842,766843,766844,
    766845,766846,766847,766848,766849,766850,766851,766765,766934,766935,766936,766937,766531,766599,766600,766938,766601,766834,766939,766940,766604,766602,766603,766888,766889,766890,766891,766892,766893,
    766894,766895,766896,766897,766898,766899,766900,766901,766902,766903,766904,766625,766991,766992,766993,766994,766995,766996,766997,766998,766999,766635,767035,767036,767037,767038,767039,767040,767041];
//Создает XML-строку и XML-документ при помощи DOM
$dom = new DomDocument('1.0', 'utf-8');
//добавление корня - <books>
$yml_catalog = $dom->appendChild($dom->createElement('yml_catalog'));
$yml_catalog->setAttributeNode(new DOMAttr('date', date('Y-m-d H:i')));
//добавление элемента <book> в <books>
$shop = $yml_catalog->appendChild($dom->createElement('shop'));
// добавление элемента <title> в <book>
// добавление элемента текстового узла <title> в <title>
// Валюты

$name = $shop->appendChild($dom->createElement('name'));
$name->appendChild($dom->createTextNode($simpla->settings->site_name));
$company = $shop->appendChild($dom->createElement('company'));
$company->appendChild($dom->createTextNode($simpla->settings->company_name));
$url = $shop->appendChild($dom->createElement('url'));
//$url->appendChild($dom->createTextNode($simpla->config->root_url));
$url->appendChild($dom->createTextNode('https://e-zoo.by/'));

$currencies_val = $simpla->money->get_currencies(array('enabled'=>1));
$main_currency = reset($currencies_val);

$currencies = $shop->appendChild($dom->createElement('currencies'));

foreach ($currencies_val as $c) {
    if ($c->enabled) {
        $currency = $currencies->appendChild($dom->createElement('currency'));
        $currency->setAttributeNode(new DOMAttr('id', $c->code));
        $currency->setAttributeNode(new DOMAttr('rate', $c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to));
    }
}

// Категории
$categories = $shop->appendChild($dom->createElement('categories'));
$categories_val = $simpla->categories->get_categories();
foreach ($categories_val as $c) {
    $category = $categories->appendChild($dom->createElement('category'));
    $category->appendChild($dom->createTextNode(htmlspecialchars($c->name)));
    $category->setAttributeNode(new DOMAttr('id', $c->id));
    if ($c->parent_id>0) {
        $category->setAttributeNode(new DOMAttr('parentId', $c->parent_id));
    }
}


$offers = $shop->appendChild($dom->createElement('offers'));

// Товары
$simpla->db->query("SET SQL_BIG_SELECTS=1");

$simpla->db->query("SELECT v.price, v.compare_price, v.id AS variant_id, p.name AS product_name, p.name_yan_market AS name_yan_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, cat.name AS cat_name, cat.name_cat_market AS name_cat_market, i.filename AS image, b.name AS brand, b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __categories cat ON cat.id = pc.category_id	
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)	
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) GROUP BY v.id ORDER BY p.id, v.position ");

$currency_code = reset($currencies_val)->code;

// В цикле мы используем не results(), a result(), то есть выбираем из базы товары по одному,
// так они нам одновременно не нужны - мы всё равно сразу же отправляем товар на вывод.
// Таким образом используется памяти только под один товар
$prev_product_id = null;
$countProd = 0;
$products = $simpla->db->results();
foreach ($products as $p) {
    if (in_array($p->category_id,$catIscl)){
        continue;
    }
    $countProd++;
        $variant_url = '';
        $variants = $simpla->variants->get_variants(array('product_id' => $p->product_id,'in_stock'=>"Y"));
        if (count($variants)>1) {
            $variant_url = '?variant='.$p->variant_id;
        }
        $prev_product_id = $p->product_id;

    $offer = $offers->appendChild($dom->createElement('offer'));
    $offer->setAttributeNode(new DOMAttr('id', $p->variant_id));

    $brandName = '';
    if($p->brand) {$brandName = $p->brand;}
    if($p->brand_clear_name) {$brandName = $p->brand_clear_name;}

    $cat_type = '';
    if ($p->name_cat_market){ $cat_type = $p->name_cat_market.' ';}
    $name = $offer->appendChild($dom->createElement('name'));
    $nameModel = ($p->name_yan_market) ? htmlspecialchars($p->name_yan_market) : htmlspecialchars($p->product_name);
    $nameModel = $cat_type.($brandName?$brandName.' ':'').$nameModel.($p->variant_name?' '.htmlspecialchars($p->variant_name):'');
    $name->appendChild($dom->createTextNode($nameModel));

    $vendor = $offer->appendChild($dom->createElement('vendor'));
    $vendor->appendChild($dom->createTextNode($brandName));

    $url = $offer->appendChild($dom->createElement('url'));
    //$url->appendChild($dom->createTextNode($simpla->config->root_url.'/products/'.$p->url.$variant_url));
    $url->appendChild($dom->createTextNode('https://e-zoo.by/products/'.$p->url.$variant_url));

    $price_val = $p->price;
    if ($price_val>$p->compare_price && $brandName!='') {
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
    $price = $offer->appendChild($dom->createElement('price'));
    $price->appendChild($dom->createTextNode($price_val));

    $currencyId = $offer->appendChild($dom->createElement('currencyId'));
    $currencyId->appendChild($dom->createTextNode($currency_code));

    $categoryId = $offer->appendChild($dom->createElement('categoryId'));
    $categoryId->appendChild($dom->createTextNode($p->category_id));

    $picture = $offer->appendChild($dom->createElement('picture'));
    $imgUrl = $simpla->design->resize_modifier($p->image, 600, 600);
    $imgUrl = str_replace("http:///public_html", "https://e-zoo.by", $imgUrl);
    $picture->appendChild($dom->createTextNode($imgUrl));

    $delivery = $offer->appendChild($dom->createElement('delivery'));
    $delivery->appendChild($dom->createTextNode('true'));

    $pickup = $offer->appendChild($dom->createElement('pickup'));
    $pickup->appendChild($dom->createTextNode('true'));

    $description = $offer->appendChild($dom->createElement('description'));
    $description->appendChild($dom->createTextNode('<![CDATA[ '.$p->body.']]>'));

    $store = $offer->appendChild($dom->createElement('store'));
    $store->appendChild($dom->createTextNode('false'));

    if (!empty($p->product_id)) {
        $options = $simpla->features->get_product_options($p->product_id);

        foreach ($options as $option) {
            $param = $offer->appendChild($dom->createElement('param'));
            $param->appendChild($dom->createTextNode($option->value));
            $param->setAttributeNode(new DOMAttr('name', $option->name));
        }
    }
}

//генерация xml
$dom->formatOutput = true; // установка атрибута formatOutput
// domDocument в значение true
// save XML as string or file
$test1 = $dom->saveXML(); // передача строки в test1
$dom->save(dirname(__FILE__).'/yandex_products_price_isc.xml'); // сохранение файла
echo "Обработано ".$countProd.' товара';
echo '<br>Экспорт в файл выполнен';
echo '<br>Ссылка на файл : <a href="/yandex_products_price_isc.xml" download>Yandex_products_price_isc</a>';
