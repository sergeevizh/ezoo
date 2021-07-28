<?php
require_once('../../api/Simpla.php');
$simpla = new Simpla();
$limit = 100;

$keyword = $simpla->request->get('query', 'string');

$keywords = explode(' ', $keyword);
$keyword_sql = '';
foreach($keywords as $keyword)
{
	$kw = $simpla->db->escape(trim($keyword));
	$keyword_sql .= $simpla->db->placehold("AND (c.name LIKE '%$kw%' OR c.meta_keywords LIKE '%$kw%')");
}


$simpla->db->query('SELECT c.id, c.name, c.image 
FROM __categories c
WHERE 1 '.$keyword_sql.'
ORDER BY c.name LIMIT ?', $limit);
$products = $simpla->db->results();

$suggestions = array();
foreach($products as $product)
{
	if(!empty($product->image)) {
		$product->image = $simpla->config->root_url .'/'.$simpla->config->categories_images_dir . $product->image;
	}

	$suggestion = new stdClass();
	$suggestion->value = $product->name;
	$suggestion->data = $product;
	$suggestions[] = $suggestion;
}

$res = new stdClass;
$res->query = $keyword;
$res->suggestions = $suggestions;
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($res);
