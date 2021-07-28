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

class Orders extends Simpla
{

    /**
     * @param $id
     * @return bool|object|string
     */
    public function get_order($id)
    {
        if (is_int($id)) {
            $where = $this->db->placehold(' WHERE o.id=? ', intval($id));
        } else {
            $where = $this->db->placehold(' WHERE o.url=? ', $id);
        }

        $query = $this->db->placehold("SELECT  o.id, o.delivery_id, o.delivery_price, o.separate_delivery, o.self_discharge_time,
										o.payment_method_id, o.paid, o.call_back, o.promo, o.payment_date, o.closed, o.discount, o.coupon_code, o.coupon_discount,
										o.date, o.user_id, o.name, o.address, o.flat_num, o.express, o.phone, o.email, o.comment, o.status, o.bonus_sale, o.bonus_id,
										o.url, o.total_price, o.note, o.wh_note, o.ip, o.courier_id, o.city_id/*regions*/, region_id/*/regions*/, o.custom_discount
										FROM __orders o $where LIMIT 1");

        if ($this->db->query($query)) {
            return $this->db->result();
        } else {
            return false;
        }
    }

    /**
     * @param array $filter
     * @return array
     */
    public function get_orders($filter = array())
    {
        // По умолчанию
        $limit = 100;
        $page = 1;
        $keyword_filter = '';
        $label_filter = '';
        $status_filter = '';
        $user_filter = '';
        $modified_since_filter = '';
        $id_filter = '';
        $date_delivery_filter = '';
        $delivery_metod_filter = '';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);


        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['id'])) {
            $id_filter = $this->db->placehold('AND o.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (isset($filter['modified_since'])) {
            $modified_since_filter = $this->db->placehold('AND o.modified > ?', $filter['modified_since']);
        }

        if (!empty($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['date_delivery'])) {
            $date_delivery_filter = $this->db->placehold('AND o.self_discharge_time LIKE ?', '%'.$filter['date_delivery'].'%');
        }

        if (isset($filter['total_date_delivery']) && !empty($filter['total_date_delivery'])){
            $date_delivery_filter_total = $this->db->placehold('AND o.date LIKE ?', $filter['total_date_delivery'].'%');
        }else{
            $date_delivery_filter_total = '';
        }

        if (!empty($filter['delivery_metod'])) {
            $delivery_metod_filter = $this->db->placehold('AND o.delivery_id = ?', $filter['delivery_metod']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.id = "' . $this->db->escape(trim($keyword)) . '" OR o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-',
                        '', trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        if (isset($filter['date_total']) && !empty($filter['date_total'])){
            $date_filter_total = $this->transformDateForQuery($filter);
        }else{
            $date_filter_total = '';
        }

        /*if (isset($filter['date_delivery_total']) && !empty($filter['date_delivery_total'])) {
            $query = $this->db->placehold("SELECT o.id, o.delivery_id, o.delivery_price, o.separate_delivery, o.self_discharge_time, o.payment_method_id, o.paid, o.call_back, o.promo, o.payment_date, o.closed, o.discount, o.coupon_code, o.coupon_discount, o.date, o.user_id, o.name, o.address, o.phone, o.email, o.comment, o.status, o.url, o.total_price, o.note, o.wh_note, region_id, o.custom_discount FROM __orders AS o LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id WHERE 1 	$id_filter $keyword_filter $label_filter $modified_since_filter $date_filter_total GROUP BY o.id ORDER BY o.date DESC $sql_limit", "%Y-%m-%d");
        }*/

        // Выбираем заказы
        if (!empty($filter['delivery_metod']) && !empty($filter['date_delivery'])) {
            $query = $this->db->placehold("SELECT o.id, o.delivery_id, o.delivery_price, o.separate_delivery, o.self_discharge_time,
										o.payment_method_id, o.paid, o.call_back, o.promo, o.payment_date, o.closed, o.discount, o.coupon_code, o.coupon_discount,
										o.date, o.user_id, o.name, o.address, o.flat_num, o.express, o.phone, o.email, o.comment, o.status,
										o.url, o.total_price, o.note, o.wh_note/*regions*/, region_id/*/regions*/, o.custom_discount
									FROM __orders AS o
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id
									WHERE 1
									$id_filter $status_filter $user_filter $keyword_filter $label_filter $modified_since_filter $date_delivery_filter $delivery_metod_filter GROUP BY o.id ORDER BY o.self_discharge_time ASC $sql_limit", "%Y-%m-%d");
        }
        else{
            $query = $this->db->placehold("SELECT o.id, o.delivery_id, o.delivery_price, o.separate_delivery, o.self_discharge_time,
										o.payment_method_id, o.paid, o.call_back, o.promo, o.payment_date, o.closed, o.discount, o.coupon_code, o.coupon_discount,
										o.date, o.user_id, o.name, o.address, o.flat_num, o.express, o.phone, o.email, o.comment, o.status,
										o.url, o.total_price, o.note, o.wh_note/*regions*/, region_id/*/regions*/, o.custom_discount
									FROM __orders AS o
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id
									WHERE 1
									$id_filter $status_filter $user_filter $keyword_filter $label_filter $modified_since_filter $date_delivery_filter $delivery_metod_filter GROUP BY o.id ORDER BY status, id DESC $sql_limit", "%Y-%m-%d");
        }

        $this->db->query($query);
        $orders = array();
        foreach ($this->db->results() as $order) {
            $orders[$order->id] = $order;
        }
        return $orders;
    }

    /**
     * @param array $filter
     * @return bool|object|string
     */
    public function count_orders($filter = array())
    {
        $keyword_filter = '';
        $label_filter = '';
        $status_filter = '';
        $user_filter = '';
        $date_delivery_filter = '';
        $delivery_metod_filter = '';
        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (!empty($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-', '',
                        trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        if (!empty($filter['date_delivery'])) {
            $date_delivery_filter = $this->db->placehold('AND o.self_discharge_time LIKE ?', '%'.$filter['date_delivery'].'%');
        }

        if (isset($filter['date_total']) && !empty($filter['date_total'])){

            $date_filter_total = $this->transformDateForQuery($filter);
        }else{
            $date_filter_total = '';
        }

        if (!empty($filter['delivery_metod'])) {
            $delivery_metod_filter = $this->db->placehold('AND o.delivery_id = ?', $filter['delivery_metod']);
        }

        // Выбираем заказы
        $query = $this->db->placehold("SELECT COUNT(DISTINCT id) as count
									FROM __orders AS o
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id
									WHERE 1
									$status_filter $user_filter $label_filter $keyword_filter $date_delivery_filter $delivery_metod_filter");
        $this->db->query($query);
        return $this->db->result('count');
    }

    /**
     * @param $filter
     * @return bool|mixed|string
     */
    private function transformDateForQuery($filter)
    {
        $date_total_start = $filter['date_total'] . ' 00:00:00';
        $date_total_end = $filter['date_total'] . ' 23:59:00';
        return $this->db->placehold('AND o.date BETWEEN \''.$date_total_start . '\' AND \''.$date_total_end . '\'');
    }

    /**
     * @param $id
     * @param $order
     * @return mixed
     */
    public function update_order($id, $order)
    {

        if (isset($order->courier_id)) {
            $_query = $this->db->placehold("SELECT area_id FROM __delivery_courier_to_area WHERE courier_id = '".(int)$order->courier_id."'");
            $this->db->query($_query);
            $_query_result = $this->db->result();
//            print_r($_query_result); exit;
            if (isset($_query_result->area_id) && $_query_result->area_id) {
                $order->courier_id = $_query_result->area_id;
            } else {
                // Show error and told to admin that
            }
        }
        $query = $this->db->placehold("UPDATE __orders SET ?%, modified=now() WHERE id=? LIMIT 1", $order, intval($id));
        $this->db->query($query);
        $this->update_total_price(intval($id));

        return $id;
    }


    public function check_status_new($id, $order) //проверка. статус изменился на новый
    {
        if (is_array($order)){
            $old_status = $this->get_order($id)->status;
            if ($old_status!=$order['status']){
                return true;
            }
        }
        return false;
    }
    /**
     * @param $id
     */
    public function delete_order($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __purchases WHERE order_id=?", $id);
            $this->db->query($query);

            $query = $this->db->placehold("DELETE FROM __orders_labels WHERE order_id=?", $id);
            $this->db->query($query);

            $query = $this->db->placehold("DELETE FROM __orders WHERE id=? LIMIT 1", $id);
            $this->db->query($query);
        }
    }

    /**
     * @param $order
     * @return mixed
     */
    public function add_order($order)
    {
        $order = (object)$order;
		/*
	    echo '<pre>';
        print_r($order);
		print_r($_SESSION);
        echo '</pre>';
        */

        $order->url = md5(uniqid($this->config->salt, true));
        $set_curr_date = '';
        if (empty($order->date)) {
            $set_curr_date = ', date=now()';
        }

        $query = $this->db->placehold("INSERT INTO __orders SET ?%$set_curr_date", $order);

        $this->db->query($query);
		 /*echo '<pre>';
		print_r($this->db->result());
		 echo '</pre>';
		 */
		$id = $this->db->insert_id();
        return $id;
    }

    /**
     * @param $id
     * @return bool|object|string
     */
    public function get_label($id)
    {
        $query = $this->db->placehold("SELECT * FROM __labels WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * @return array|bool
     */
    public function get_labels()
    {
        $query = $this->db->placehold("SELECT * FROM __labels ORDER BY position");
        $this->db->query($query);
        return $this->db->results();
    }


    /**
     * Создание метки заказов
     *
     * @param $label
     * @return bool|mixed
     */
    public function add_label($label)
    {
        $query = $this->db->placehold('INSERT INTO __labels SET ?%', $label);
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query("UPDATE __labels SET position=id WHERE id=?", $id);
        return $id;
    }


    /**
     * Обновить метку
     *
     * @param $id
     * @param $label
     * @return mixed
     */
    public function update_label($id, $label)
    {
        $query = $this->db->placehold("UPDATE __labels SET ?% WHERE id in(?@) LIMIT ?", $label, (array)$id, count((array)$id));
        $this->db->query($query);
        return $id;
    }

    /**
     * Удалить метку
     *
     * @param $id
     * @return bool|mixed
     */
    public function delete_label($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __orders_labels WHERE label_id=?", intval($id));
            if ($this->db->query($query)) {
                $query = $this->db->placehold("DELETE FROM __labels WHERE id=? LIMIT 1", intval($id));
                return $this->db->query($query);
            } else {
                return false;
            }
        }
    }

    /**
     * @param array $order_id
     * @return array|bool
     */
    public function get_order_labels($order_id = array())
    {
        if (empty($order_id)) {
            return array();
        }

        $label_id_filter = $this->db->placehold('AND order_id in(?@)', (array)$order_id);

        $query = $this->db->placehold("SELECT ol.order_id, l.id, l.name, l.color, l.position
					FROM __labels l LEFT JOIN __orders_labels ol ON ol.label_id = l.id
					WHERE
					1
					$label_id_filter
					ORDER BY position
					");

        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * @param $id
     * @param $labels_ids
     */
    public function update_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        $query = $this->db->placehold("DELETE FROM __orders_labels WHERE order_id=?", intval($id));
        $this->db->query($query);
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("INSERT INTO __orders_labels SET order_id=?, label_id=?", $id, $l_id);
            }
        }
    }

    /**
     * @param $id
     * @param $labels_ids
     */
    public function add_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("INSERT IGNORE INTO __orders_labels SET order_id=?, label_id=?", $id, $l_id);
            }
        }
    }

    /**
     * @param $id
     * @param $labels_ids
     */
    public function delete_order_labels($id, $labels_ids)
    {
        $labels_ids = (array)$labels_ids;
        if (is_array($labels_ids)) {
            foreach ($labels_ids as $l_id) {
                $this->db->query("DELETE FROM __orders_labels WHERE order_id=? AND label_id=?", $id, $l_id);
            }
        }
    }

    /* couriers
    */
    public function get_courier($id)
    {
        $query = $this->db->placehold("SELECT * FROM __delivery_couriers WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }
    public function get_courier_base()
    {
        $query = $this->db->placehold("SELECT * FROM __delivery_couriers WHERE start=1 LIMIT 1");
        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * @return array|bool
     */
    public function get_couriers()
    {
        $query = $this->db->placehold("SELECT * FROM __delivery_couriers ORDER BY position");
        $this->db->query($query);
        return $this->db->results();
    }
    public function get_order_courier($order_id)
    {
        $query = $this->db->placehold("SELECT `dc`.`id` as `order_courier_id`, `o`.`courier_id` as `order_area_id` FROM __orders o LEFT JOIN __delivery_courier_to_area dca ON (`o`.`courier_id` = `dca`.`area_id`) LEFT JOIN __delivery_couriers dc ON (`dca`.`courier_id` = `dc`.`id`) WHERE `o`.`id` = ?", (int)$order_id);
        $this->db->query($query);
        return $this->db->result();
    }
    /*    добавить */
    public function add_courier($courier)
    {
        $query = $this->db->placehold('INSERT INTO __delivery_couriers SET ?%', $courier);
        if (!$this->db->query($query)) {
            return false;
        }

        $id = $this->db->insert_id();
        $this->db->query("UPDATE __delivery_couriers SET position=id WHERE id=?", $id);
        return $id;
    }

    public function get_date_order($date_time)
    {
        $date_time = trim($date_time);
        $date = stristr($date_time,' ', true);
        $date = trim($date);
        return $date;
    }
    public function get_time_order($date_time)
    {
        $date_time = trim($date_time);
        $time = stristr($date_time,' ', false);
        $time = trim($time);
        return $time;
    }

    /**
     * Обновить
     */
    public function update_courier($id, $courier)
    {
        $query = $this->db->placehold("UPDATE __delivery_couriers SET ?% WHERE id in(?@) LIMIT ?", $courier, (array)$id, count((array)$id));
        $this->db->query($query);
        return $id;
    }

    /**
     * Удалить
     */
    public function delete_courier($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("UPDATE __orders SET courier_id=NULL WHERE courier_id=?", intval($id));
            if ($this->db->query($query)) {
                $query = $this->db->placehold("DELETE FROM __delivery_couriers WHERE id=? LIMIT 1", intval($id));
                return $this->db->query($query);
            } else {
                return false;
            }
        }
    }
    /* couriers */

    /**
     * @param $id
     * @return bool|object|string
     */
    public function get_purchase($id)
    {
        $query = $this->db->placehold("SELECT * FROM __purchases WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * @param array $filter
     * @return array|bool
     */
    public function get_purchases($filter = array())
    {
        $order_id_filter = '';
        if (!empty($filter['order_id'])) {
            $order_id_filter = $this->db->placehold('AND order_id in(?@)', (array)$filter['order_id']);
        }

        $query = $this->db->placehold("SELECT * FROM __purchases WHERE 1 $order_id_filter ORDER BY id");
        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * @param $id
     * @param $purchase
     * @return bool
     */
    public function update_purchase($id, $purchase)
    {
        $purchase = (object)$purchase;
        $old_purchase = $this->get_purchase($id);
        if (!$old_purchase) {
            return false;
        }

        $order = $this->get_order(intval($old_purchase->order_id));
        if (!$order) {
            return false;
        }

        // Не допустить нехватки на складе
        $variant = $this->variants->get_variant($purchase->variant_id);
        if ($order->closed && !empty($purchase->amount) && !empty($variant) && !$variant->infinity && $variant->stock < ($purchase->amount - $old_purchase->amount)) {
            return false;
        }

        // Если заказ закрыт, нужно обновить склад при изменении покупки
        if ($order->closed && !empty($purchase->amount)) {
            if ($old_purchase->variant_id != $purchase->variant_id) {
                if (!empty($old_purchase->variant_id)) {
                    $query = $this->db->placehold("UPDATE __variants SET stock=stock+? WHERE id=? AND stock IS NOT NULL LIMIT 1", $old_purchase->amount, $old_purchase->variant_id);
                    $this->db->query($query);
                }
                if (!empty($purchase->variant_id)) {
                    $query = $this->db->placehold("UPDATE __variants SET stock=stock-? WHERE id=? AND stock IS NOT NULL LIMIT 1", $purchase->amount, $purchase->variant_id);
                    $this->db->query($query);
                }
            } elseif (!empty($purchase->variant_id)) {
                $query = $this->db->placehold("UPDATE __variants SET stock=stock+(?) WHERE id=? AND stock IS NOT NULL LIMIT 1", $old_purchase->amount - $purchase->amount, $purchase->variant_id);
                $this->db->query($query);
            }
        }

        $query = $this->db->placehold("UPDATE __purchases SET ?% WHERE id=? LIMIT 1", $purchase, intval($id));
        $this->db->query($query);
        $this->update_total_price($order->id);
        return $id;
    }

    /**
     * @param $purchase
     * @return mixed
     */
    public function add_purchase($purchase)
    {
        $purchase = (object)$purchase;
        if (!empty($purchase->variant_id)) {
            $variant = $this->variants->get_variant($purchase->variant_id);
            if (empty($variant)) {
                return false;
            }
            $product = $this->products->get_product(intval($variant->product_id));
            if (empty($product)) {
                return false;
            }
        }

        $order = $this->get_order(intval($purchase->order_id));
        if (empty($order)) {
            return false;
        }

        // Не допустить нехватки на складе
        if ($order->closed && !empty($purchase->amount) && !$variant->infinity && $variant->stock < $purchase->amount) {
            return false;
        }

        if (!isset($purchase->product_id) && isset($variant)) {
            $purchase->product_id = $variant->product_id;
        }

        if (!isset($purchase->product_name) && !empty($product)) {
            $purchase->product_name = $product->name;
        }

        if (!isset($purchase->sku) && !empty($variant)) {
            $purchase->sku = $variant->sku;
        }

        if (!isset($purchase->variant_name) && !empty($variant)) {
            $purchase->variant_name = $variant->name;
        }

        if (!isset($purchase->price) && !empty($variant)) {
            $purchase->price = $variant->price;
        }

        if (!isset($purchase->amount)) {
            $purchase->amount = 1;
        }

        // Если заказ закрыт, нужно обновить склад при добавлении покупки
        if ($order->closed && !empty($purchase->amount) && !empty($variant->id)) {
            $stock_diff = $purchase->amount;
            $query = $this->db->placehold("UPDATE __variants SET stock=stock-? WHERE id=? AND stock IS NOT NULL LIMIT 1", $stock_diff, $variant->id);
            $this->db->query($query);
        }

        $query = $this->db->placehold("INSERT INTO __purchases SET ?%", $purchase);
        $this->db->query($query);
        $purchase_id = $this->db->insert_id();

        $this->update_total_price($order->id);
        return $purchase_id;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete_purchase($id)
    {
        $purchase = $this->get_purchase($id);
        if (!$purchase) {
            return false;
        }

        $order = $this->get_order(intval($purchase->order_id));
        if (!$order) {
            return false;
        }

        // Если заказ закрыт, нужно обновить склад при изменении покупки
        if ($order->closed && !empty($purchase->amount)) {
            $stock_diff = $purchase->amount;
            $query = $this->db->placehold("UPDATE __variants SET stock=stock+? WHERE id=? AND stock IS NOT NULL LIMIT 1", $stock_diff, $purchase->variant_id);
            $this->db->query($query);
        }

        $query = $this->db->placehold("DELETE FROM __purchases WHERE id=? LIMIT 1", intval($id));
        $this->db->query($query);
        $this->update_total_price($order->id);
        return true;
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function close($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if (!$order->closed) {
            $variants_amounts = array();
            $purchases = $this->get_purchases(array('order_id' => $order->id));
            foreach ($purchases as $purchase) {
                if (isset($variants_amounts[$purchase->variant_id])) {
                    $variants_amounts[$purchase->variant_id] += $purchase->amount;
                } else {
                    $variants_amounts[$purchase->variant_id] = $purchase->amount;
                }
            }

            foreach ($variants_amounts as $id => $amount) {
                $variant = $this->variants->get_variant($id);
                if (empty($variant)/* || ($variant->stock < $amount)*/) {
                    return false;
                }
            }
            foreach ($purchases as $purchase) {
                $variant = $this->variants->get_variant($purchase->variant_id);
                if (!$variant->infinity) {
                    $new_stock = $variant->stock - $purchase->amount;
                    $this->variants->update_variant($variant->id, array('stock' => $new_stock));
                }
            }
            $query = $this->db->placehold("UPDATE __orders SET closed=1, modified=NOW() WHERE id=? LIMIT 1", $order->id);
            $this->db->query($query);
        }
        return $order->id;
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function open($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if ($order->closed) {
            $purchases = $this->get_purchases(array('order_id' => $order->id));
            foreach ($purchases as $purchase) {
                $variant = $this->variants->get_variant($purchase->variant_id);
                if ($variant && !$variant->infinity) {
                    $new_stock = $variant->stock + $purchase->amount;
                    $this->variants->update_variant($variant->id, array('stock' => $new_stock));
                }
            }
            $query = $this->db->placehold("UPDATE __orders SET closed=0, modified=NOW() WHERE id=? LIMIT 1", $order->id);
            $this->db->query($query);
        }
        return $order->id;
    }

    /**
     * @param $order_id
     * @return bool
     */
    public function pay($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        if (!$this->close($order->id)) {
            return false;
        }
        $query = $this->db->placehold("UPDATE __orders SET payment_status=1, payment_date=NOW(), modified=NOW() WHERE id=? LIMIT 1", $order->id);
        $this->db->query($query);
        return $order->id;
    }

    /**
     * @param $id
     * @param null $status
     * @return bool|object|string
     */
    public function get_next_order($id, $status = null)
    {
        $f = '';
        if ($status !== null) {
            $f = $this->db->placehold('AND status=?', $status);
        }
        $this->db->query("SELECT MIN(id) as id FROM __orders WHERE id>? $f LIMIT 1", $id);
        $next_id = $this->db->result('id');
        if ($next_id) {
            return $this->get_order(intval($next_id));
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @param null $status
     * @return bool|object|string
     */
    public function get_prev_order($id, $status = null)
    {
        $f = '';
        if ($status !== null) {
            $f = $this->db->placehold('AND status=?', $status);
        }
        $this->db->query("SELECT MAX(id) as id FROM __orders WHERE id<? $f LIMIT 1", $id);
        $prev_id = $this->db->result('id');
        if ($prev_id) {
            return $this->get_order(intval($prev_id));
        } else {
            return false;
        }
    }

    /**
     * @param $order_id
     * @return bool
     */
    private function update_total_price($order_id)
    {
        $order = $this->get_order(intval($order_id));
        if (empty($order)) {
            return false;
        }

        $query = $this->db->placehold("UPDATE __orders o SET o.total_price=IFNULL((SELECT SUM(p.price*p.amount)*(100-o.discount)/100 FROM __purchases p WHERE p.order_id=o.id), 0)+o.delivery_price*(1-o.separate_delivery)-o.coupon_discount, modified=NOW() WHERE o.id=? LIMIT 1",
            $order->id);
        $this->db->query($query);
        return $order->id;
    }

    public function sum_price($filter = array())//Сумма всех заказов пользователя
    {
        $keyword_filter = '';
        $label_filter = '';
        $status_filter = '';
        $user_filter = '';

        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (!empty($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-', '',
                        trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        // Выбираем заказы
        $query = $this->db->placehold("SELECT SUM(o.total_price) as sum
									FROM __orders AS o
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id
									WHERE 1
									$status_filter $user_filter $label_filter $keyword_filter");
        $this->db->query($query);
        return $this->db->result('sum');
    }

    public function sum_sale($filter = array()) //Сумма скидки всех заказов пользователя
    {
        $keyword_filter = '';
        $label_filter = '';
        $status_filter = '';
        $user_filter = '';

        if (isset($filter['status'])) {
            $status_filter = $this->db->placehold('AND o.status = ?', intval($filter['status']));
        }

        if (isset($filter['user_id'])) {
            $user_filter = $this->db->placehold('AND o.user_id = ?', intval($filter['user_id']));
        }

        if (!empty($filter['label'])) {
            $label_filter = $this->db->placehold('AND ol.label_id = ?', $filter['label']);
        }

        if (!empty($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (o.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR REPLACE(o.phone, "-", "")  LIKE "%' . $this->db->escape(str_replace('-', '',
                        trim($keyword))) . '%" OR o.address LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
            }
        }

        // Выбираем заказы
        $query = $this->db->placehold("SELECT SUM(o.coupon_discount) as sale
									FROM __orders AS o
									LEFT JOIN __orders_labels AS ol ON o.id=ol.order_id
									WHERE 1
									$status_filter $user_filter $label_filter $keyword_filter");
        $this->db->query($query);
        return $this->db->result('sale');
    }

//    public function send_sms($text_sms, $number_phone){
//        echo 'Отправляем смс на номер '.$number_phone.' : '.$text_sms.'<br>';
//        require_once 'api/UnisenderApi.php';
//        $apikey=$this->settings->api_unisend_key; //API-ключ к вашему кабинету
//        $uni = new Unisender\ApiWrapper\UnisenderApi($apikey); //создаем экземляр класса,
//        $sender=$this->settings->api_unisend_name;//имя отправителя
//        if (empty($sender)){
//            echo "Ошибка: Не задано имя отправителя<br>";
//            return false;
//        }
//        if (empty($apikey)){
//            echo "Ошибка: Не задан ключ<br>";
//            return false;
//        }
//        $result=$uni->sendSms(Array("phone"=>$number_phone,"sender"=>$sender,"text"=>$text_sms));
//        $result = json_decode($result, true);
//        if ($result['error']){
//            echo 'Ошибка отправки: '.$result['error'].'<br>';
//        }
//        else{
//            echo 'Сообщение отправлено<br>';
//        }
//    }

    public function send_sms($text_sms, $phone_number){

        $username = $this->settings->mts_login;
        $password = $this->settings->mts_password;
        $client_id = (int)$this->settings->mts_client_id;

        /* Send information */
        $item->phone_number = str_replace(array('+', ' ', '-', '_'), '', $phone_number);
        $item->extra_id = uniqid();
        $item->callback_url = 'https://animals.pf.by';
        $item->tag = 'Client sms';
        $item->channels = array('sms');
        $item->channel_options->sms->text = $text_sms;
        $item->channel_options->sms->alpha_name = 'ezoo.by';
        $item->channel_options->sms->ttl = '600';
        $item = json_encode($item);

        /* cURL send request SMS */
        $ch = curl_init('https://api.connect.mts.by/' . $client_id . '/json2/simple');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode($username . ":" .$password)
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $item);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);

        if($res === false)
        {
            echo 'Ошибка curl: ' . curl_error($ch) . '<br>';
        }
        else
        {
            $res = json_decode($res);
            echo 'Сообщение отправлено (Message ID: ' . $res->message_id . ') на номер: ' . $phone_number . '<br>Текст сообщения: ' . $text_sms;
        }
        curl_close($ch);
    }
}
