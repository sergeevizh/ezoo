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

/**
 * API Варианты товаров
 *
 * Class Variants
 */
class Variants extends Simpla
{
    /**
     * Функция возвращает варианты товара
     *
     * @param    $filter
     * @return    array
     */
    public function get_variants($filter = array())
    {
        $product_id_filter = '';
        $variant_id_filter = '';
        $in_stock_filter = '';

        /*regions*/
        $regions_ids = array_keys($this->regions->get_regions(array('enabled' => 1)));
        $regions_stocks = '';
        foreach($regions_ids as $r_id){
            $regions_stocks .= " v.region_stock_" . intval($r_id) . ", ";
        }
        /*/regions*/

        if (!empty($filter['product_id'])) {
            $product_id_filter = $this->db->placehold('AND v.product_id in(?@)', (array)$filter['product_id']);
        }

        if (!empty($filter['id'])) {
            $variant_id_filter = $this->db->placehold('AND v.id in(?@)', (array)$filter['id']);
        }

        if (!empty($filter['in_stock']) && $filter['in_stock']) {
            $in_stock_filter = $this->db->placehold('AND (v.stock>0 OR v.stock IS NULL)');
        }

        if (!$product_id_filter && !$variant_id_filter) {
            return array();
        }

        $query = $this->db->placehold("SELECT /*regions*/$regions_stocks/*/regions*/ v.id, v.product_id , v.price, NULLIF(v.compare_price, 0) as compare_price, v.sale_end, v.sku, IFNULL(v.stock, ?) as stock, (v.stock IS NULL) as infinity, v.name, v.attachment, v.position
					FROM __variants AS v
					WHERE 1
					    $product_id_filter
					    $variant_id_filter
					    $in_stock_filter
					ORDER BY v.position
					", $this->settings->max_order_amount);

        if ($results = $this->cache->get($query)){
            $res = $results;
        } else {
            $this->db->query($query);
            $res = $this->db->results();
            $this->cache->set($query, $res, false, 86400);
        }

          foreach ($res as &$row) {
            $supply_dates = $this->dates->getVariantsSupplyDates(array('variant_id' => (int)$row->id));

            if (!empty($supply_dates)) {
                $row->supply_dates = array();
                foreach ($supply_dates as $supply_date) {
                    $row->supply_dates[$supply_date->region_id] = $supply_date->{date};
                }
            }

            if (isset($_SESSION['region_id']) && defined('IS_CLIENT')) {
                $region_stock = 'region_stock_' . intval($_SESSION['region_id']);
                if (property_exists($row, $region_stock)) {
                    $row->stock = $row->{$region_stock};
                }
            }
        }

        /*regions*/
        //return $this->db->results();
        return $res;
        /*/regions*/
    }

    /**
     * Функция возвращает вариант
     *
     * @param  int $id
     * @return bool|object
     */
    public function get_variant($id)
    {
        if (empty($id)) {
            return false;
        }

        /*regions*/
        $regions_ids = array_keys($this->regions->get_regions(array('enabled' => 1)));
        $regions_stocks = '';
        foreach($regions_ids as $r_id){
            $regions_stocks .= " v.region_stock_" . intval($r_id) . ", ";
        }
        /*/regions*/

        $query = $this->db->placehold("SELECT /*regions*/$regions_stocks/*/regions*/ v.id, v.product_id , v.price, NULLIF(v.compare_price, 0) as compare_price, v.sale_end, v.sku, IFNULL(v.stock, ?) as stock, (v.stock IS NULL) as infinity, v.name, v.attachment
					FROM __variants v WHERE v.id=?
					LIMIT 1", $this->settings->max_order_amount, $id);

//        if ($result = $this->cache->get($query)){
//            $variant = $result;
//        } else {
//            $this->db->query($query);
//            $variant = $this->db->result();
//            $this->cache->set($query, $variant, false, 86400);
//        }
        $this->db->query($query);
        $variant = $this->db->result();
        /*regions*/
        if (isset($_SESSION['region_id']) && defined('IS_CLIENT')) {
            $region_stock = 'region_stock_'.intval($_SESSION['region_id']);
            if (property_exists($variant, $region_stock)) {
                $variant->stock = $variant->{$region_stock};
            }
        }
        /*/regions*/

        $supply_dates = $this->dates->getVariantsSupplyDates(array('variant_id' => (int)$id));

        if (!empty($supply_dates)) {
            $variant->supply_dates = array();
            foreach ($supply_dates as $supply_date) {
                $variant->supply_dates[$supply_date->region_id] = $supply_date->date;
            }
        }



        return $variant;
    }

    /**
     * @param  int $id
     * @param  array|object $variant
     * @return int
     */
    public function update_variant($id, $variant)
    {

        if (isset($variant->variant_supply)) {
            foreach ($variant->variant_supply as $region => $date) {

                $date_record = $this->dates->getVariantsSupplyDates(array('variant_id' => (int)$id, 'region_id' => (int)$region));

                if (empty($date_record) && !empty($date)) {
                    $this->dates->addVariantSupplyDate(array('variant_id' => (int)$id, 'region_id' => (int)$region, 'date' => $date));
                } else {
                    if (!empty($date)) {
                        $this->dates->updateVariantSupplyDate(array('variant_id' => (int)$id, 'region_id' => (int)$region, 'date' => $date));
                    } else {
                        $this->dates->deleteVariantSupplyDate(array('variant_id' => (int)$id, 'region_id' => (int)$region));
                    }
                }


            }
        }

        unset($variant->variant_supply);

        $query = $this->db->placehold("UPDATE __variants SET ?% WHERE id=? LIMIT 1", $variant, intval($id));
        $this->db->query($query);
        return $id;
    }

    /**
     * @param  array|object $variant
     * @return int
     */
    public function add_variant($variant)
    {

        if (isset($variant->variant_supply)) {
            foreach ($variant->variant_supply as $region => $date) {

                $date_record = $this->dates->getVariantsSupplyDates(array('variant_id' => (int)$variant->id, 'region_id' => $region));
                if (empty($date_record) && !empty($date)) {
                    $this->dates->addVariantSupplyDate(array('variant_id' => (int)$variant->id, 'region_id' => $region, 'date' => $date));
                } else {
                    if (!empty($date)) {
                        $this->dates->updateVariantSupplyDate(array('variant_id' => (int)$variant->id, 'region_id' => $region, 'date' => $date));
                    } else {
                        $this->dates->deleteVariantSupplyDate(array('variant_id' => (int)$variant->id, 'region_id' => $region));
                    }
                }


            }
        }

        unset($variant->variant_supply);

        $query = $this->db->placehold("INSERT INTO __variants SET ?%", $variant);
        $this->db->query($query);
        return $this->db->insert_id();
    }

    /**
     * @param  int $id
     * @return void
     */
    public function delete_variant($id)
    {
        if (!empty($id)) {

            $this->delete_attachment($id);

            $this->db->query("DELETE FROM __variants WHERE id = ? LIMIT 1", intval($id));

            $this->db->query('UPDATE __purchases SET variant_id=NULL WHERE variant_id=?', intval($id));

            $this->dates->deleteVariantSupplyDate(array('variant_id' => (int)$id));
        }
    }

    /**
     * @param  int $id
     * @return void
     */
    public function delete_attachment($id)
    {
        $query = $this->db->placehold("SELECT attachment FROM __variants WHERE id=?", $id);
        $this->db->query($query);
        $filename = $this->db->result('attachment');

        $query = $this->db->placehold("SELECT 1 FROM __variants WHERE attachment=? AND id!=?", $filename, $id);
        $this->db->query($query);
        $exists = $this->db->num_rows();

        if (!empty($filename) && $exists == 0) {
            @unlink($this->config->root_dir . '/' . $this->config->downloads_dir . $filename);
        }
        $this->update_variant($id, array('attachment' => null));
    }
}
