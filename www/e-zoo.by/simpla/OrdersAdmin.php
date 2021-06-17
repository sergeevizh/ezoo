<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('api/Simpla.php');

class OrdersAdmin extends Simpla
{
    public function fetch()
    {
        $filter = array();
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        $filter['limit'] = 40;

        // Поиск
        $keyword = $this->request->get('keyword', 'string');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
            $this->design->assign('keyword', $keyword);
        }

        // Фильтр по метке
        $label = $this->orders->get_label($this->request->get('label'));
        if (!empty($label)) {
            $filter['label'] = $label->id;
            $this->design->assign('label', $label);
        }

        // Обработка действий
        if ($this->request->method('post')) {

            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete': {
                        foreach ($ids as $id) {
                            $o = $this->orders->get_order(intval($id));
                            if ($o->status < 3) {
                                $this->orders->update_order($id, array('status' => 3));
                                $this->orders->open($id);
                            } else {
                                $this->orders->delete_order($id);
                            }
                        }
                        break;
                    }
                    case 'set_status_0': {
                        foreach ($ids as $id) {
                            if ($this->orders->open(intval($id))) {
                                $this->orders->update_order($id, array('status' => 0));
                            }
                        }
                        break;
                    }
                    case 'set_status_1': {
                        foreach ($ids as $id) {
                            if (!$this->orders->close(intval($id))) {
                                $this->design->assign('message_error', 'error_closing');
                            } else {
                                $order = $this->orders->get_order(intval($id));
                                $phoneOrder = $string = preg_replace('~\D+~','',$order->phone);
                                if ($order->city_id==0) {  //для минска
                                    $errorSMS = false;
                                    if ($this->orders->check_status_new($order->id, array('status' => 1))) {
                                        $mask_sms_1 = $this->settings->mask_sms_1;
                                        if ($mask_sms_1) {
                                            $sms_1 = str_replace("%NUMBER%", $order->id, $mask_sms_1);
                                        } else {
                                            echo 'Шаблон маски не задан<br>';
                                            $errorSMS = true;
                                        }
                                        if (!$errorSMS) $this->orders->send_sms($sms_1, $phoneOrder);
                                    }
                                }
                                $this->orders->update_order($id, array('status' => 1));
                            }
                        }
                        break;
                    }
                    case 'set_status_2': {
                        foreach ($ids as $id) {
                            if (!$this->orders->close(intval($id))) {
                                $this->design->assign('message_error', 'error_closing');
                            } else {
                                $order = $this->orders->get_order(intval($id));
                                $phoneOrder = $string = preg_replace('~\D+~','',$order->phone);
                                $date_and_time = explode(' ', $order->self_discharge_time);
                                unset($date_and_time[0]);
                                $time = implode('', $date_and_time);
                                if (($order->total_price >= $this->settings->promo_price && in_array($time, $this->settings->promo_time))
                                    && ($order->total_price >= $this->settings->promo_price_second && in_array($time, $this->settings->promo_time_second))) {
                                    $errorSMS = false;
                                    $mask = $this->settings->mask_sms_promo;
                                    $mask_second = $this->settings->mask_sms_promo_second;
                                    if ($mask && $mask_second) {
                                        $promo = $this->promo->get_first_code();
                                        $promo_second = $this->promo_second->get_first_code();
                                        if ($promo && $promo_second) {
                                            $sms = str_replace("%PROMO%", $promo->code, $mask);
                                            $sms_second = str_replace("%PROMO%", $promo->code, $mask_second);
                                            $this->promo->delete_code($promo->id);
                                            $this->promo_second->delete_code($promo_second->id);
                                        } else if ($promo) {
                                            $sms = str_replace("%PROMO%", $promo->code, $mask);
                                            $this->promo->delete_code($promo->id);
                                        } else if ($promo_second) {
                                            $sms_second = str_replace("%PROMO%", $promo_second->code, $mask_second);
                                            $this->promo_second->delete_code($promo_second->id);
                                        } else {
                                            echo 'Промокоды закончились<br>';
                                            $errorSMS = true;
                                        }
                                    } else {
                                        echo 'Шаблон маски не задан<br>';
                                        $errorSMS = true;
                                    }
                                    if (!$errorSMS) {
                                        $this->orders->send_sms($sms, $phoneOrder);
                                        $this->orders->send_sms($sms_second, $phoneOrder);
                                    }
                                } else if ($order->total_price >= $this->settings->promo_price && in_array($time, $this->settings->promo_time)) {
                                    $errorSMS = false;
                                    $mask = $this->settings->mask_sms_promo;
                                    if ($mask) {
                                        $promo = $this->promo->get_first_code();
                                        if ($promo) {
                                            $sms = str_replace("%PROMO%", $promo->code, $mask);
                                            $this->promo->delete_code($promo->id);
                                        } else {
                                            echo 'Промокоды закончились<br>';
                                            $errorSMS = true;
                                        }
                                    } else {
                                        echo 'Шаблон маски не задан<br>';
                                        $errorSMS = true;
                                    }
                                    if (!$errorSMS) $this->orders->send_sms($sms, $phoneOrder);
                                } else if ($order->total_price >= $this->settings->promo_price_second && in_array($time, $this->settings->promo_time_second)) {
                                    $errorSMS = false;
                                    $mask_second = $this->settings->mask_sms_promo_second;
                                    if ($mask_second) {
                                        $promo_second = $this->promo_second->get_first_code();
                                        if ($promo_second) {
                                            $sms_second = str_replace("%PROMO%", $promo_second->code, $mask_second);
                                            $this->promo_second->delete_code($promo_second->id);
                                        } else {
                                            echo 'Промокоды закончились<br>';
                                            $errorSMS = true;
                                        }
                                    } else {
                                        echo 'Шаблон маски не задан<br>';
                                        $errorSMS = true;
                                    }
                                    if (!$errorSMS) $this->orders->send_sms($sms_second, $phoneOrder);

                                }
                                $this->orders->update_order($id, array('status' => 2));
                            }
                        }
                        break;
                    }
                    case(preg_match('/^set_label_([0-9]+)/', $this->request->post('action'), $a) ? true : false): {
                        $l_id = intval($a[1]);
                        if ($l_id > 0) {
                            foreach ($ids as $id) {
                                if ($l_id == 7){
                                    $order = $this->orders->get_order(intval($id));
                                    if ($order->city_id == 0)
                                    {
                                        $checkSobran = true;
                                        $getlablOrderTemp = $this->orders->get_order_labels(array('order_id' => $id));
                                        foreach ($getlablOrderTemp as $lablOrderTemp) {
                                            if ($lablOrderTemp->id == 7) {
                                                $checkSobran = false;
                                            }
                                        }
                                        if ($checkSobran){
                                            $phoneOrder = $string = preg_replace('~\D+~','',$order->phone);
                                            if ($order->delivery_id == 1) { //курьер
                                                $date_order = $this->orders->get_date_order($order->self_discharge_time);
                                                $date_time = $this->orders->get_time_order($order->self_discharge_time);
                                                if (strlen($date_order) > 6 && strlen($date_time) > 6) {
                                                    if (strtotime(date("d.m.Y")) == strtotime($date_order)) {
                                                        $date_order = 'сегодня';
                                                    }
                                                } else {
                                                    echo 'ошибка формирования смс (дата/время)<br>';
                                                    $errorSMS = true;
                                                }
                                                $courierOrder = $this->orders->get_courier($order->courier_id);
                                                if ($courierOrder) {
                                                    $phoneCourier = $courierOrder->phone_courier;
                                                } else {
                                                    $courierOrder = $this->orders->get_courier_base();
                                                    if ($courierOrder) {
                                                        $phoneCourier = $courierOrder->phone_courier;
                                                    } else {
                                                        echo 'Нет информации о курьере<br>';
                                                        $errorSMS = true;
                                                    }
                                                }
                                                $mask_sms_2_courier = $this->settings->mask_sms_2_courier;
                                                if ($mask_sms_2_courier) {
                                                    $mask_sms_2_courier = str_replace("%NUMBER%", $order->id, $mask_sms_2_courier);
                                                    $mask_sms_2_courier = str_replace("%PHONE%", $phoneCourier, $mask_sms_2_courier);
                                                    $mask_sms_2_courier = str_replace("%DATE%", $date_order, $mask_sms_2_courier);
                                                    $mask_sms_2_courier = str_replace("%TIME%", $date_time, $mask_sms_2_courier);
                                                } else {
                                                    echo 'Шаблон маски не задан<br>';
                                                    $errorSMS = true;
                                                }
                                                if (!$errorSMS) $this->orders->send_sms($mask_sms_2_courier, $phoneOrder);
                                            } elseif ($order->delivery_id == 2) { //самовывоз
                                                $date_order_create = $this->orders->get_order($order->id)->date;
                                                $date_order_create = new DateTime($date_order_create);
                                                $date_order_create->add(new DateInterval('P2D'));
                                                $date_order_create = $date_order_create->format('d.m.Y');
                                                $mask_sms_2_sklad = $this->settings->mask_sms_2_sklad;
                                                if ($mask_sms_2_sklad) {
                                                    $mask_sms_2_sklad = str_replace("%NUMBER%", $order->id, $mask_sms_2_sklad);
                                                    $mask_sms_2_sklad = str_replace("%DATE%", $date_order_create, $mask_sms_2_sklad);
                                                } else {
                                                    echo 'Шаблон маски не задан<br>';
                                                    $errorSMS = true;
                                                }
                                                if (!$errorSMS) $this->orders->send_sms($mask_sms_2_sklad, $phoneOrder);
                                            }
                                        }
                                    }
                                }
                                $this->orders->add_order_labels($id, $l_id);
                            }
                        }
                        break;
                    }
                    case(preg_match('/^unset_label_([0-9]+)/', $this->request->post('action'), $a) ? true : false): {
                        $l_id = intval($a[1]);
                        if ($l_id > 0) {
                            foreach ($ids as $id) {
                                $this->orders->delete_order_labels($id, $l_id);
                            }
                        }
                        break;
                    }
                }
            }
        }

        if (empty($keyword)) {
            $status = $this->request->get('status', 'integer');
            $filter['status'] = $status;
            $this->design->assign('status', $status);
        }

        if ($this->request->get('now_day') == 'Y' && ($filter['status']==1 || $filter['status']==0)) {
            $filter['date_delivery'] = date('d.m.Y');
	        $filter['date_total'] = date('Y-m-d');

            $filter['delivery_metod'] = 1;
            $this->design->assign('filter_day', true);
        }
        else{
            $this->design->assign('filter_day', false);
        }

        $orders_count = $this->orders->count_orders($filter);
        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $orders_count;
        }

        // Отображение
        $orders = array();
        foreach ($this->orders->get_orders($filter) as $o) {
            $orders[$o->id] = $o;

            /* Пометка заказов с акцией */
            $orders[$o->id]->marketing_offer = 0;

            $purchases = $this->orders->get_purchases(array('order_id' => $o->id));
            foreach ($purchases as $purchase){
                $product = $this->products->get_product((int)$purchase->product_id);
//                TODO ветпрапарат передаем параметр lecense
                $orders[$o->id]->lecense += $product->lecense;

                if ($orders[$o->id]->marketing_offer === 0){
                    if ($product->marketing_offer){
                        $orders[$o->id]->marketing_offer = 1;
                    }
                }
            }
        }

        // Метки заказов
        foreach ($this->orders->get_order_labels(array_keys($orders)) as $ol) {
            $orders[$ol->order_id]->labels[] = $ol;
        }

        $this->design->assign('pages_count', ceil($orders_count / $filter['limit']));
        $this->design->assign('current_page', $filter['page']);

        $this->design->assign('orders_count', $orders_count);

        $this->design->assign('orders', $orders);

        // Метки заказов
        $labels = $this->orders->get_labels();
        $this->design->assign('labels', $labels);

        return $this->design->fetch('orders.tpl');
    }
}
