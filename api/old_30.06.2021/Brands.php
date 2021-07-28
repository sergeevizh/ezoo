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

class Brands extends Simpla
{
    /**
     * Функция возвращает массив брендов, удовлетворяющих фильтру
     *
     * @param array $filter
     * @return array|bool
     */
    public function get_brands($filter = array())
    {
        $category_id_filter = 'WHERE 1';
        $visible_filter = '';
        $in_stock_filter = '';
        $visible_is_main_filter = '';
        $label_new_filter = '';

        if (isset($filter['in_stock'])) {
            $in_stock_filter = $this->db->placehold('AND (SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND pv.price>0 AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = ?',
                intval($filter['in_stock']));
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND p.visible=?', intval($filter['visible']));
        }

        if (!empty($filter['category_id'])) {
            $category_id_filter = $this->db->placehold("LEFT JOIN __products p ON p.brand_id=b.id LEFT JOIN __products_categories pc ON p.id = pc.product_id WHERE pc.category_id in(?@) $visible_filter $in_stock_filter",
                (array)$filter['category_id']);
        }

        if (isset($filter['visible_is_main'])) {
            $visible_is_main_filter = $this->db->placehold('AND b.visible_is_main=?', intval($filter['visible_is_main']));
            $query = $this->db->placehold("SELECT DISTINCT b.id, b.name, b.url, b.meta_title, b.meta_keywords, b.meta_description, b.description, b.image, b.banner, b.color, b.background, b.label_new, b.visible_is_main, b.sort
										FROM __brands b
											$category_id_filter
											$visible_is_main_filter
										ORDER BY b.sort");
            $this->db->query($query);

            return $this->db->results();
        }

        if (isset($filter['label_new'])) {
            $label_new_filter = $this->db->placehold('AND b.label_new=?', intval($filter['label_new']));
        }

        // Выбираем все бренды
        $query = $this->db->placehold("SELECT DISTINCT b.id, b.name, b.url, b.meta_title, b.meta_keywords, b.meta_description, b.description, b.image, b.banner, b.color, b.background, b.label_new, b.visible_is_main, b.sort
										FROM __brands b
											$category_id_filter
											$visible_is_main_filter
											$label_new_filter
										ORDER BY b.name");

//        if ($result = $this->cache->get($query)){
//            return $result;
//        } else {
//            $this->cache->set($query, $this->db->results(), false, 86400);
//            $this->db->query($query);
//        }
        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция возвращает бренд по его id или url
     * (в зависимости от типа аргумента, int - id, string - url)
     *
     * @param  int|string $id
     * @return bool|object
     */
    public function get_brand($id)
    {
        if (is_int($id)) {
            $filter = $this->db->placehold('b.id = ?', $id);
        } else {
            $filter = $this->db->placehold('b.url = ?', $id);
        }

        $query = $this->db->placehold("SELECT b.id, b.name,b.h1_head, b.clear_name, b.importer, b.manufacturer, b.license_link, b.url, b.meta_title, b.meta_keywords, b.meta_description, b.description, b.image, b.banner, b.color, b.background, b.label_new, b.visible_is_main, b.sort
										FROM __brands b
										WHERE $filter
										LIMIT 1");

//        if ($result = $this->cache->get($query)){
//            return $result;
//        } else {
//            $this->cache->set($query, $this->db->result(), false, 86400);
//            $this->db->query($query);
//        }
        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * Добавление бренда
     *
     * @param  array|object $brand
     * @return mixed
     */
    public function add_brand($brand)
    {
        $brand = (array)$brand;
        if (empty($brand['url'])) {
            $brand['url'] = preg_replace("/[\s]+/ui", '_', $brand['name']);
            $brand['url'] = strtolower(preg_replace("/[^0-9a-zа-я_]+/ui", '', $brand['url']));
        }

        $query= $this->db->placehold('DELETE FROM __deleted_brands WHERE url=?', $brand['url']);
        $this->db->query($query);

        $this->db->query("INSERT INTO __brands SET ?%", $brand);
        return $this->db->insert_id();

    }

    /**
     * Обновление бренда(ов)
     *
     * @param  int $id
     * @param  array|object $brand
     * @return mixed
     */
    public function update_brand($id, $brand)
    {
        $query = $this->db->placehold("UPDATE __brands SET ?% WHERE id=? LIMIT 1", $brand, intval($id));
        $this->db->query($query);
        return $id;
    }

    /**
     * Удаление бренда
     *
     * @param int $id
     * @return void
     */
    public function delete_brand($id)
    {
        if (!empty($id)) {
            $this->delete_image($id);

            $query = $this->db->placehold("SELECT url FROM __brands WHERE id=?", $id);
            $this->db->query($query);
            $url = $this->db->result('url');


            if (!empty($url)){

                $query = $this->db->placehold("INSERT INTO __deleted_brands SET url=?", $url);
                $this->db->query($query);
            }


            $query = $this->db->placehold("DELETE FROM __brands WHERE id=? LIMIT 1", $id);
            $this->db->query($query);

            $query = $this->db->placehold("UPDATE __products SET brand_id=NULL WHERE brand_id=?", $id);
            $this->db->query($query);
        }
    }

    /**
     * Удаление изображения бренда
     *
     * @param  int $brand_id
     * @return void
     */
    public function delete_image($brand_id)
    {
        $query = $this->db->placehold("SELECT image FROM __brands WHERE id=?", intval($brand_id));
        $this->db->query($query);
        $filename = $this->db->result('image');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __brands SET image=NULL WHERE id=?", $brand_id);
            $this->db->query($query);
            $query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE image=? LIMIT 1", $filename);
            $this->db->query($query);
            $count = $this->db->result('count');
            if ($count == 0) {
                @unlink($this->config->root_dir . $this->config->brands_images_dir . $filename);
            }
        }
    }

    /**
     * Удаление баннера бренда
     *
     * @param  int $brand_id
     * @return void
     */
    public function delete_banner($brand_id)
    {
        $query = $this->db->placehold("SELECT banner FROM __brands WHERE id=?", intval($brand_id));
        $this->db->query($query);
        $filename = $this->db->result('banner');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __brands SET banner=NULL WHERE id=?", $brand_id);
            $this->db->query($query);
            $query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE banner=? LIMIT 1", $filename);
            $this->db->query($query);
            $count = $this->db->result('count');
            if ($count == 0) {
                @unlink($this->config->root_dir . $this->config->brands_images_dir . $filename);
            }
        }
    }

    /**
     * Удаление картинки фона бренда
     *
     * @param  int $brand_id
     * @return void
     */
    public function delete_background($brand_id)
    {
        $query = $this->db->placehold("SELECT background FROM __brands WHERE id=?", intval($brand_id));
        $this->db->query($query);
        $filename = $this->db->result('background');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __brands SET background=NULL WHERE id=?", $brand_id);
            $this->db->query($query);
            $query = $this->db->placehold("SELECT count(*) as count FROM __brands WHERE background=? LIMIT 1", $filename);
            $this->db->query($query);
            $count = $this->db->result('count');
            if ($count == 0) {
                @unlink($this->config->root_dir . $this->config->brands_background_images_dir . $filename);
            }
        }
    }
}
