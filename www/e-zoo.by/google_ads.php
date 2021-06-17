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

$column_names = array(
    'variant_id' => 'id',
    'product_name' => 'Item title',
    'url' => 'Final URL',
    'image' => 'Image URL',
    'price' => 'Price',
    'name_yan_market' => 'Item subtitle',
    'body' => 'Item description'
);

$column_delimiter = ',';
$export_files_dir = '/';
$filename = 'google_ads_feed.csv';

$f = fopen($filename, 'w');
fclose($f);

$f = fopen($filename, 'ab');

foreach ($column_names as $key => $value) {
    $column_names[$key] = $simpla->convert_str_encoding($value, 'windows-1251', 'UTF-8', $key);
}
fwrite($f, implode($column_delimiter, $column_names)."\n");

$simpla->db->query("SET SQL_BIG_SELECTS=1");

$simpla->db->query("SELECT v.price, v.compare_price, v.id AS variant_id, p.name AS product_name, p.name_yan_market AS name_yan_market, v.name AS variant_name, v.position AS variant_position, v.sku AS variant_sku, p.id AS product_id, p.url, p.annotation, p.body, pc.category_id, cat.name AS cat_name, cat.name_cat_market AS name_cat_market, i.filename AS image, b.name AS brand, b.clear_name AS brand_clear_name, b.manufacturer AS manufacturer, b.importer AS importer
					FROM __variants v LEFT JOIN __products p ON v.product_id=p.id
					LEFT JOIN s_brands b ON b.id = p.brand_id
					LEFT JOIN __products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM __products_categories WHERE product_id=p.id LIMIT 1)
					LEFT JOIN __categories cat ON cat.id = pc.category_id
					LEFT JOIN __images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
					WHERE p.visible AND (v.stock >0 OR v.stock is NULL) GROUP BY v.id ORDER BY p.id, v.position ");

$currency_code = reset($currencies_val)->code;


$prev_product_id = null;
$countProd = 0;
$products = $simpla->db->results();

foreach ($products as $p){
    $str = array();
    $countProd++;

    $brandName = '';
    if($p->brand) {$brandName = $p->brand;}
    if($p->brand_clear_name) {$brandName = $p->brand_clear_name;}

    $cat_type = '';
    if ($p->name_cat_market){ $cat_type = $p->name_cat_market.' ';}

    foreach ($column_names as $n => $c) {


        switch ($n){
            case 'product_name':
                $nameModel = ($p->name_yan_market) ? htmlspecialchars($p->name_yan_market) : htmlspecialchars($p->product_name);
                $str[] = $cat_type.($brandName?$brandName.' ':'').$nameModel.($p->variant_name?' '.htmlspecialchars($p->variant_name):'');
                break;
            case 'url':
                $str[] = 'https://' . $_SERVER['SERVER_NAME'] . '/products/' . $p->$n;
                break;
            case 'image':
                $imgUrl = $simpla->design->resize_modifier($p->image, 600, 600);
                $imgUrl = str_replace("http:///public_html", "https://e-zoo.by", $imgUrl);
                $str[] = $imgUrl;
                break;
            case 'price':
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
                $str[] = $price_val . ' BYN';
                break;
            case 'body':
                $str[] = html_entity_decode(str_replace(array(',', '"', ';'), array(',', ' ', ','), strip_tags($p->$n)));
                break;
            case 'name_yan_market':
                $str[] = html_entity_decode(str_replace(array(',', '"', ';'), array(',', ' ', ','), strip_tags($p->$n)));
                break;
            case 'id':
                $str[] = $p->variant_id;
                break;
            default:
                $str[] = $p->$n;
                break;
        }


    }

    fputcsv($f, $str, $column_delimiter);
}



fclose($f);
echo "Обработано ".$countProd.' товара';
echo '<br>Экспорт в файл выполнен';
echo '<br>Ссылка на файл : <a href="/google_ads_feed.csv" download>google_ads_feed</a>';
