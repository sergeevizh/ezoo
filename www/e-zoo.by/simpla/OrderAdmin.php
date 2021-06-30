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


class OrderAdmin extends Simpla
{

    public function fetch()
    {
        $order = new stdClass;
        if ($this->request->method('post')) {
            $order->id = $this->request->post('id', 'integer');
            $order->name = $this->request->post('name');
            $order->email = $this->request->post('email');
            $order->phone = $this->request->post('phone');
            $order->address = $this->request->post('address');
            $order->comment = $this->request->post('comment');
            $order->flat_num = $this->request->post('flat_num');
			$order->express = $this->request->post('express');
			$order->bonus_sale = $this->request->post('bonus_sale');
			$order->bonus_id = $this->request->post('bonus_id');
			
            $order->promo = $this->request->post('promo');
            $order->self_discharge_time = $this->request->post('self_discharge_time');
            $order->note = $this->request->post('note');
            $order->wh_note = $this->request->post('wh_note');
            //$order->discount = $this->request->post('discount', 'float');

            /* custom discount */
            $order->custom_discount = $this->request->post('custom_discount');
            /* /custom discount */

            $order->coupon_discount = $this->request->post('coupon_discount', 'float');
            $order->delivery_id = $this->request->post('delivery_id', 'integer');
            $order->delivery_price = $this->request->post('delivery_price', 'float');
            $order->payment_method_id = $this->request->post('payment_method_id', 'integer');
            $order->paid = $this->request->post('paid', 'integer');
            $order->user_id = $this->request->post('user_id', 'integer');
            $order->separate_delivery = $this->request->post('separate_delivery', 'integer');
            $order->courier_id = $this->request->post('courier_id', 'integer');

            $phoneOrder = $string = preg_replace('~\D+~','',$order->phone);

            if (!$order_labels = $this->request->post('order_labels')) {
                $order_labels = array();
            }

            if (empty($order->id)) {
                $order->id = $this->orders->add_order($order);
                $this->design->assign('message_success', 'added');

                $date_and_time = $order->self_discharge_time;

                $date_and_time = explode(' ', $date_and_time);
                unset($date_and_time[0]);
                $time = implode('', $date_and_time);
                $date = explode(' ', $order->self_discharge_time)[0];
                $city = $this->city->get_city($order->city_id);
                $result = $this->limits->get_limit_by_date($date, $time, $city);
                if ($result) {
                    $this->limits->update_limit(array('current' => $result->current+1), $result->id);
                }
            } else {
                $prev_date_and_time = $this->orders->get_order($order->id)->self_discharge_time;
                $this->orders->update_order($order->id, $order);
                if ($prev_date_and_time !== $order->self_discharge_time) {
                    $date_and_time = explode(' ', $prev_date_and_time);
                    unset($date_and_time[0]);
                    $time = implode('', $date_and_time);
                    $date = explode(' ', $prev_date_and_time)[0];
                    $result = $this->limits->get_limit_by_date($date, $time);

                    if ($result && $result->current > 0) {
                        $this->limits->update_limit(array('current' => $result->current-1), $result->id);
                    }
                }

                $this->design->assign('message_success', 'updated');
            }

            $city_id = $this->orders->get_order($order->id)->city_id;
//            if ($city_id==='6' || $city_id==='8' || $city_id==='10' || $city_id==='13') { //для минска, гомеля, бобруйска, гродно
                $errorSMS = false;

                $checkSobran = false;
                $checkSobranPost = false;
                $checkSobranOld = false;
                foreach ($order_labels as $label_val) {
                    if ($label_val == 7) {
                        $checkSobranPost = true;
                        $getlablOrderTemp = $this->orders->get_order_labels(array('order_id' => $order->id));
                        foreach ($getlablOrderTemp as $lablOrderTemp) {
                            if ($lablOrderTemp->id == 7) {
                                $checkSobranOld = true;
                            }
                        }
                    }
                }
                if ($checkSobranPost && !$checkSobranOld) $checkSobran = true;

                if ($checkSobran) {
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
                        if ($date_time != '') {
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
                        } else {
                            $mask_sms_2_courier_without_time = $this->settings->mask_sms_2_courier_without_time;
                            if ($mask_sms_2_courier_without_time) {
                                $mask_sms_2_courier_without_time = str_replace("%NUMBER%", $order->id, $mask_sms_2_courier_without_time);
                                $mask_sms_2_courier_without_time = str_replace("%PHONE%", $phoneCourier, $mask_sms_2_courier_without_time);
                                $mask_sms_2_courier_without_time = str_replace("%DATE%", $date_order, $mask_sms_2_courier_without_time);
                            } else {
                                echo 'Шаблон маски не задан<br>';
                                $errorSMS = true;
                            }
                            if (!$errorSMS) $this->orders->send_sms($mask_sms_2_courier_without_time, $phoneOrder);
                        }
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
//            }

/*            if ($city_id==='8') { //для бобруйска
                $errorSMS = false;

                $checkSobranB = false;
                $checkSobranPostB = false;
                $checkSobranOldB = false;
                foreach ($order_labels as $label_val) {
                    if ($label_val == '20') {
                        $checkSobranPostB = true;
                        $getlablOrderTemp = $this->orders->get_order_labels(array('order_id' => $order->id));
                        foreach ($getlablOrderTemp as $lablOrderTemp) {
                            if ($lablOrderTemp->id == 0) {
                                $checkSobranOldB = true;
                            }
                        }
                    }
                }
                if ($checkSobranPostB && !$checkSobranOldB) $checkSobranB = true;

                if ($checkSobranB) {
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

            if ($city_id==='10') { //для гомеля
                $errorSMS = false;

                $checkSobranG = false;
                $checkSobranPostG = false;
                $checkSobranOldG = false;
                foreach ($order_labels as $label_val) {
                    if ($label_val == '22') {
                        $checkSobranPostG = true;
                        $getlablOrderTemp = $this->orders->get_order_labels(array('order_id' => $order->id));
                        foreach ($getlablOrderTemp as $lablOrderTemp) {
                            if ($lablOrderTemp->id == 0) {
                                $checkSobranOldG = true;
                            }
                        }
                    }
                }
                if ($checkSobranPostG && !$checkSobranOldG) $checkSobranG = true;

                if ($checkSobranG) {
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
            }*/
            $this->orders->update_order_labels($order->id, $order_labels);

            if ($order->id) {
                // Покупки
                $purchases = array();
                if ($this->request->post('purchases')) {
                    foreach ($this->request->post('purchases') as $n => $va) {
                        foreach ($va as $i => $v) {
                            if (empty($purchases[$i])) {
                                $purchases[$i] = new stdClass;
                            }
                            $purchases[$i]->$n = $v;
                        }
                    }
                }
                $posted_purchases_ids = array();
                foreach ($purchases as $purchase) {
                    $variant = $this->variants->get_variant($purchase->variant_id);

                    if (!empty($purchase->id)) {
                        if (!empty($variant)) {
                            $this->orders->update_purchase($purchase->id,
                                array('variant_id' => $purchase->variant_id, 'variant_name' => $variant->name, 'sku' => $variant->sku, 'price' => $purchase->price, 'amount' => $purchase->amount));
                        } else {
                            $this->orders->update_purchase($purchase->id, array('price' => $purchase->price, 'amount' => $purchase->amount));
                        }
                    } elseif (!$purchase->id = $this->orders->add_purchase(array(
                        'order_id' => $order->id,
                        'variant_id' => $purchase->variant_id,
                        'variant_name' => $variant->name,
                        'price' => $purchase->price,
                        'amount' => $purchase->amount
                    ))
                    ) {
                        $this->design->assign('message_error', 'error_closing');
                    }

                    $posted_purchases_ids[] = $purchase->id;
                }

                // Удалить непереданные товары
                foreach ($this->orders->get_purchases(array('order_id' => $order->id)) as $p) {
                    if (!in_array($p->id, $posted_purchases_ids)) {
                        $this->orders->delete_purchase($p->id);
                    }
                }

                // Принять?
                if ($this->request->post('status_new')) {
                    $new_status = 0;
                } elseif ($this->request->post('status_accept')) {
                    $new_status = 1;
                } elseif ($this->request->post('status_done')) {
                    $new_status = 2;
                } elseif ($this->request->post('status_deleted')) {
                    $new_status = 3;
                } else {
                    $new_status = $this->request->post('status', 'string');
                }

                if ($new_status == 0) {
                    if (!$this->orders->open(intval($order->id))) {
                        $this->design->assign('message_error', 'error_open');
                    } else {
                        $this->orders->update_order($order->id, array('status' => 0));
                    }
                } elseif ($new_status == 1) {
                    if (!$this->orders->close(intval($order->id))) {
                        $this->design->assign('message_error', 'error_closing');
                    } else {
//                        if ($city_id==='6'|| $city_id==='8' || $city_id==='10' || $city_id==='13') {  //для минска, гомеля, бобруйска, гродно
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
//                            }
                        }
                        $this->orders->update_order($order->id, array('status' => 1));
                    }
                }
                elseif ($new_status == 2) {
                    if (!$this->orders->close(intval($order->id))) {
                        $this->design->assign('message_error', 'error_closing');
                    } else {
                        $this->orders->update_order($order->id, array('status' => 2));
                        $date_and_time = explode(' ', $order->self_discharge_time);
                        unset($date_and_time[0]);
                        $time = implode('', $date_and_time);
                        if (($this->orders->get_order($order->id)->total_price >= $this->settings->promo_price && in_array($time, $this->settings->promo_time)) &&
                            ($this->orders->get_order($order->id)->total_price >= $this->settings->promo_price_second && in_array($time, $this->settings->promo_time_second))) {
                            $errorSMS = false;
                            $mask = $this->settings->mask_sms_promo;
                            $mask_second = $this->settings->mask_sms_promo_second;
                            if ($mask && $mask_second) {
                                $promo = $this->promo->get_first_code();
                                $promo_second = $this->promo_second->get_first_code();
                                if ($promo && $promo_second) {
                                    $sms = str_replace("%PROMO%", $promo->code, $mask);
                                    $sms_second = str_replace("%PROMO%", $promo_second->code, $mask_second);
                                    $this->promo->delete_code($promo->id);
                                    $this->promo_second->delete_code($promo_second->id);
                                }else if ($promo) {
                                    $sms = str_replace("%PROMO%", $promo->code, $mask);
                                    $this->promo->delete_code($promo->id);
                                }else if ($promo_second) {
                                    $sms_second = str_replace("%PROMO%", $promo_second->code, $mask_second);
                                    $this->promo_second->delete_code($promo_second->id);
                                }
                                else {
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
                        } else if ($this->orders->get_order($order->id)->total_price >= $this->settings->promo_price && in_array($time, $this->settings->promo_time)) {
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
                        } else if ($this->orders->get_order($order->id)->total_price >= $this->settings->promo_price_second && in_array($time, $this->settings->promo_time_second)) {
                            $errorSMS = false;
                            $mask_second = $this->settings->mask_sms_promo_second;
                            if ($mask_second) {
                                $promo_second = $this->promo_second->get_first_code();
                                if ($promo_second) {
                                    $sms = str_replace("%PROMO%", $promo_second->code, $mask_second);
                                    $this->promo_second->delete_code($promo_second->id);
                                } else {
                                    echo 'Промокоды закончились<br>';
                                    $errorSMS = true;
                                }
                            } else {
                                echo 'Шаблон маски не задан<br>';
                                $errorSMS = true;
                            }
                            if (!$errorSMS) $this->orders->send_sms($sms, $phoneOrder);
                        }
                    }
                } elseif ($new_status == 3) {
                    if (!$this->orders->open(intval($order->id))) {
                        $this->design->assign('message_error', 'error_open');
                    } else {
                        $this->orders->update_order($order->id, array('status' => 3));
                    }
                    header('Location: ' . $this->request->get('return'));
                }
                $order = $this->orders->get_order($order->id);

                // Отправляем письмо пользователю
                if ($this->request->post('notify_user')){
                    $this->notify->email_order_user($order->id);
                }

                /* custom discount */
                $total = 0;
                $total_not_sale = 0;
                $variants_ids = array();
                $delivery = $this->delivery->get_delivery($order->delivery_id);

                $TotalWithOutSale = 0;
                $TotalWithOutSaleBrands = 0;
                $TotalSaleBrands = 0;
                $TotalWithOutSaleBrandsDate = Array();
                $TotalSaleBrandsDate = Array();
                $TotalWithOutSaleBrandsDateIsk = Array();

                foreach ($purchases as $purchase) {

                    $total += $purchase->price * $purchase->amount;

                    $variant = $this->variants->get_variant((int)$purchase->variant_id);

                    if ($variant->compare_price < $variant->price) {
                        $total_not_sale += $purchase->price * $purchase->amount;
                    }

                    array_push($variants_ids, $purchase->variant_id);


                }


                foreach ($purchases as $purchase) {
                    $v = $this->variants->get_variant((int)$purchase->variant_id);
                    $p = $this->products->get_product((int)$v->product_id);


                    $ProductOut = 0;
                    $check_city = 0;
                    $product = new stdClass();

                    $product->id = $v->product_id;
                    $product->brand_id = $p->brand_id;
                    $product->variant = $v;
                    $product->amount = $purchase->amount;
                    $product->sale_double_item = $p->sale_double_item;
                    $product->sale_double_item_value = $p->sale_double_item_value;
                    $product->sale_double_item_sam = $p->sale_double_item_sam;
                    $product->sale_double_item_sam_value = $p->sale_double_item_sam_value;


                    //Проверка на исключение товара из скидки
                    $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='product' AND value=?", $delivery->id, $product->id);
                    if ($this->db->result()) {
                        if ($ProductOut == 0) {
                            $TotalWithOutSale += $product->variant->price * $product->amount;
                            $ProductOut = 1;
                            continue;
                        }

                    }


                    //Проверка на исключение категорий
                    $this->db->query("SELECT * FROM __products_categories WHERE product_id=?", $product->id);
                    $Categorys = $this->db->results();
                    foreach ($Categorys as $Category) {
                        $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='category' AND value=?", $delivery->id, $Category->category_id);
                        if ($this->db->result()) {
                            if ($ProductOut == 0) {
                                $TotalWithOutSale += $product->variant->price * $product->amount;
                                $ProductOut = 1;
                                continue;
                            }
                        }
                    }


                    //Проверка на исключение бренда из скидки
                    $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='brand' AND value=?", $delivery->id, $product->brand_id);
                    if ($this->db->result()) {
                        if ($ProductOut == 0) {
                            $TotalWithOutSale += $product->variant->price * $product->amount;
                            $ProductOut = 1;
                            continue;
                        }
                    }


                    /*                //Скидка по сумме доставки
                                    $delivery->discount_percent = 0;
                                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
                                    foreach ($this->db->results() as $row) {
                                        if ($total >= $row->discount_from && $delivery->discount_percent < $row->discount_percent) {
                                            $delivery->discount_percent = intval($row->discount_percent);

                                        }
                                        $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                                        $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $product_percent) / 100), 2, ".", ".");
                                        $ProductOut = 1;
                                        continue;
                                    }*/

                    //Проверка скидки на второй товар
                    if ($ProductOut == 0) {
                        if (!$product->variant->compare_price) {
                            if ($product->sale_double_item && $product->sale_double_item_value != 0 && $delivery->id == 1) {
                                $product_percent = $product->sale_double_item_value;
                                $countProductSale = ($product->amount - $product->amount % 2) / 2;
                                $countProductNotSale = $product->amount - $countProductSale;
                                $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                                $TotalSaleBrands += number_format(($product->variant->price * $countProductSale) * ((100 - $product_percent) / 100), 2, ".", ".");
                                $TotalSaleBrands += $countProductNotSale * $product->variant->price;
                                $ProductOut = 1;
                                continue;
                            }
                            if ($product->sale_double_item_sam && $product->sale_double_item_sam_value != 0 && $delivery->id == 2) {
                                $product_percent = $product->sale_double_item_sam_value;
                                $countProductSale = ($product->amount - $product->amount % 2) / 2;
                                $countProductNotSale = $product->amount - $countProductSale;
                                $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                                $TotalSaleBrands += number_format(($product->variant->price * $countProductSale) * ((100 - $product_percent) / 100), 2, ".", ".");
                                $TotalSaleBrands += $countProductNotSale * $product->variant->price;
                                $ProductOut = 1;
                                continue;
                            }
                        }
                    }


                    //Проверка на индивидуальную скидку по товару
                    if ($ProductOut == 0) {
                        if (!$product->variant->compare_price) {
                            $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=? AND product_id=? ORDER by discount_percent DESC", $delivery->id, $product->id);
                            $result_product = $this->db->results();
                            if ($result_product) {
                                $product_percent = 0;
                                foreach ($result_product as $product_row) {
                                    if ($product_row->discount_from <= $total) {
                                        $product_percent = $product_row->discount_percent;
                                        break;
                                    }
                                }
                                $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                                $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $product_percent) / 100), 2, ".", ".");
                                $ProductOut = 1;
                                continue;
                            }
                        }
                    }


                    //Проверка на индивидуальную скидку по бренду на дату
                    if ($ProductOut == 0) {
                        if (!$product->variant->compare_price) {
                            $this->db->query("SELECT * FROM __delivery_date_brand WHERE delivery_id=? AND brands_id=? ORDER by discount_percent DESC", $delivery->id, $product->brand_id);
                            $result_brand = $this->db->results();
                            if ($result_brand) {
                                $brand_percent = Array();
                                foreach ($result_brand as $brand_row) {
                                    if ($brand_row->discount_from <= $total) {
                                        if (empty($brand_percent[$brand_row->date_sale])) {
                                            $brand_percent[$brand_row->date_sale] = $brand_row->discount_percent;
                                        }
                                    }
                                }
                                $TotalSaleBrandsTemp = 0;
                                if ($ProductOut == 0) {
                                    if (!$product->variant->compare_price) {
                                        $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=? AND brands_id=? ORDER by discount_percent DESC", $delivery->id, $product->brand_id);
                                        $result_brand_temp = $this->db->results();
                                        if ($result_brand_temp) {
                                            $brand_percent_temp = 0;
                                            foreach ($result_brand_temp as $brand_row_temp) {
                                                if ($brand_row_temp->discount_from <= $total) {
                                                    $brand_percent_temp = $brand_row_temp->discount_percent;
                                                    break;
                                                }
                                            }
                                            $TotalSaleBrandsTemp = number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent_temp) / 100), 2, ".", ".");
                                        }
                                    }
                                }

                                foreach ($brand_percent as $key => $brand_percent_item) {
                                    $TotalWithOutSaleBrandsDateIsk[$key] += $TotalSaleBrandsTemp;
                                    $TotalWithOutSaleBrandsDate[$key] += ($TotalSaleBrandsTemp == 0) ? $product->variant->price * $product->amount : 0;
                                    $TotalSaleBrandsDate[$key] += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent_item) / 100), 2, ".", ".");
                                }
                            }
                        }
                    }


                    //Проверка на индивидуальную скидку по бренду
                    if ($ProductOut == 0) {
                        if (!$product->variant->compare_price) {
                            $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=? AND brands_id=? ORDER by discount_percent DESC", $delivery->id, $product->brand_id);
                            $result_brand = $this->db->results();

                            if ($result_brand) {
                                $brand_percent = 0;
                                foreach ($result_brand as $brand_row) {
                                    if ($brand_row->discount_from <= $total) {
                                        $brand_percent = $brand_row->discount_percent;
                                        break;
                                    }
                                }
                                $TotalWithOutSaleBrands += $product->variant->price * $product->amount;

                                //DENIS R
                                //$TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent) / 100), 2, ".", ".");
                                $TotalSaleBrands += number_format((round($product->variant->price * ((100 - $brand_percent) / 100), 2) * $product->amount), 2, ".", "");


                                $ProductOut = 1;
                                continue;
                            }
                        }
                    }

                    //Проверка скидки по городу
                    $this->db->query("SELECT * FROM __delivery_city WHERE delivery_id=? AND city_id=?", $delivery->id, $city_id);
                    $delivery_cities = $this->db->result();
                    if ($delivery_cities) {

                        if (!$product->variant->compare_price) {
                            $product_percent = 0;

                            if ($delivery_cities->from_sum <= $total) {
                                $product_percent = $delivery_cities->discount_percent;
                                $TotalWithOutSale += $product->variant->price * $product->amount;
                            }
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $product_percent) / 100), 2, ".", ".");

                            if ($delivery_cities->check_sale_other == 0) {
                                $ProductOut = 1;
                            }
                            continue;
                        }
                    }

                    //Скидка на товар с акцией
                    if ($product->variant->compare_price){
                        $TotalSaleBrands+=number_format($product->variant->price*$product->amount, 2,".", ".");
                    }
                }


                if ($this->request->post('custom_discount')){

                    if ($this->request->post('custom_discount_val')){
                        $order->coupon_discount = $this->request->post('custom_discount_val', 'float');
                        $order->total_price = $total - $this->request->post('custom_discount_val', 'float');
                    }

                } elseif ($TotalSaleBrands && $total){
                    $order->coupon_discount = $total - $TotalSaleBrands;
                    $order->total_price =$TotalSaleBrands;
                }

                $this->orders->update_order($order->id, $order);


            }
        } else {
            $order->id = $this->request->get('id', 'integer');
            $order = $this->orders->get_order(intval($order->id));
            // Метки заказа
            $order_labels = array();
            if (isset($order->id)) {
                foreach ($this->orders->get_order_labels($order->id) as $ol) {
                    $order_labels[] = $ol->id;
                }
            }
        }


        $subtotal = 0;
        $purchases_count = 0;
        if ($order && $purchases = $this->orders->get_purchases(array('order_id' => $order->id))) {
            // Покупки
            $products_ids = array();
            $variants_ids = array();
            foreach ($purchases as $purchase) {
                $products_ids[] = $purchase->product_id;
                $variants_ids[] = $purchase->variant_id;
            }

            $products = array();
            foreach ($this->products->get_products(array('id' => $products_ids, 'limit' => count($products_ids))) as $p) {
                $products[$p->id] = $p;
            }

            $images = $this->products->get_images(array('product_id' => $products_ids));
            foreach ($images as $image) {
                $products[$image->product_id]->images[] = $image;
            }

            $variants = array();
            foreach ($this->variants->get_variants(array('product_id' => $products_ids)) as $v) {
                $variants[$v->id] = $v;
            }

            foreach ($variants as $variant) {
                if (!empty($products[$variant->product_id])) {
                    $products[$variant->product_id]->variants[] = $variant;
                }
            }


            foreach ($purchases as &$purchase) {
                if (!empty($products[$purchase->product_id])) {
                    $purchase->product = $products[$purchase->product_id];
                }
                if (!empty($variants[$purchase->variant_id])) {
                    $purchase->variant = $variants[$purchase->variant_id];
                }
                $subtotal += $purchase->price * $purchase->amount;
                $purchases_count += $purchase->amount;
            }
        } else {
            $purchases = array();
        }

        // Если новый заказ и передали get параметры
        if (empty($order->id)) {
            $order = new stdClass;
            if (empty($order->phone)) {
                $order->phone = $this->request->get('phone', 'string');
            }
            if (empty($order->name)) {
                $order->name = $this->request->get('name', 'string');
            }
            if (empty($order->address)) {
                $order->address = $this->request->get('address', 'string');
            }

			
            if (empty($order->email)) {
                $order->email = $this->request->get('email', 'string');
            }
        }
        $this->design->assign('purchases', $purchases);
        $this->design->assign('purchases_count', $purchases_count);
        $this->design->assign('subtotal', $subtotal);
        $this->design->assign('order', $order);

        if (!empty($order->id)) {
            // Способ доставки
            $delivery = $this->delivery->get_delivery($order->delivery_id);
            $this->design->assign('delivery', $delivery);
            $courier_id = $this->orders->get_order_courier($order->id);
            $this->design->assign('courier_id', $courier_id->order_courier_id);


            // Способ оплаты
            $payment_method = $this->payment->get_payment_method($order->payment_method_id);

            if (!empty($payment_method)) {
                $this->design->assign('payment_method', $payment_method);

                // Валюта оплаты
                $payment_currency = $this->money->get_currency(intval($payment_method->currency_id));
                $this->design->assign('payment_currency', $payment_currency);
            }
            // Пользователь
            if ($order->user_id) {
                $this->design->assign('user', $this->users->get_user(intval($order->user_id)));
            }

            // Соседние заказы
            $this->design->assign('next_order', $this->orders->get_next_order($order->id, $this->request->get('status', 'string')));
            $this->design->assign('prev_order', $this->orders->get_prev_order($order->id, $this->request->get('status', 'string')));
        }

        // Все способы доставки
        $deliveries = $this->delivery->get_deliveries();
        $this->design->assign('deliveries', $deliveries);

        // Все курьеры
        $couriers = $this->orders->get_couriers();
        $this->design->assign('couriers', $couriers);

        // Все способы оплаты
        $payment_methods = $this->payment->get_payment_methods();
        $this->design->assign('payment_methods', $payment_methods);

        // Метки заказов
        $labels = $this->orders->get_labels();
        $this->design->assign('labels', $labels);

        $this->design->assign('order_labels', $order_labels);

        if ($this->request->get('view') == 'print') {
            return $this->design->fetch('order_print.tpl');
        } elseif ($this->request->get('view') == 'export'){
            $data = array();
            array_push($data, ['Заказ №' . $order->id]);
            array_push($data, ['от ' . date("d.m.y", strtotime($order->date))]);
            array_push($data, array( ' ' ));

            $customer_info = [
                ['Получатель'],
                [$order->name],
                [$order->phone],
                [$order->email],
                [$order->address],
                [$delivery->name],
                [$order->self_discharge_time],
                [$order->comment]
            ];

            foreach ($customer_info as $item) {

                if ($item[0] != ''){
                    array_push($data, $item);
                }

            }

            if ($order_labels) {
                array_push($data, ['Метки :']);
                $data_labels = [];

                foreach ($labels as $label) {
                    if (in_array($label->id, $order_labels)) {
                        array_push($data_labels, $label->name);
                    }
                }

                array_push($data, $data_labels);
            }

            if ($couriers) {
                foreach ($couriers as $courier) {
                    if ($courier->id==$courier_id) {
                        array_push($data, ['Курьер: ' . $courier->name . ' (' . $courier->phone_courier . ')']);
                    }
                }
            }

            array_push($data, array( ' ' ));
            array_push($data, ['Товар', 'Вариант', 'Артикул', 'Цена', 'Количество', 'Всего']);

            foreach ($purchases as $purchase){
                array_push($data,[$purchase->product_name, $purchase->variant_name , $purchase->sku, $purchase->price . ' BYN', $purchase->amount . ' шт.', $purchase->price*$purchase->amount . ' BYN']);
            }

            array_push($data, array( ' ' ));

            if ($order->discount > 0){
                array_push($data, ['Скидка: ', $order->discount . '%']);
            }

            if ($order->coupon_discount>0){
                if ($order->coupon_code){
                    array_push($data, ['Скидка: ' . $order->coupon_code, $order->coupon_discount]);
                } else {
                    array_push($data, ['Скидка: ', $order->coupon_discount . ' BYN']);
                }

            }

            array_push($data, ['Итого: ', $order->total_price . ' BYN']);

            if ($payment_method){
                array_push($data, ['Способ оплаты: ', $payment_method->name]);
            }

            array_push($data, ['К оплате: ', $order->total_price . ' BYN']);

            array_push($data, array( ' ' ));

//            if ($order->paid) {
//                array_push($data, ['Заказ оплачен']);
//            } else {
//                array_push($data, ['Заказ не оплачен']);
//            }

            $xlsx = $this->xlsx->fromArray($data);
//            $xlsx->downloadAs('order_№' . $order->id . '.xls');
            $xlsx->saveAs('files/orders/order_№' . $order->id . '.xls');
            $this->design->assign('success_export', 'files/orders/order_№' . $order->id . '.xls');
            return $this->design->fetch('order.tpl');

        } else {
            return $this->design->fetch('order.tpl');
        }
    }
}
