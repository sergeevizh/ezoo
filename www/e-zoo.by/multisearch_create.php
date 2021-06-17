<?php

require_once('api/Simpla.php');
$simpla = new Simpla();

$dom = new DomDocument('1.0', 'utf-8');

$catalog = $dom->appendChild($dom->createElement('yml_catalog'));

$shop = $catalog->appendChild($dom->createElement('shop'));

$name = $shop->appendChild($dom->createElement('name'));
$name->appendChild($dom->createTextNode($simpla->settings->site_name));

$url = $shop->appendChild($dom->createElement('url'));
$url->appendChild($dom->createTextNode('https://e-zoo.by'));

$currencies_enable = $simpla->money->get_currencies(array('enabled'=>1));
$main_currency = reset($currencies_enable);

$currencies = $shop->appendChild($dom->createElement('currencies'));
foreach ($currencies_enable as $c) {
    if ($c->enabled) {
        $currency = $currencies->appendChild($dom->createElement('currency'));
        $currency->setAttributeNode(new DOMAttr('id', $c->code));
        $currency->setAttributeNode(new DOMAttr('rate', $c->rate_to/$c->rate_from*$main_currency->rate_from/$main_currency->rate_to));
    }
}

$categories = $shop->appendChild($dom->createElement('categories'));
$categories_enable = $simpla->categories->get_categories();

foreach ($categories_enable as $c) {
    $category = $categories->appendChild($dom->createElement('category'));
    $category->appendChild($dom->createTextNode(htmlspecialchars($c->name)));
    $category->setAttributeNode(new DOMAttr('id', $c->id));
    $category->setAttributeNode(new DOMAttr('url', 'https://e-zoo.by/catalog/' . $c->url));
    if ($c->parent_id>0) {
        $category->setAttributeNode(new DOMAttr('parentId', $c->parent_id));
    }
}

$offers = $shop->appendChild($dom->createElement('offers'));
$simpla->db->query("SET SQL_BIG_SELECTS=1");

$simpla->db->query("SELECT v.price, v.compare_price, v.id AS variant_id, p.meta_keywords, p.name AS product_name, p.name_yan_market AS name_yan_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, cat.name AS cat_name, cat.name_cat_market AS name_cat_market, cat.url AS cat_url, i.filename AS image, b.name AS brand, b.url AS brand_url, b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer, p.created
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __categories cat ON cat.id = pc.category_id
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) AND pc.category_id IS NOT NULL GROUP BY v.id ORDER BY p.id, v.position ");

$currency_code = reset($currencies_enable)->code;
$countProd = 0;
$products = $simpla->db->results();
foreach ($products as $product) {
    $countProd++;

    $brandName = '';
    if($product->brand) {$brandName = $product->brand;}
    if($product->brand_clear_name) {$brandName = $product->brand_clear_name;}

    $offer = $offers->appendChild($dom->createElement('offer'));
    $offer->setAttributeNode(new DOMAttr('id', $product->variant_id));
    $offer->setAttributeNode(new DOMAttr('available', 'true'));

    $url = $offer->appendChild($dom->createElement('url'));
    $url->appendChild($dom->createTextNode('https://e-zoo.by/products/'.$product->url));

    $vendor = $offer->appendChild($dom->createElement('vendor'));
    $vendor->appendChild($dom->createTextNode($brandName));

    $category = $offer->appendChild($dom->createElement('categoryId'));
    $category->appendChild($dom->createTextNode($product->category_id));

    $currency = $offer->appendChild($dom->createElement('currencyId'));
    $currency->appendChild($dom->createTextNode($currency_code));

    $picture = $offer->appendChild($dom->createElement('picture'));
    $imgUrl = $simpla->design->resize_modifier($product->image, 160, 160);
    $imgUrl = str_replace("http:///www/www-root/data/www/", "https://", $imgUrl);
    $picture->appendChild($dom->createTextNode($imgUrl));

    $name = $offer->appendChild($dom->createElement('name'));
    $name->appendChild($dom->createTextNode($product->product_name));

    $presence = $offer->appendChild($dom->createElement('presence'));
    $presence->appendChild($dom->createTextNode('Есть в наличии'));

    $created = $offer->appendChild($dom->createElement('createdAt'));
    $created->appendChild($dom->createTextNode($product->created));

    $description = $offer->appendChild($dom->createElement('description'));
    $description->appendChild($dom->createTextNode($product->body));

    $vendorCode = $offer->appendChild($dom->createElement('vendorCode'));
    $vendorCode->appendChild($dom->createTextNode($product->variant_sku));

    if ($product->meta_keywords) {
        $keywords = $offer->appendChild($dom->createElement('keywords'));
        $keywords->appendChild($dom->createTextNode($product->meta_keywords));
    }

    if ($product->compare_price) {
        $oldPrice = $offer->appendChild($dom->createElement('oldprice'));
        $oldPrice->appendChild($dom->createTextNode($product->compare_price));
    }

    $price_val = $product->price;
    if ($price_val>$product->compare_price && $brandName!='') {
        $productElem = $simpla->products->get_product(intval($product->product_id));
        $varintProd = $simpla->variants->get_variant($product->variant_id);
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
}


$dom->formatOutput = true;

$test1 = $dom->saveXML();
$dom->save(dirname(__FILE__).'/multisearch_feed.xml');
echo "Обработано ".$countProd.' товара';
echo '<br>Экспорт в файл выполнен';
echo '<br>Ссылка на файл : <a href="/multisearch_feed.xml" download>multisearch_feed</a>';
