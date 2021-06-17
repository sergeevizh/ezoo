<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('Simpla.php');

class Products extends Simpla
{

    /**
     * Функция возвращает товары
     * Возможные значения фильтра:
     * id - id товара или их массив
     * category_id - id категории или их массив
     * brand_id - id бренда или их массив
     * page - текущая страница, integer
     * limit - количество товаров на странице, integer
     * sort - порядок товаров, возможные значения: position(по умолчанию), name, price
     * keyword - ключевое слово для поиска
     * features - фильтр по свойствам товара, массив (id свойства => значение свойства)
     *
     * @param array $filter
     * @return array|bool
     */
    public function get_products($filter = array())
    {

        // По умолчанию
        $limit = 100;
        $page = 1;
        $category_id_filter = '';
        $brand_id_filter = '';
        $product_id_filter = '';
        $product_not_id_filter = '';
        $features_filter = '';
        $keyword_filter = '';
        $visible_filter = '';
        $min_price = '';
        $max_price = '';
        $is_featured_filter = '';
        $is_featured_cart = '';
        $discounted_filter = '';
        $in_stock_filter = '';
        $group_by = 'GROUP BY p.id';
        $order = 'p.position DESC';
        $join = '';
        $marketing = '';
        $lecense = '';
        $cond = '';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        if (!empty($filter['id'])) {
            $product_id_filter = $this->db->placehold('AND p.id IN(?@)', (array)$filter['id']);
        }

        if (!empty($filter['not_id'])) {
            $product_not_id_filter = $this->db->placehold('AND p.id NOT in(?@)', (array)$filter['not_id']);
        }

        if (!empty($filter['marketing_offer'])) {
            $marketing = $this->db->placehold('AND p.marketing_offer=?', (int)$filter['marketing_offer']);
        }
        if (!empty($filter['lecense'])) {
            $lecense = $this->db->placehold('AND p.lecense=?', (int)$filter['lecense']);
        }

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id in(?@)', (array)$filter['category_id']);
            $group_by = "GROUP BY p.id";
        }

        if (!empty($filter['brand_id'])) {
            $brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);
        }

        if (isset($filter['featured'])) {
            $is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));
        }

        if (isset($filter['featured_cart'])) {
            $is_featured_cart = $this->db->placehold('AND p.featured_cart=?', intval($filter['featured_cart']));
        }

        if (isset($filter['discounted'])) {
            $discounted_filter = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['discounted']));
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?',
                intval($filter['in_stock']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (defined('IS_CLIENT')){
            if (!empty($filter['region'])){
                if (isset($_SESSION['region_id'])){
                    $join = $this->db->placehold('LEFT JOIN __variants v ON p.id=v.product_id LEFt JOIN __supply_dates_variants sdv ON sdv.variant_id=v.id LEFT JOIN __supply_dates_brands sdb ON p.brand_id=sdb.brand_id');
                    $cond = $this->db->placehold('AND (v.region_stock_' . intval($filter['region']) . '  IS NULL OR v.region_stock_' . intval($filter['region']) . '>0 OR (sdv.region_id=? OR sdb.region_id=?))', intval($filter['region']), intval($filter['region']));
                }
            } else {
                $join = $this->db->placehold('LEFT JOIN __variants v ON p.id=v.product_id LEFt JOIN __supply_dates_variants sdv ON sdv.variant_id=v.id LEFT JOIN __supply_dates_brands sdb ON p.brand_id=sdb.brand_id');
                $cond = $this->db->placehold(' AND (v.stock IS NULL OR v.stock>0 OR (sdv.region_id=0 OR sdb.region_id=0))');
            }
        }


        if (!empty($filter['min_price'])) {
            $min_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>=? LIMIT 1)', intval($filter['min_price']));
        }

        if (!empty($filter['max_price'])) {
            $max_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price<=? LIMIT 1)', intval($filter['max_price']));
        }

        if (!empty($filter['sort'])) {
            switch ($filter['sort']) {
                case 'position':
                    $order = 'p.position DESC';
                    break;
                case 'name':
                    $order = 'p.name';
                    break;
                case 'created':
                case 'novelty':
                    $order = 'p.created DESC';
                    break;
                case 'price':
                case 'cheap':
                    $order = '(SELECT -pv.price FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id AND pv.position=(SELECT MIN(position) FROM __variants WHERE (stock>0 OR stock IS NULL) AND product_id=p.id LIMIT 1) LIMIT 1) DESC';
                    break;
                case 'price_desc':
                case 'expensive':
                    $order = '(SELECT -pv.price FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id AND pv.position=(SELECT MIN(position) FROM __variants WHERE (stock>0 OR stock IS NULL) AND product_id=p.id LIMIT 1) LIMIT 1)';
                    break;
                // TODO сделать соритровку по по рейтингу
                case 'budget':

                    break;
                case 'rand':
                    $order = 'RAND()';
                    break;
                default:
                    $order = 'p.position DESC';

            }
        }
        // товары с 0 вконце
        if (!empty($filter['sort_priority_stock'])) {
            $order = 'IF((SELECT 1 FROM __variants WHERE (stock IS NULL OR stock>0) AND product_id=p.id LIMIT 1), 1, 0) DESC, '.$order;
        }
        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                }
            }
        }

        if (!empty($filter['features']) && !empty($filter['features'])) {
            foreach ($filter['features'] as $feature => $value) {
                $features_filter .= $this->db->placehold('AND p.id in (SELECT product_id FROM __options WHERE feature_id=? AND value=? ) ', $feature, $value);
            }
        }

        $query = "SELECT
					p.id,
					p.url,
					p.brand_id,
					p.name,
					p.annotation,
					p.body,
					p.position,
					p.created as created,
					p.visible,
					p.pickup,
					p.lecense,
					p.marketing_offer,
					p.featured_cart,
					p.sale_double_item,
					p.sale_double_item_value,
					p.sale_double_item_sam,
					p.sale_double_item_sam_value,
					p.featured,
					p.meta_title,
					p.meta_keywords,
					p.meta_description,
					b.name as brand,
					b.url as brand_url
				FROM __products p
				$category_id_filter
				$join
				LEFT JOIN __brands b ON p.brand_id = b.id
				WHERE
					1
					$product_id_filter
					$product_not_id_filter
					$marketing
					$lecense
					$brand_id_filter
					$features_filter
					$keyword_filter
					$is_featured_filter
					$is_featured_cart
					$discounted_filter
					$in_stock_filter
					$visible_filter
					$min_price
					$max_price
					$cond
				$group_by
				ORDER BY $order
					$sql_limit";

        if ($results = $this->cache->get($query)){
            return $results;
        } else {
            $this->cache->set($query, $this->db->results(), false, 86400);
            $this->db->query($query);
        }
//        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция возвращает количество товаров
     * Возможные значения фильтра:
     * category_id - id категории или их массив
     * brand_id - id бренда или их массив
     * keyword - ключевое слово для поиска
     * features - фильтр по свойствам товара, массив (id свойства => значение свойства)
     *
     * @param array $filter
     * @return bool|object|string
     */
    public function count_products($filter = array())
    {
        $category_id_filter = '';
        $brand_id_filter = '';
        $product_id_filter = '';
        $keyword_filter = '';
        $visible_filter = '';
        $min_price = '';
        $max_price = '';
        $is_featured_filter = '';
        $is_featured_cart = '';
        $in_stock_filter = '';
        $discounted_filter = '';
        $features_filter = '';
        $marketing = '';
        $lecense = '';
        $join ='';
        $cond ='';

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id in(?@)', (array)$filter['category_id']);
        }

        if (!empty($filter['brand_id'])) {
            $brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);
        }

        if (!empty($filter['id'])) {
            $product_id_filter = $this->db->placehold('AND p.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                }
            }
        }

        if (!empty($filter['marketing_offer'])) {
            $marketing = $this->db->placehold('AND p.marketing_offer=?', (int)$filter['marketing_offer']);
        }

        if (!empty($filter['lecense'])) {
            $lecense = $this->db->placehold('AND p.lecense=?', (int)$filter['lecense']);
        }

        if (isset($filter['featured'])) {
            $is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));
        }

        if (isset($filter['featured_cart'])) {
            $is_featured_cart = $this->db->placehold('AND p.featured_cart=?', intval($filter['featured_cart']));
        }


        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?',
                intval($filter['in_stock']));
        }

        if (isset($filter['discounted'])) {
            $discounted_filter = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?', intval($filter['discounted']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (!empty($filter['min_price'])) {
            $min_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>=? LIMIT 1)', intval($filter['min_price']));
        }

        if (!empty($filter['max_price'])) {
            $max_price = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.price<=? LIMIT 1)', intval($filter['max_price']));
        }

        if (defined('IS_CLIENT')){
            if (!empty($filter['region'])){
                if (isset($_SESSION['region_id'])){
                    $join = $this->db->placehold('LEFT JOIN __variants v ON p.id=v.product_id LEFt JOIN __supply_dates_variants sdv ON sdv.variant_id=v.id LEFT JOIN __supply_dates_brands sdb ON p.brand_id=sdb.brand_id');
                    $cond = $this->db->placehold('AND (v.region_stock_' . intval($filter['region']) . '>0 OR (sdv.region_id=? OR sdb.region_id=?))', intval($filter['region']), intval($filter['region']));
                }
            } else {
                $join = $this->db->placehold('LEFT JOIN __variants v ON p.id=v.product_id LEFt JOIN __supply_dates_variants sdv ON sdv.variant_id=v.id LEFT JOIN __supply_dates_brands sdb ON p.brand_id=sdb.brand_id');
                $cond = $this->db->placehold(' AND (v.stock>0 OR (sdv.region_id=0 OR sdb.region_id=0))');
            }
        }

        if (!empty($filter['features']) && !empty($filter['features'])) {
            foreach ($filter['features'] as $feature => $value) {
                $features_filter .= $this->db->placehold('AND p.id in (SELECT product_id FROM __options WHERE feature_id=? AND value=? ) ', $feature, $value);
            }
        }

        $query = "SELECT count(distinct p.id) as count
				FROM __products AS p
				$category_id_filter
				$join
				WHERE 1
					$brand_id_filter
					$marketing
					$lecense
					$product_id_filter
					$keyword_filter
					$is_featured_filter
					$is_featured_cart
					$in_stock_filter
					$discounted_filter
					$visible_filter
					$min_price
					$max_price
					$features_filter
					$cond ";

        $this->db->query($query);
        return $this->db->result('count');
    }

    public function max_min_products($filter = array())
    {
        $category_id_filter = '';
        $brand_id_filter = '';
        $product_id_filter = '';
        $keyword_filter = '';
        $visible_filter = '';
        $is_featured_filter = '';
        $in_stock_filter = '';
        $discounted_filter = '';
        $features_filter = '';

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold('INNER JOIN __products_categories pc ON pc.product_id = p.id AND pc.category_id in(?@)', (array)$filter['category_id']);
        }

        if (!empty($filter['brand_id'])) {
            $brand_id_filter = $this->db->placehold('AND p.brand_id in(?@)', (array)$filter['brand_id']);
        }

        if (!empty($filter['id'])) {
            $product_id_filter = $this->db->placehold('AND p.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = $this->db->escape(trim($keyword));
                if ($kw !== '') {
                    $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                }
            }
        }

        if (isset($filter['featured'])) {
            $is_featured_filter = $this->db->placehold('AND p.featured=?', intval($filter['featured']));
        }

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?',
                intval($filter['in_stock']));
        }

        if (isset($filter['discounted'])) {
            $discounted_filter = $this->db->placehold('AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = ?', intval($filter['discounted']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }


        if (!empty($filter['features']) && !empty($filter['features'])) {
            foreach ($filter['features'] as $feature => $value) {
                $features_filter .= $this->db->placehold('AND p.id in (SELECT product_id FROM __options WHERE feature_id=? AND value=? ) ', $feature, $value);
            }
        }
        $query = "SELECT  MIN(pv.price) as min_price, MAX(pv.price) as max_price
				FROM __products AS p
				INNER JOIN __variants pv ON pv.product_id=p.id
				$category_id_filter
				WHERE 1
					$brand_id_filter
					$product_id_filter
					$keyword_filter
					$is_featured_filter
					$in_stock_filter
					$discounted_filter
					$visible_filter
					$features_filter ";

        if ($result = $this->cache->get($query)){
            return $result;
        } else {
            $this->cache->set($query, $this->db->result(), false, 86400);
            $this->db->query($query);
        }
        return $this->db->result();
    }

    /**
     * Функция возвращает товар по id
     *
     * @param $id
     * @return bool|object|string
     */
    public function get_product($id)
    {
        if (is_int($id)) {
            $filter = $this->db->placehold('p.id = ?', $id);
        } else {
            $filter = $this->db->placehold('p.url = ?', $id);
        }
        $query = "SELECT DISTINCT
					p.id,
					p.url,
					p.brand_id,
					p.name,
					p.name_yan_market,
					p.annotation,
					p.body,
					p.position,
					p.created as created,
					p.visible,
					p.pickup,
					p.lecense,
					p.marketing_offer,
					p.sale_double_item,
					p.sale_double_item_value,
					p.sale_double_item_sam,
					p.sale_double_item_sam_value,
					p.featured,
					p.featured_cart,
					p.meta_title,
					p.meta_keywords,
					p.meta_description
				FROM __products AS p
				WHERE $filter
				GROUP BY p.id
				LIMIT 1";

//        if ($result = $this->cache->get($query)){
//            return $result;
//        } else {
//            $this->cache->set($query, $this->db->result(), false, 86400);
//            $this->db->query($query);
//            $product = $this->db->result();
//        }
        $this->db->query($query);
        $product = $this->db->result();
//        $this->db->query($query);
        return $product;
    }

    /**
     * @param $id
     * @param $product
     * @return bool
     */
    public function update_product($id, $product)
    {

        $query = $this->db->placehold("SELECT url FROM __products WHERE id in (?@) LIMIT ?", (array)$id, count((array)$id));
        $this->db->query($query);
        $url = $this->db->result();

        $query = $this->db->placehold('DELETE FROM __deleted_products WHERE url=?', $url->url);
        $this->db->query($query);

        $query = $this->db->placehold("UPDATE __products SET ?% WHERE id in (?@) LIMIT ?", $product, (array)$id, count((array)$id));
        if ($this->db->query($query)) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * @param $product
     * @return bool|mixed
     */
    public function add_product($product)
    {
        $product = (array)$product;

        if (empty($product['url'])) {
            $product['url'] = preg_replace("/[\s]+/ui", '-', $product['name']);
            $product['url'] = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $product['url']));
        }

        // Если есть товар с таким URL, добавляем к нему число
        while ($this->get_product((string)$product['url'])) {
            if (preg_match('/(.+)_([0-9]+)$/', $product['url'], $parts)) {
                $product['url'] = $parts[1] . '_' . ($parts[2] + 1);
            } else {
                $product['url'] = $product['url'] . '_2';
            }
        }

        $query= $this->db->placehold('DELETE FROM __deleted_products WHERE url=?', $product['url']);
        $this->db->query($query);


        if ($this->db->query("INSERT INTO __products SET ?%", $product)) {
            $id = $this->db->insert_id();
            $this->db->query("UPDATE __products SET position=id WHERE id=?", $id);
            return $id;
        } else {
            return false;
        }
    }


    /**
     * Удалить товар
     *
     * @param $id
     * @return bool
     */
    public function delete_product($id)
    {
        if (!empty($id)) {
            // Удаляем варианты
            $variants = $this->variants->get_variants(array('product_id' => $id));
            foreach ($variants as $v) {
                $this->variants->delete_variant($v->id);
            }

            // Удаляем изображения
            $images = $this->get_images(array('product_id' => $id));
            foreach ($images as $i) {
                $this->delete_image($i->id);
            }

            // Удаляем категории
            $categories = $this->categories->get_categories(array('product_id' => $id));
            foreach ($categories as $c) {
                $this->categories->delete_product_category($id, $c->id);
            }

            // Удаляем свойства
            $options = $this->features->get_options(array('product_id' => $id));
            foreach ($options as $o) {
                $this->features->delete_option($id, $o->feature_id);
            }

            // Удаляем связанные товары
            $related = $this->get_related_products($id);
            foreach ($related as $r) {
                $this->delete_related_product($id, $r->related_id);
            }

            // Удаляем товар из связанных с другими
            $query = $this->db->placehold("DELETE FROM __related_products WHERE related_id=?", intval($id));
            $this->db->query($query);

            // Удаляем отзывы
            $comments = $this->comments->get_comments(array('object_id' => $id, 'type' => 'product'));
            foreach ($comments as $c) {
                $this->comments->delete_comment($c->id);
            }

            // Удаляем из покупок
            $this->db->query('UPDATE __purchases SET product_id=NULL WHERE product_id=?', intval($id));

            $query = $this->db->placehold("SELECT url FROM __products WHERE id=?", $id);
            $this->db->query($query);
            $url = $this->db->result('url');

            if (!empty($url)){

                $query = $this->db->placehold("INSERT INTO __deleted_products SET url=?", $url);
                $this->db->query($query);
            }


            // Удаляем товар
            $query = $this->db->placehold("DELETE FROM __products WHERE id=? LIMIT 1", intval($id));
            if ($this->db->query($query)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public function duplicate_product($id)
    {
        $product = $this->get_product($id);
        $product->id = null;
        $product->external_id = '';
        $product->created = null;

        // Сдвигаем товары вперед и вставляем копию на соседнюю позицию
        $this->db->query('UPDATE __products SET position=position+1 WHERE position>?', $product->position);
        $new_id = $this->products->add_product($product);
        $this->db->query('UPDATE __products SET position=? WHERE id=?', $product->position + 1, $new_id);

        // Очищаем url
        $this->db->query('UPDATE __products SET url="" WHERE id=?', $new_id);

        // Дублируем категории
        $categories = $this->categories->get_product_categories($id);
        foreach ($categories as $c) {
            $this->categories->add_product_category($new_id, $c->category_id);
        }

        // Дублируем изображения
        $images = $this->get_images(array('product_id' => $id));
        foreach ($images as $image) {
            $this->add_image($new_id, $image->filename);
        }

        // Дублируем варианты
        $variants = $this->variants->get_variants(array('product_id' => $id));
        foreach ($variants as $variant) {
            $variant->product_id = $new_id;
            unset($variant->id);
            if ($variant->infinity) {
                $variant->stock = null;
            }
            unset($variant->infinity);
            $variant->external_id = '';
            $this->variants->add_variant($variant);
        }

        // Дублируем свойства
        $options = $this->features->get_options(array('product_id' => $id));
        foreach ($options as $o) {
            $this->features->update_option($new_id, $o->feature_id, $o->value);
        }

        // Дублируем связанные товары
        $related = $this->get_related_products($id);
        foreach ($related as $r) {
            $this->add_related_product($new_id, $r->related_id);
        }


        return $new_id;
    }

    /**
     * @param array $product_id
     * @return array|bool
     */
    public function get_related_products($product_id = array())
    {
        if (empty($product_id)) {
            return array();
        }

        $product_id_filter = $this->db->placehold('AND product_id in(?@)', (array)$product_id);

        $query = $this->db->placehold("SELECT product_id, related_id, position
					FROM __related_products
					WHERE
					1
					$product_id_filter
					ORDER BY position
					");

        if ($results = $this->cache->get($query)){
            return $results;
        } else {
            $this->cache->set($query, $this->db->results(), false, 86400);
            $this->db->query($query);
        }
//        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция возвращает связанные товары
     *
     * @param $product_id
     * @param $related_id
     * @param int $position
     * @return mixed
     */
    public function add_related_product($product_id, $related_id, $position = 0)
    {
        $query = $this->db->placehold("INSERT IGNORE INTO __related_products SET product_id=?, related_id=?, position=?", $product_id, $related_id, $position);
        $this->db->query($query);
        return $related_id;
    }

    /**
     * Удаление связанного товара
     *
     * @param $product_id
     * @param $related_id
     */
    public function delete_related_product($product_id, $related_id)
    {
        $query = $this->db->placehold("DELETE FROM __related_products WHERE product_id=? AND related_id=? LIMIT 1", intval($product_id), intval($related_id));
        $this->db->query($query);
    }

    /**
     * @param array $filter
     * @return array|bool
     */
    public function get_images($filter = array())
    {
        $product_id_filter = '';
        $group_by = '';

        if (!empty($filter['product_id'])) {
            $product_id_filter = $this->db->placehold('AND i.product_id in(?@)', (array)$filter['product_id']);
        }

        // images
        $query = $this->db->placehold("SELECT i.id, i.product_id, i.name, i.filename, i.position
									FROM __images AS i WHERE 1 $product_id_filter $group_by ORDER BY i.product_id, i.position");

        if ($results = $this->cache->get($query)){
            return $results;
        } else {
            $this->cache->set($query, $this->db->results(), false, 86400);
            $this->db->query($query);
        }
//        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * @param  $product_id
     * @param  $filename
     * @param  string $name
     * @return bool|mixed|object|string
     */
    public function add_image($product_id, $filename)
    {
        $query = $this->db->placehold("SELECT id FROM __images WHERE product_id=? AND filename=?", $product_id, $filename);
        $this->db->query($query);
        $id = $this->db->result('id');
        if (empty($id)) {
            $query = $this->db->placehold("INSERT INTO __images SET product_id=?, filename=?", $product_id, $filename);
            $this->db->query($query);
            $id = $this->db->insert_id();
            $query = $this->db->placehold("UPDATE __images SET position=id WHERE id=?", $id);
            $this->db->query($query);
        }
        return ($id);
    }

    /**
     * @param $id
     * @param $image
     * @return mixed
     */
    public function update_image($id, $image)
    {
        $query = $this->db->placehold("UPDATE __images SET ?% WHERE id=?", $image, $id);
        $this->db->query($query);

        return ($id);
    }

    /**
     * @param $id
     */
    public function delete_image($id)
    {
        $query = $this->db->placehold("SELECT filename FROM __images WHERE id=?", $id);
        $this->db->query($query);
        $filename = $this->db->result('filename');
        $query = $this->db->placehold("DELETE FROM __images WHERE id=? LIMIT 1", $id);
        $this->db->query($query);
        $query = $this->db->placehold("SELECT count(*) as count FROM __images WHERE filename=? LIMIT 1", $filename);
        $this->db->query($query);
        $count = $this->db->result('count');
        if ($count == 0) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            // Удалить все ресайзы
            $rezised_images = glob($this->config->root_dir . $this->config->resized_images_dir . $file . ".*x*." . $ext);
            if (is_array($rezised_images)) {
                foreach (glob($this->config->root_dir . $this->config->resized_images_dir . $file . ".*x*." . $ext) as $f) {
                    @unlink($f);
                }
            }

            @unlink($this->config->root_dir . $this->config->original_images_dir . $filename);
        }
    }

    /**
     * Следующий товар
     *
     * @param  int $id
     * @return bool|object|string
     */
    public function get_next_product($id)
    {
        $this->db->query("SELECT position FROM __products WHERE id=? LIMIT 1", $id);
        $position = $this->db->result('position');

        $this->db->query("SELECT pc.category_id FROM __products_categories pc WHERE product_id=? ORDER BY position LIMIT 1", $id);
        $category_id = $this->db->result('category_id');

        $query = $this->db->placehold("SELECT id FROM __products p, __products_categories pc
										WHERE pc.product_id=p.id AND p.position>?
										AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
										AND pc.category_id=?
										AND p.visible ORDER BY p.position limit 1", $position, $category_id);
        $this->db->query($query);

        return $this->get_product((integer)$this->db->result('id'));
    }

    /**
     * Предыдущий товар
     *
     * @param  int $id
     * @return bool|object|string
     */
    public function get_prev_product($id)
    {
        $this->db->query("SELECT position FROM __products WHERE id=? LIMIT 1", $id);
        $position = $this->db->result('position');

        $this->db->query("SELECT pc.category_id FROM __products_categories pc WHERE product_id=? ORDER BY position LIMIT 1", $id);
        $category_id = $this->db->result('category_id');

        $query = $this->db->placehold("SELECT id FROM __products p, __products_categories pc
										WHERE pc.product_id=p.id AND p.position<?
										AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
										AND pc.category_id=?
										AND p.visible ORDER BY p.position DESC limit 1", $position, $category_id);
        $this->db->query($query);

        return $this->get_product((integer)$this->db->result('id'));
    }

    public function renders($filter = array())
    {
        $filter['visible'] = 1;
        $filter['sort_priority_stock'] = 1;

        $products = array();
        foreach ($this->get_products($filter) as $p) {
            $products[$p->id] = $p;
        }
//        echo '<pre>';
//        var_dump($this->get_products($filter));
//        echo '</pre>';
        if (!empty($products)) {
            $products_ids = array_keys($products);
            foreach ($products as &$product) {
                $product->comments_count = 0;
                $product->price = 0;
                $product->taste = '';
                $product->compare_price = 0;
                $product->variants = array();
                $product->images = array();
                $product->properties = array();
                $product->check = 0;
            }

            $this->db->query("
				SELECT value, product_id
				FROM __options
				WHERE feature_id=244
				AND product_id IN(?@)
			", $products_ids);
            $options = $this->db->results();

            foreach ($options as $option) {
                $products[$option->product_id]->taste = $option->value;
            }
            $this->db->query("
				SELECT COUNT(id) AS comments_count, object_id
				FROM __comments
				WHERE type='product'
				AND object_id IN(?@)
				AND approved=1
				GROUP BY object_id
			", $products_ids);

            $comments_counts = $this->db->results();

            foreach ($comments_counts as $comment) {
                $products[$comment->object_id]->comments_count = $comment->comments_count;
            }

            $variants = $this->variants->get_variants(array('product_id' => $products_ids));

            foreach ($variants as &$variant) {
                if (empty($products[$variant->product_id]->price)) {
                    $products[$variant->product_id]->price = $variant->price;
                }
                if (empty($products[$variant->product_id]->compare_price)) {
                    $products[$variant->product_id]->compare_price = $variant->compare_price;
                }


                if($variant->price>$variant->compare_price){
                    $variant->sale = $this->cart->GetPriceByDelivery($products[$variant->product_id],$variant);
                }

                $brand_supply_dates = array();
                foreach ($this->dates->getBrandsSupplyDates(array('brand_id' => (int)$products[$variant->product_id]->brand_id)) as $date){
                    $brand_supply_dates[$date->region_id] = $date->date;
                }

                if ($variant->stock > 0 || isset($_SESSION['region_id']) && $brand_supply_dates[$_SESSION['region_id']] || isset($_SESSION['region_id']) && !empty($variant->supply_dates[$_SESSION['region_id']]) && $variant->stock <= 0 || isset($_SESSION['region_short_name']) && $_SESSION['region_short_name'] == 'Минск' && !empty($variant->supply_dates[0]) && $variant->stock <= 0 || isset($_SESSION['region_short_name']) && $_SESSION['region_short_name'] == 'Минск' && !empty($brand_supply_dates[0]) && $variant->stock <= 0 ){

                    if (isset($_SESSION['region_id']) && $brand_supply_dates[$_SESSION['region_id']]){
                        $variant->supply_dates = $brand_supply_dates[$_SESSION['region_id']];
                    } elseif (isset($_SESSION['region_id']) && !empty($variant->supply_dates[$_SESSION['region_id']])){
                        $variant->supply_dates = $variant->supply_dates[$_SESSION['region_id']];
                    } elseif(isset($_SESSION['region_short_name']) && $_SESSION['region_short_name'] == 'Минск'){
                        if (!empty($variant->supply_dates[0])){
                            $variant->supply_dates = $variant->supply_dates[0];
                        } else if (!empty($brand_supply_dates[0])){
                            $variant->supply_dates = $brand_supply_dates[0];
                        }

                    } else {
                        unset($variant->supply_dates);
                    }

                    $products[$variant->product_id]->variants[] = $variant;

                    if ($products[$variant->product_id]->check == 0){
                        $products[$variant->product_id]->check = 1;
                    }
                }
            }

            $images = $this->products->get_images(array('product_id' => $products_ids));
            foreach ($images as $image) {
                $products[$image->product_id]->images[] = $image;
            }

            foreach ($products as &$product) {
                if (isset($product->variants[0])) {
                    $product->variant = $product->variants[0];
                }
//                echo '<pre>'; print_r($products); echo '</pre>'; exit;
                if (isset($product->images[0])) {
                    $product->image = $product->images[0];
                }
            }
        }

        return $products;
    }
}
