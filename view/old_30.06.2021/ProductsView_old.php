<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('View.php');

class ProductsView extends View
{
    public function fetch()
    {

        // GET-Параметры
        $category_url = $this->request->get('category', 'string');
        $brand_url = $this->request->get('brand');

        $filter = array();
        $filter['visible'] = 1;

        // Если задан бренд, выберем его из базы
        if (!empty($brand_url) && !is_array($brand_url)) {
            $brand = $this->brands->get_brand((string)$brand_url);
            if (empty($brand)) {
                return false;
            }
            $this->design->assign('brand', $brand);
            $filter['brand_id'] = $brand->id;
        } elseif (is_array($brand_url)) {
            $filter['brand_id'] = $brand_url;
        }

        // Выберем текущую категорию
        if (!empty($category_url)) {
            $category = $this->categories->get_category((string)$category_url);
            if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
                return false;
            }
            $this->design->assign('category', $category);
            $filter['category_id'] = $category->children;
        }

        if ($this->request->get('min_price', 'integer')) {
            $filter['min_price'] = $this->request->get('min_price', 'integer') * $this->currency->rate_to / $this->currency->rate_from;
        }

        if ($this->request->get('max_price')) {
            $filter['max_price'] = $this->request->get('max_price', 'integer') * $this->currency->rate_to / $this->currency->rate_from;
        }

        // Если задано ключевое слово
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $this->design->assign('keyword', $keyword);
            $filter['keyword'] = $keyword;
        }

        if ($type = $this->request->get('type', 'string')) {
            if ($type == 'featured') {
                $filter['featured'] = 1;
            } elseif ($type == 'actions' || $type == 'discounted') {
                $filter['discounted'] = 1;
            }
            $this->design->assign('type', $type);
        }

        // Сортировка товаров, сохраняем в сесси, чтобы текущая сортировка оставалась для всего сайта
        if ($sort = $this->request->get('sort', 'string')) {
            $_SESSION['sort'] = $sort;
        }
        if (!empty($_SESSION['sort'])) {
            $filter['sort'] = $_SESSION['sort'];
        } else {
            $filter['sort'] = 'position';
        }
        $this->design->assign('sort', $filter['sort']);

        // Свойства товаров
        if (!empty($category)) {
            $features = array();
            foreach ($this->features->get_features(array('category_id' => $category->id, 'in_filter' => 1)) as $feature) {
                $features[$feature->id] = $feature;
                if (($val = strval($this->request->get($feature->id))) != '') {
                    $filter['features'][$feature->id] = $val;
                }
            }

            $options_filter['visible'] = 1;

            $features_ids = array_keys($features);
            if (!empty($features_ids)) {
                $options_filter['feature_id'] = $features_ids;
            }
            $options_filter['category_id'] = $category->children;
            if (isset($filter['features'])) {
                $options_filter['features'] = $filter['features'];
            }
            if (!empty($brand)) {
                $options_filter['brand_id'] = $brand->id;
            }

            $options = $this->features->get_options($options_filter);

            foreach ($options as $option) {
                if (isset($features[$option->feature_id])) {
                    $features[$option->feature_id]->options[] = $option;
                }
            }

            foreach ($features as $i => &$feature) {
                if (empty($feature->options)) {
                    unset($features[$i]);
                }
            }

            $this->design->assign('features', $features);
            $this->design->assign('prices_ranges', $this->products->max_min_products(array('category_id' => $filter['category_id'], 'visible' => 1, 'in_stock' => 1)));
        }

        // Постраничная навигация
        $items_per_page = $this->settings->products_num;
        // Текущая страница в постраничном выводе
        $current_page = $this->request->get('page', 'integer');
        // Если не задана, то равна 1
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);
        // Вычисляем количество страниц
        $products_count = $this->products->count_products($filter);
        $this->design->assign('products_count', $products_count);
        // Если ето фильтр
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->design->assign('wrapper', '');
            return $this->design->fetch('_filter_tooltip.tpl');
        }


        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $items_per_page = $products_count;
        }

        $pages_num = ceil($products_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_products_num', $products_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        ///////////////////////////////////////////////
        // Постраничная навигация END
        ///////////////////////////////////////////////


//        $discount = 0;
//        if (isset($_SESSION['user_id']) && $user = $this->users->get_user(intval($_SESSION['user_id']))) {
//            $discount = $user->discount;
//        }

        // Товары
        $products = $this->products->renders($filter);

    /*    echo"<pre>";
        	echo print_r($products,1);
        echo"</pre>";*/


        $this->design->assign('products', $products);

        // Если искали товар и найден ровно один - перенаправляем на него
        if (!empty($keyword) && count($products) == 1) {
            $p = current($products);
            header('Location: ' . $this->config->root_url . '/products/' . $p->url);
        }

        // Выбираем бренды, они нужны нам в шаблоне
        if (!empty($category)) {
            $brands = $this->brands->get_brands(array('category_id' => $category->children, 'visible' => 1));
            if (!empty($brands)) {
                /*                $alf_brands = array();
                foreach($brands as $brand) {
                    $key = mb_strtoupper(mb_substr($brand->name, 0, 1, 'UTF-8'), 'UTF-8');
                    if(!isset($alf_brands[$key])) $alf_brands[$key] = array();
                    $alf_brands[$key][$brand->name] = $brand;
                    ksort($alf_brands[$key]);
                }
                ksort($alf_brands);
                $this->design->assign('alf_brands', $alf_brands);*/

                $category->brands = $brands;
                //$category->alf_brands = $alf_brands;
            }
        } elseif (!empty($type) && !$brand) {
            $query = $this->db->placehold("
				SELECT c.name, count(pc.product_id) as products_count, c.parent_id, c.id, c.url
				FROM __products_categories pc
				LEFT JOIN __categories c ON c.id=pc.category_id
				INNER JOIN __products p ON p.id=pc.product_id AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
				WHERE 1
				AND p.visible=1 
				AND (SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = 1
				AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1
				GROUP BY pc.category_id
				ORDER BY products_count DESC");

            $this->db->query($query);
            $results_categories = $this->db->results();
            foreach ($results_categories as &$u) {
                $u->url .= '?type=actions';
            }

            $this->design->assign('results_categories', $results_categories);

        } elseif (!empty($brand)) {
        }

        // Устанавливаем мета-теги в зависимости от запроса
        if ($this->page && !isset($keyword)) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
        } elseif (isset($category)) {
            $this->design->assign('meta_title', $category->meta_title);
            $this->design->assign('meta_keywords', $category->meta_keywords);
            $this->design->assign('meta_description', $category->meta_description);
        } elseif (isset($brand)) {
            $query = $this->db->placehold("
				SELECT c.name, count(pc.product_id) as products_count, c.parent_id, c.id, c.url, p.brand_id
				FROM __products_categories pc
				LEFT JOIN __categories c ON c.id=pc.category_id
				INNER JOIN __products p ON p.id=pc.product_id AND pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)
				WHERE 1
				AND p.visible=1 
				AND p.brand_id=? 
				AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1
				GROUP BY pc.category_id
				ORDER BY products_count DESC", $brand->id);

            $this->db->query($query);
            $results_categories = $this->db->results();
            foreach ($results_categories as &$u) {
                $u->url .= '?brand[]=' . $u->brand_id;
            }
            /*
                        foreach ($this->db->results() as $value) {
                            $results_categories[$value->id] = $this->categories->get_category((int)$value->id);
                            $results_categories[$value->id]->products_count = $value->products_count;
                        }*/

            $this->design->assign('results_categories', $results_categories);

            $this->design->assign('meta_title', $brand->meta_title);
            $this->design->assign('meta_keywords', $brand->meta_keywords);
            $this->design->assign('meta_description', $brand->meta_description);
        } elseif (isset($keyword)) {
            $this->design->assign('meta_title', $keyword);

            if ($keyword != '') {

                $keyword_filter = '';
                if (!empty($filter['keyword'])) {
                    $keywords = explode(' ', $filter['keyword']);
                    foreach ($keywords as $keyword) {
                        $kw = $this->db->escape(trim($keyword));
                        if ($kw !== '') {
                            $keyword_filter .= $this->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
                        }
                    }
                }

                $query = $this->db->placehold("
				SELECT c.name, count(pc.product_id) as products_count, c.parent_id, c.id, c.url
				FROM __products_categories pc
				LEFT JOIN __categories c ON c.id=pc.category_id
				INNER JOIN __products p ON p.id=pc.product_id AND p.visible=1 $keyword_filter
				WHERE 1
				AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1
				GROUP BY pc.category_id
				ORDER BY products_count DESC");

                $this->db->query($query);
                $results_categories = $this->db->results();
                foreach ($results_categories as &$u) {
                    $u->url .= '?keyword=' . $keyword;
                }

                $this->design->assign('results_categories', $results_categories);
            }

        }

        if ($category->visible_childs && $category->subcategories){return $this->design->fetch('categories.tpl');}
        else{
        return $this->design->fetch('products.tpl');
        }
    }
}
