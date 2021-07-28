<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("memory_limit","1024M");
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
$price_list = $dom->appendChild($dom->createElement('price-list'));
//добавление элемента <book> в <books>
$settings = $price_list->appendChild($dom->createElement('settings'));
// добавление элемента <title> в <book>
// добавление элемента текстового узла <title> в <title>
// Валюты
$currencies = $simpla->money->get_currencies(array('enabled'=>1));
$main_currency = reset($currencies);
foreach ($currencies as $c) {
    if ($c->enabled) {
        $currency = $settings->appendChild($dom->createElement('currency'));
        $currency->appendChild($dom->createTextNode($c->code));
    }
}
$items_list = $price_list->appendChild($dom->createElement('items-list'));

// Товары
$simpla->db->query("SET SQL_BIG_SELECTS=1");
// Товары
/*$simpla->db->query("SELECT v.price, v.id AS variant_id, p.name AS product_name, p.name_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, pc.name_cat_market_onliner AS name_cat_market_onliner, pc.name AS name_cat,  i.filename AS image, b.name AS brand,  b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) GROUP BY v.id ORDER BY p.id, v.position ");*/

$simpla->db->query("SELECT v.price, v.compare_price, v.id AS variant_id, p.name AS product_name, p.name_yan_market AS name_market, p.name_onliner_market AS onliner_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, cat.name_cat_market_onliner AS name_cat_market_onliner, cat.name AS name_cat, i.filename AS image, b.name AS brand, b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __categories cat ON cat.id = pc.category_id
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) GROUP BY v.id ORDER BY p.id, v.position ");

$currency_code = reset($currencies)->code;

// В цикле мы используем не results(), a result(), то есть выбираем из базы товары по одному,
// так они нам одновременно не нужны - мы всё равно сразу же отправляем товар на вывод.
// Таким образом используется памяти только под один товар
$prev_product_id = null;
$countProd = 0;
$products = $simpla->db->results();
foreach ($products as $p) {
    $countProd++;
        $variant_url = '';
        if ($prev_product_id === $p->product_id) {
            $variant_url = '?variant='.$p->variant_id;
        }
        $prev_product_id = $p->product_id;

    $item = $items_list->appendChild($dom->createElement('item'));
    $category = $item->appendChild($dom->createElement('category'));
    $catName = '';
    if($p->name_cat) {$catName = $p->name_cat;}
    if($p->name_cat_market_onliner) {$catName = $p->name_cat_market_onliner;}
    $category->appendChild($dom->createTextNode($catName));

    $vendor = $item->appendChild($dom->createElement('vendor'));
    $brandName = '';
    if($p->brand) {$brandName = $p->brand;}
    if($p->brand_clear_name) {$brandName = $p->brand_clear_name;}
    $vendor->appendChild($dom->createTextNode($brandName));

    $model = $item->appendChild($dom->createElement('model'));
   /* $nameModel = ($p->name_market) ? htmlspecialchars($p->name_market) : htmlspecialchars($p->product_name);*/
    $nameModel = ($p->onliner_market) ? htmlspecialchars($p->onliner_market) : htmlspecialchars($p->product_name);
    $nameModel = $nameModel.($p->variant_name?' '.htmlspecialchars($p->variant_name):'');
    $model->appendChild($dom->createTextNode($nameModel));

    $article = $item->appendChild($dom->createElement('article'));
    $article->appendChild($dom->createTextNode(''));

    $id = $item->appendChild($dom->createElement('id'));
    $id->appendChild($dom->createTextNode($p->variant_id));


    $price_val = $p->price;
    if ($price_val>$p->compare_price) {
        $productElem = $simpla->products->get_product(intval($p->product_id));
        $varintProd = $simpla->variants->get_variant($p->variant_id);
        if ($productElem && $varintProd) {
            $variantSale = $simpla->cart->GetPriceForPiceList($productElem, $varintProd);
            if ($variantSale) {
                if ($variantSale['delivery']) {
                    $price_val = $variantSale['delivery'];
                }
            }
        } 
    }
    $price = $item->appendChild($dom->createElement('price'));
    $price->appendChild($dom->createTextNode($price_val));

    $currency = $item->appendChild($dom->createElement('currency'));
    $currency->appendChild($dom->createTextNode($currency_code));

    $comment = $item->appendChild($dom->createElement('comment'));
    //$comment->appendChild($dom->createTextNode('Отличные цены, принимаем карты рассрочки. Большой выбор кормов, лакомств, наполнителей, переносок и других зоотоваров. Профессиональная консультация и помощь в подборе. Аккуратные курьеры. Бесплатная доставка от 20 руб по г. Минск и 10 км за МКАД.'));
    $comment->appendChild($dom->createTextNode($simpla->settings->onliner_descr));

    $producer = $item->appendChild($dom->createElement('producer'));
    $producer->appendChild($dom->createTextNode($p->manufacturer));

    $importer = $item->appendChild($dom->createElement('importer'));
    $importer->appendChild($dom->createTextNode($p->importer));

    $serviceCenters = $item->appendChild($dom->createElement('serviceCenters'));
    $serviceCenters->appendChild($dom->createTextNode(''));

    $warranty = $item->appendChild($dom->createElement('warranty'));
    $warranty->appendChild($dom->createTextNode('0'));

    $deliveryTownTime = $item->appendChild($dom->createElement('deliveryTownTime'));
    $deliveryTownTime->appendChild($dom->createTextNode('1'));

    $deliveryTownPrice = $item->appendChild($dom->createElement('deliveryTownPrice'));
    $deliveryTownPrice->appendChild($dom->createTextNode('0'));

    $deliveryCountryTime = $item->appendChild($dom->createElement('deliveryCountryTime'));
    $deliveryCountryTime->appendChild($dom->createTextNode(''));

    $deliveryCountryPrice = $item->appendChild($dom->createElement('deliveryCountryPrice'));
    $deliveryCountryPrice->appendChild($dom->createTextNode(''));

    $productLifeTime = $item->appendChild($dom->createElement('productLifeTime'));
    $productLifeTime->appendChild($dom->createTextNode(''));

    $isCashless = $item->appendChild($dom->createElement('isCashless'));
    $isCashless->appendChild($dom->createTextNode('нет'));

    $isCredit = $item->appendChild($dom->createElement('isCredit'));
    $isCredit->appendChild($dom->createTextNode('нет'));

    $stockStatus = $item->appendChild($dom->createElement('stockStatus'));
    $stockStatus->appendChild($dom->createTextNode('in_stock'));
}

//генерация xml
$dom->formatOutput = true; // установка атрибута formatOutput
// domDocument в значение true
// save XML as string or file
$test1 = $dom->saveXML(); // передача строки в test1
$dom->save(dirname(__FILE__).'/onliner_products_price.xml'); // сохранение файла
echo "Обработано ".$countProd.' товара';
echo '<br>Экспорт в файл выполнен';
echo '<br>Ссылка на файл : <a href="/onliner_products_price.xml" download>onliner_products_price</a>';
