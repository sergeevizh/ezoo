<?php
require_once 'Simpla.php';

class Regions extends Simpla {

    /*
     *
     * Функция возвращает страницу по ее id или url (в зависимости от типа)
     * @param $id id или url страницы
     *
     */
    public function get_region($id) {
        if (gettype($id) == 'string') {
            $where = $this->db->placehold(' WHERE url=? ', $id);
        } else {
            $where = $this->db->placehold(' WHERE id=? ', intval($id));
        }

        $query = "SELECT * FROM __regions $where LIMIT 1";

        $this->db->query($query);
        return $this->db->result();
    }

    /*
     *
     * Функция возвращает массив страниц, удовлетворяющих фильтру
     * @param $filter
     *
     */
    public function get_regions($filter = array()) {
        $enabled_filter = '';
        $regions        = array();

        if (isset($filter['enabled'])) {
            $enabled_filter = $this->db->placehold('AND enabled = ?', intval($filter['enabled']));
        }

        $query = "SELECT * FROM __regions WHERE 1 $enabled_filter ORDER BY id";

        $this->db->query($query);

        foreach ($this->db->results() as $region) {
            $regions[$region->id] = $region;
        }

        return $regions;
    }

    /*
     *
     * Создание страницы
     *
     */
    public function add_region($region) {

        /* fix */
        if( $region->id == 0) unset($region->id);
        /* / fix */

        $query = $this->db->placehold('INSERT INTO __regions SET ?%', $region);
         
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        return $id;
    }

    /*
     *
     * Обновить страницу
     *
     */
    public function update_region($id, $region) {
        $query = $this->db->placehold('UPDATE __regions SET ?% WHERE id in (?@)', $region, (array) $id);
        $this->db->query($query);
        return $id;
    }

    /*
     *
     * Удалить страницу
     *
     */
    public function delete_region($id) {
        if (!empty($id)) {
            $this->delete_variant_price_column(intval($id));

            $query = $this->db->placehold("DELETE FROM __regions WHERE id=? LIMIT 1", intval($id));
            if ($this->db->query($query)) {
                return true;
            }

        }
        return false;
    }

    /* fix */
    public function add_variant_price_column($id) {
        if (!is_int($id)) {
            return;
        }
        //не забыть обновить новые цены в стандартные цены товара.
        $query = $this->db->placehold("alter table __variants add column `region_stock_?` mediumint(9) default NULL", intval($id));
        $this->db->query($query);
        $query = $this->db->placehold("update __variants set `region_stock_?`=0", intval($id));
        $this->db->query($query);
    }
     /* fix */
    public function delete_variant_price_column($id) {
        if (!is_int($id)) {
            return;
        }
        $query = $this->db->placehold("alter table __variants drop column `region_stock_?`", intval($id));
        $this->db->query($query);
    }
}
