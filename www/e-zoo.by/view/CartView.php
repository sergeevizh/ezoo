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

class CartView extends View
{
    public function __construct()
    {
        parent::__construct();
		
		
	
		
        // Если передан id варианта, добавим его в корзину
        if ($variant_id = $this->request->get('variant', 'integer')) {
            $this->cart->add_item($variant_id, $this->request->get('amount', 'integer'));
            header('location: ' . $this->config->root_url . '/cart/');
        }

        // Удаление товара из корзины
        if ($delete_variant_id = intval($this->request->get('delete_variant'))) {
            $this->cart->delete_item($delete_variant_id);
            if (!isset($_POST['submit_order']) || $_POST['submit_order'] != 1) {
                header('location: ' . $this->config->root_url . '/cart/');
            }
        }

        // Если нажали на чекбокс возле товара
        if (isset($_POST['check']) && isset($_COOKIE['user_id'])) {
            $variant_id = intval($this->request->post('variant_id'));
            $check = 0;
            $user_id = $_COOKIE['user_id'];

            if ($this->request->post('checkbox')) {
                $check = intval($this->request->post('checkbox'));
            }

            $query = $this->db->placehold("UPDATE __cart c SET c.checked=? WHERE c.variant_id=? AND c.user_id=?", $check, $variant_id, $user_id);
            $this->db->query($query);
            header('location: ' . $this->config->root_url . '/cart/');
        }

        // Если добавили товар из истории
        if (isset($_POST['history'])){
            $variant_id = intval($this->request->post('variant'));
            $this->cart->add_item($variant_id);

            if (isset($_COOKIE['user_history'][$variant_id])){
                setcookie('user_history[' . $variant_id . ']', 1, time() - 1, "/");
            }

            header('location: ' . $this->config->root_url . '/cart/');
        }

        // Если нажали оформить заказ
        if (isset($_POST['checkout'])) {	
		
	        if (!empty($this->request->post('press_check'))) {
                return $this->design->fetch('cart.tpl');
            }
            $order = new stdClass;
            $order->delivery_id = $this->request->post('delivery_id', 'integer');
            $order->payment_method_id = $this->request->post('payment_method_id', 'integer');
            if (empty($this->request->post('payment_method_id'))) {
                $order->payment_method_id = 13;
            }
            //$order->name = $this->request->post('name');  отключаем имя для заказа
            //$order->email = $this->request->post('email');  отключаем почту для заказа
            $order->self_discharge_time = trim($this->request->post('self_discharge_time'));
			
            $time = trim($this->request->post('time'));
            $order->phone = $this->request->post('phone');
            $order->comment = $this->request->post('comment');
            $order->ip = $_SERVER['REMOTE_ADDR'];
//            $order->address = $this->request->post('address');
            $order->address = $this->request->post('yamap_input');
            $order->flat_num = $this->request->post('flat_num');
            $order->express = $this->request->post('express');
			if($order->express == 1){
				$time='';
			}

            $order->promo = $this->request->post('promo');
            $order->courier_id = (int)$this->request->post('suggestion_courier');

            if ($this->request->post('call_back')) {
                $order->call_back = $this->request->post('call_back');
            }

            /*regions*/
            if ($_SESSION['region_id']) {
                $order->region_id = intval($_SESSION['region_id']);
            }
            /*/regions*/

            //Запись в файл тех, кто согласился на рассылку
            if ($this->request->post('mailing')) {


                $cur_date = date("d.m.Y");
                $csv_data = array($order->phone, $order->ip, $cur_date);

                $fp = fopen('mailing.csv', 'a');


                if (!isset($_COOKIE['Allow_mailing'])) {
                    setcookie("Allow_mailing", $cur_date, time() + 2592000);
                    fputcsv($fp, $csv_data, ';');


                }
                fclose($fp);

            }


            if (!empty($this->request->post('city')) || $this->request->post('city') == "0") {
                $order->city_id = $this->request->post('city');
                $cityTempId = $this->request->post('city');
                $cityTemp = $this->request->post('city');
                if ($cityTemp == "0") {
                    $cityTemp = 'Город: Минск ';
                } else {
                    $cityTemp = 'Город: ' . $this->city->get_city($cityTemp)->name . ' ';
                }
                $cityareaTemp = $this->request->post('city_area');
                if (!empty($this->request->post('city_area'))) {
                    $this->db->query("SELECT * FROM __shipping_area WHERE id=?", $cityareaTemp);
                    $city_area_name = $this->db->result();
                    if ($city_area_name) {
                        $cityareaTemp = 'Пункт самовывоза: ' . $city_area_name->name_area . ' ';
                    }
                }
                if ($order->delivery_id == 1) {
                    $cityareaTemp = '';
                }
                if ($cityTemp) {
                    $temAddress = $cityTemp . $cityareaTemp . $order->address;
                    $order->address = $temAddress;
                }
            } else {
                $order->city_id = 0;
                if ($order->delivery_id == 1) {
                    $temAddress = 'Город: Минск ' . $order->address;
                } else {
                    $temAddress = 'Город: Минск - Пункт самовывоза: Сеница 68/8 МКАД';
                }
                $order->address = $temAddress;
            }
            $CustomDiscount = 0;
            $DayNumber = date('N', strtotime($order->self_discharge_time));
            if ($order->delivery_id == 1) {
                if ($DayNumber >= 1 && $DayNumber <= 4 && ($time == "10:00 - 12:00" || $time == '12:00 - 15:00')) {
                    $CustomDiscount = 10;
                } elseif ($DayNumber == 5 || $DayNumber == 6 || $DayNumber == 7) {
                    $CustomDiscount = 20;
                }
            }


            $this->design->assign('payment_method_id', $order->payment_method_id);
            $this->design->assign('delivery_id', $order->delivery_id);
            $this->design->assign('time', $time);
            //$this->design->assign('name', $order->name);  отключаем имя для заказа
            //$this->design->assign('email', $order->email);   отключаем почту для заказа
            $this->design->assign('phone', $order->phone);
            $this->design->assign('address', $order->address);
            $this->design->assign('flat_num', $order->flat_num);
            $this->design->assign('express', $order->express);
            $this->design->assign('self_discharge_time', $order->self_discharge_time);

            //бренд
            //$this->design->assign('brands', $brands);


            //            Скидка
            $cart = $this->cart->get_cart();
			
            if (!empty($this->request->post('city'))) {
                if ($cityTempId && $cityTempId != '0') {
                    $this->db->query("SELECT * FROM __delivery_city WHERE delivery_id=? AND city_id=?", $order->delivery_id, $cityTempId);
                    $cities_deliver_sum = $this->db->result();
                    if ($cities_deliver_sum) {
                        // TODO если стоимости со скидкой < сумма товара (что бы пропускало 1 ветпрепарат с ценой = 0,00
                        if ($cities_deliver_sum->from_sum > $cart->total_without_discount) {
                            return $this->design->fetch('cart.tpl');
                        }
                    }
                }
            }
			
		
			
            //$brand_id = $this->brands->get_brand(intval($product->brand_id));
            //$this->design->assign('brand', $this->brands->get_brand(intval($product->brand_id)));

            if (!empty($order->delivery_id)) {
                $delivery = $this->delivery->get_delivery($order->delivery_id);


                if (!empty($delivery)) {
                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=? AND discount_from >= 0 AND discount_percent > 0", $delivery->id);
                    $delivery_discounts = $this->db->results();
                    //по дате доставки
                    $this->db->query("SELECT * FROM __delivery_date WHERE delivery_id=?  AND  discount_from<" . $cart->total_without_discount . " ORDER BY discount_from DESC LIMIT 1", $delivery->id);
                    $deliveries_date = $this->db->results();
                    $new_date = $order->self_discharge_time;
                    $date_sale_check = false;
                    foreach ($deliveries_date as $delivery_date) {
                        $price_day = 0;
                        if (strtotime($new_date) == strtotime($delivery_date->date_sale)) {
                            $TWOSD = empty($_SESSION['delivery_brand_data_minus'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_minus'][$delivery->id][$delivery_date->date_sale];
                            $TWOSDI = empty($_SESSION['delivery_brand_data_minus_isk'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_minus_isk'][$delivery->id][$delivery_date->date_sale];
                            $TSBD = empty($_SESSION['delivery_brand_data_plus'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_plus'][$delivery->id][$delivery_date->date_sale];
                            $discount_day = ($cart->total_without_discount_not_sale - $_SESSION['delivery_minus'][$delivery->id] - $_SESSION['delivery_brand_minus'][$delivery->id] - $TWOSD) * (1 - (100 - $delivery_date->discount_percent) / 100);
                            $order->coupon_discount = round(($cart->total_without_discount - $cart->total_price) + $discount_day + $_SESSION['delivery_brand_minus'][$delivery->id] + $TWOSD + $TWOSDI - $_SESSION['delivery_brand_plus'][$delivery->id] - $TSBD, 2);
                            $date_sale_check = true;
                            break;
                        }
                    }
                    if (!empty($delivery_discounts) && !$date_sale_check) {
                        $check_not_sale_city = false;
                        $check_city_cart = false;
                        if (!empty($this->request->post('city'))) {
                            /*----price city deliver-----*/
                            if ($cityTempId && $cityTempId != '0') {
                                $this->db->query("SELECT * FROM __delivery_city WHERE delivery_id=? AND city_id=?", $order->delivery_id, $cityTempId);
                                $cities_deliver = $this->db->result();
                                if (!empty($cities_deliver)) {
                                    $CustomDiscount = 0;
                                    if ($cities_deliver->discount_percent) {
                                        $CustomDiscount = intval($cities_deliver->discount_percent);
                                        $check_city_cart = true;
                                        if ($cities_deliver->check_sale_other) {
                                            $check_not_sale_city = true;
                                        }
                                    }
                                }
                            }
                            /*----price city deliver-----*/
                        }

                        $selected_discount_percent = 0;
                        $order->coupon_discount = round(($cart->total_without_discount - $cart->total_price) + $_SESSION['delivery_brand_minus'][$delivery->id] - $_SESSION['delivery_brand_plus'][$delivery->id], 2);
                        foreach ($delivery_discounts as $row) {
                            if ($cart->total_price >= $row->discount_from && $row->discount_percent > $selected_discount_percent) {
                                $selected_discount_percent = $row->discount_percent;
                                if ($CustomDiscount == 0 || ($row->discount_percent > $CustomDiscount && !$check_city_cart)) {
                                    $discount_price = ($cart->total_without_discount_not_sale - $_SESSION['delivery_minus'][$delivery->id] - $_SESSION['delivery_brand_minus'][$delivery->id]) * (1 - (100 - $row->discount_percent) / 100);
                                } else {
                                    if ($check_not_sale_city) {
                                        $discount_price = ($cart->total_without_discount_not_sale - $_SESSION['delivery_minus'][$delivery->id]) * (1 - (100 - $CustomDiscount) / 100);
                                    } else {
                                        $discount_price = ($cart->total_without_discount_not_sale - $_SESSION['delivery_minus'][$delivery->id] - $_SESSION['delivery_brand_minus'][$delivery->id]) * (1 - (100 - $CustomDiscount) / 100);
                                    }
                                }

                                if ($check_not_sale_city) {
                                    $order->coupon_discount = round(($cart->total_without_discount - $cart->total_price) + $discount_price, 2);
                                } else {
                                    $order->coupon_discount = round(($cart->total_without_discount - $cart->total_price) + $discount_price + $_SESSION['delivery_brand_minus'][$delivery->id] - $_SESSION['delivery_brand_plus'][$delivery->id], 2);
                                }
                            }
                        }
                    }
                    if (empty($delivery_discounts) && $date_sale_check) {
                        $delivery->total_price = $cart->total_without_discount - $_SESSION['delivery_brand_minus'][$delivery->id] + $_SESSION['delivery_brand_plus'][$delivery->id];
                    }
                }
            }
			


// Скидки END


            /*            $deliveries = $this->delivery->get_deliveries(array('enabled' => 1));
                        foreach ($deliveries as $delivery) {

                            $delivery->payments = $this->delivery->get_delivery_payments($delivery->id);
                            $delivery->discount_for_order = $cart->total_without_discount-$cart->total_price;
                            $delivery->discount_percent = 0;
                            $delivery->total_price = $cart->total_price;
                            $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
                            $delivery_discounts = $this->db->results();
                            $selected_discount_percent = 0;
                            foreach ($delivery_discounts as $row) {
                                if ($cart->total_price >= $row->discount_from && $row->discount_percent > $selected_discount_percent) {
                                    $selected_discount_percent = $row->discount_percent;

                                    $discount_price = $cart->total_without_discount_not_sale * (1-(100 - $row->discount_percent) / 100);
                                    $discount_for_order = ($cart->total_without_discount-$cart->total_price)+$discount_price;
                                    $delivery->total_price = $cart->total_without_discount- $discount_for_order;

                                    if($_SERVER['REMOTE_ADDR'] == '178.151.13.246') {

                                        exit;
                                    }
                                }
                            }
                        }*/


            $order->discount = $cart->discount;
            //$order->coupon_discount = $delivery_discount_for_order;
            if ($cart->coupon) {
                $order->coupon_discount += $cart->coupon_discount;
                $order->coupon_code = $cart->coupon->code;
            }


            if (!empty($this->user->id)) {
                $order->user_id = $this->user->id;
                $order->email = $this->user->email;
                $order->name = $this->user->name;
            }/*else{  //отключаем почту для заказа
				$UserByEmail = $this->users->get_users(array('keyword'=>$order->email));
				if(!empty($UserByEmail)&&!empty($UserByEmail[0]))
				{
					$order->user_id = $UserByEmail[0]->id;
				}
			}*/

            //         if (empty($order->name)) {
            //             $this->design->assign('error', 'empty_name');
            //           } elseif (empty($order->email)) {   отключаем почту для заказа
            //               $this->design->assign('error', 'empty_email');
//            } elseif ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code)) {
//                $this->design->assign('error', 'captcha');
            //         } else {
            $order->self_discharge_time .= ' ' . $time;
            // Добавляем заказ в базу
			//Бонус
			
			
			/*Вернуть после запуска бонусов
			$order->bonus_sale = 0;
			if(isset($_COOKIE['bonus']) && !empty($_COOKIE['bonus']) && isset($_COOKIE['percent']) && !empty($_COOKIE['percent']) && $_COOKIE['percent']>0){
				$bonus_sale = number_format(($cart->total_without_discount - $order->coupon_discount) * $_COOKIE['percent'] / 100, 2, ".", ".");
				$order->bonus_sale = $bonus_sale;
				$order->bonus_id = $_COOKIE['bonus'];
				Bonus::deleteBonusByUser($order->bonus_id ,$order->user_id);
				unset($_COOKIE['percent']);
				unset($_COOKIE['bonus']);
				setcookie("percent","",time()-3600,"/");
				setcookie("bonus","",time()-3600,"/");
			}
			*/
			
		
            $log = str_replace(array('	', PHP_EOL), '', print_r($order, true));
            file_put_contents(__DIR__ . '/log_cart.txt', $log . PHP_EOL, FILE_APPEND);      
			$order_id = $this->orders->add_order($order);
            $_SESSION['order_id'] = $order_id;

            $city = $this->city->get_city($order->city_id);
            $result = $this->limits->get_limit_by_date(explode(' ',$order->self_discharge_time)[0], str_replace(' ', '', $time), $city->name);
            if ($result) {
                $this->limits->update_limit(array('current' => $result->current+1), $result->id);
            }

            // Если использовали купон, увеличим количество его использований
            if ($cart->coupon) {
                $this->coupons->update_coupon($cart->coupon->id, array('usages' => $cart->coupon->usages + 1));
            }

            /*
                         * TODO ветпрепараты
                         * Не понятно, почему если пользователь незарегистрирован берутся данные из POST'а, если зарегисрирован, то из корзины, если
                         * и в случае зареганого пользователя и в случае незарегистрированного у нас есть товары в корзине
                         *
                         * */
            if ($cart->purchases && count($cart->purchases) > 0) {
                foreach ($cart->purchases as $purchase) {
                    if ($purchase->check != 0) {
                        $this->orders->add_purchase(array(
                            'order_id' => $order_id,
                            'variant_id' => intval($purchase->variant->id),
                            'amount' => intval($purchase->amount)
                        ));
                    }
                }
            }


            // Добавляем товары к заказу
            /*if (isset($_COOKIE['user_id'])) {
                foreach ($cart->purchases as $purchase) {
                    if ($purchase->check != 0) {
                        $this->orders->add_purchase(array(
                            'order_id' => $order_id,
                            'variant_id' => intval($purchase->variant->id),
                            'amount' => intval($purchase->amount)
                        ));
                    }
                }
            } else {

                foreach ($this->request->post('amounts') as $variant_id => $amount) {
                    $this->orders->add_purchase(array(
                        'order_id' => $order_id,
                        'variant_id' => intval($variant_id),
                        'amount' => intval($amount)
                    ));
                }
            }*/
//            TODO end
            $order = $this->orders->get_order($order_id);
			// Стоимость доставки
            $delivery = $this->delivery->get_delivery($order->delivery_id);
            if (!empty($delivery) && $delivery->free_from > $order->total_price) {

                $this->orders->update_order($order->id, array(
                    'delivery_price' => $delivery->price,
                    'separate_delivery' => $delivery->separate_payment
                ));
            }
            // Отправляем письмо пользователю
            $this->notify->email_order_user($order->id);

            // Отправляем письмо администратору
            $this->notify->email_order_admin($order->id);

            // Добавляем историю заказа для неарегистрированных пользователей
            if (!isset($_COOKIE['user_id']) && isset($_COOKIE['shopping_cart'])) {

                $purchase_count = count($_COOKIE['shopping_cart']);
                $history_count = count($_COOKIE['user_history']);
                if ($purchase_count > 5 || $purchase_count+$history_count>5) {
                    $this->cart->purge_user_history();
                    $i = 0;
                } else {
                    $i = count($_COOKIE['shopping_cart']);
                }
                foreach ($_COOKIE['shopping_cart'] as $variant_id => $amount) {
                    if (!isset($_COOKIE['user_history'][$variant_id]) && $i < 5) {
                        $this->cart->add_user_history($variant_id);
                    }
                    $i++;
                }
            }
			
			/**Добавление бонуса пользователю*/	
			//получаем активные бонусы
			/*Вернуть после запуска бонусов
			$bonus_all  = Bonus::getBonusbyStatusNotNull(1);	
			//проверяем дату акции
			$ret = array();
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_sale')==0){
					$ret[] = $mas;
				}elseif(strtotime($mas->time_to_sale)+86400 >= time() && strtotime($mas->time_from_sale) <= time())
					$ret[] = $mas;
			}
			$bonus_all = $ret;
			//проверяем сумму заказа
			$ret = array();
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_summ')==0){
					$ret[] = $mas;
				}elseif($order->total_price > $mas->summ)
					$ret[] = $mas;
			}
			$bonus_all = $ret;
			//города
			$ret = array();
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_cities')==0){
					$ret[] = $mas;
				}elseif(substr_count($mas->cities,$order->city_id)>0)
					$ret[] = $mas;
			}
			$bonus_all = $ret;
			//Время доставки    st_dilevery
			$ret = array();
			$dil_time = str_replace(' ','',$_POST['time']);			
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_dilevery')==0){
					$ret[] = $mas;
				}elseif(substr_count($mas->time_dilevery,$dil_time)>0){
					$ret[] = $mas;
				}
			}
			$bonus_all = $ret;
			//Дата заказа
			$ret = array();
			$date_order = date("Y-m-d");//2021-06-28
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_date_order')==0){
					$ret[] = $mas;
				}elseif($mas->date_order == $date_order)
					$ret[] = $mas;
			}
			$bonus_all = $ret;
			//продукты  $bonus_all->products
			//массив товаров в заказе
			$mass_product = array();
			$mass_brands = array();
			foreach ($cart->purchases as $pur){
				$mass_product[] = $pur->product->id;
				$mass_brands[]	= $pur->product->brand_id;
			}		
			$ret = array();
			foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_products')==0){
					$ret[] = $mas;
				}else{
					foreach($mass_product as $prod){
						if(substr_count($mas->products,$prod)>0){
							$ret[] = $mas;
							break;
						}
					}
				}			
			}
			$bonus_all = $ret;
			//Бренды
			$ret = array();
				foreach ($bonus_all as $mas){
				if(substr_count($mas->ifstatus,'st_brands')==0){
					$ret[] = $mas;
				}else{
					foreach($mass_brands as $brand){
						if(substr_count($mas->brands,$brand)>0){
							$ret[] = $mas;
							break;
						}
					}
				}			
			}
			//промокоды проверка
			$ret = array();
			foreach ($bonus_all as $key=>$mas){
				$query = $this->db->placehold("SELECT `promo`, `service`, `id_promo` FROM __bonus_promos WHERE id_bonus = ".$mas->id." AND active = 1 and user_id=0");
				$this->db->query($query);
				$str = $this->db->result();
				if(!empty($str)){
					$ret[$key] = $mas;
					$ret[$key]->promo = $str->promo;
					$ret[$key]->id_promo = $str->id_promo;
				}
			}
			$bonus_all = $ret;
			if(!empty($bonus_all)){
				//получаем бонусы пользователя
				$query = $this->db->placehold("SELECT `id_bonus` FROM __users WHERE id = ".$this->user->id);
				$this->db->query($query);
				$res = $this->db->result()->id_bonus;
				$bb = array();
				if(!empty($res )){
					$bb = explode(';',$res);
					foreach($bb as $key=>$b){
						if(empty($b))
							unset($bb[$key]);
					}
				}
				foreach ($bonus_all as $bon){
					if(!in_array($bon->id,$bb)){
						$bb[] = $bon->id;
						//пометить прокод для пользователя в s_promo
						$query = $this->db->placehold("UPDATE __bonus_promos SET `user_id` = ".$this->user->id." WHERE `id_promo` = '".$bon->id_promo."'");
						$this->db->query($query);	
					}
				}
				$bb = implode(';',$bb);
				$query = $this->db->placehold("UPDATE __users SET `id_bonus` = '".$bb."' WHERE id = ".$this->user->id);
				$this->db->query($query);	
				$_SESSION['bonusmy'] = 'YES';
			}
			*/
			
            // Очищаем корзину (сессию)
            $this->cart->empty_cart();
            if (isset($_COOKIE['shopping_cart'])){
                foreach ($_COOKIE['shopping_cart'] as $variant => $amount){
                    $this->cart->delete_item($variant);
                }
            }			
            // Перенаправляем на страницу заказа
            header('Location: ' . $this->config->root_url . '/thank?order_id=' . $order->id);

        } else {

            // Если нам запостили amounts, обновляем их
            if ($amounts = $this->request->post('amounts')) {
                foreach ($amounts as $variant_id => $amount) {
                    $this->cart->update_item($variant_id, $amount);
                }

                $coupon_code = trim($this->request->post('coupon_code', 'string'));
                if (empty($coupon_code)) {
                    $this->cart->apply_coupon('');
                    header('location: ' . $this->config->root_url . '/cart/');
                } else {
                    $coupon = $this->coupons->get_coupon((string)$coupon_code);

                    if (empty($coupon) || !$coupon->valid) {
                        $this->cart->apply_coupon($coupon_code);
                        $this->design->assign('coupon_error', 'invalid');
                    } else {
                        $this->cart->apply_coupon($coupon_code);
                        header('location: ' . $this->config->root_url . '/cart/');
                    }
                }
            }
        }
    }

    public function fetch()
    {
        $cart = $this->cart->get_cart();
        // Способы доставки
        $deliveries = $this->delivery->get_deliveries(array('enabled' => 1));
        /*
        echo $TotalWithOut;
        echo '<br/>'.$cart->total_without_discount_not_sale;*/
        $product_sale_price = Array();
        foreach ($deliveries as &$delivery) {
            //Пересчитываем стоимость товаров учитывая исключения и скидки
            $TotalWithOutSale = 0;
            $TotalWithOutSaleBrands = 0;
            $TotalSaleBrands = 0;
            $TotalWithOutSaleBrandsDate = Array();
            $TotalSaleBrandsDate = Array();
            $TotalWithOutSaleBrandsDateIsk = Array();
            $product_sale_price[$delivery->id] = Array();
            foreach ($cart->purchases as $variant_id => $item) {
                $ProductOut = 0;
                $product = new stdClass();
                $product->id = $item->product->id;
                $product->brand_id = $item->product->brand_id;
                $product->variant = $item->variant;
                $product->amount = $item->amount;
                $product->sale_double_item = $item->product->sale_double_item;
                $product->sale_double_item_value = $item->product->sale_double_item_value;
                $product->sale_double_item_sam = $item->product->sale_double_item_sam;
                $product->sale_double_item_sam_value = $item->product->sale_double_item_sam_value;
                if (!$product->variant->compare_price) {
                    $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = $product->variant->price;
                    $product_sale_price[$delivery->id][$item->variant->id]['percent'] = 0;
                } else {
                    $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                    $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = $product->variant->price;
                    $product_sale_price[$delivery->id][$item->variant->id]['percent'] = 0;
                }

                if ($product->variant->compare_price) {
                    $product->sale_price = false;
                }

                if (!$item->check || $item->check == '0') {
                    $ProductOut = 1;
                    continue;
                }

                //Проверка на исключение товара из скидки
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='product' AND value=?", $delivery->id, $product->id);
                if ($this->db->result()) {
                    if ($ProductOut == 0) {
                        $TotalWithOutSale += $product->variant->price * $product->amount;
                        $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
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
                            $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
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
                        $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                        $ProductOut = 1;
                        continue;
                    }
                }

                /*----price city deliver c отключением прочих скидок и без-----*/
                if (!$product->variant->compare_price) {
                    $this->db->query("SELECT * FROM __delivery_city WHERE delivery_id=?", $delivery->id);
                    $cities_deliver_temp = $this->db->results();
                    if (!empty($cities_deliver_temp)) {
                        $delivery->sale_city = Array();
                        foreach ($cities_deliver_temp as $item_city) {
                            if ($item_city->discount_percent) {
                                $city_discount_percent = intval($item_city->discount_percent);
                                $product_sale_price[$delivery->id][$item->variant->id][$item_city->city_id]['value'] = number_format(($product->variant->price) * ((100 - $city_discount_percent) / 100), 2, ".", ".");
                                $product_sale_price[$delivery->id][$item->variant->id][$item_city->city_id]['percent'] = $city_discount_percent;
                            }
                            if ($item_city->check_sale_other) {
                                $product_sale_price[$delivery->id][$item->variant->id][$item_city->city_id]['sale_other_not'] = 1;
                            } else {
                                $product_sale_price[$delivery->id][$item->variant->id][$item_city->city_id]['sale_other_not'] = 0;
                            }
                        }
                    }
                }
                /*----price city deliver-----*/

                //Проверка скидки на второй товар
                if ($ProductOut == 0) {
                    if (!$product->variant->compare_price) {
                        if ($product->sale_double_item && $product->sale_double_item_value != 0 && $delivery->id == 1) {
                            $product_percent = $product->sale_double_item_value;
                            $countProductSale = ($product->amount - $product->amount % 2) / 2;
                            $countProductNotSale = $product->amount - $countProductSale;
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $SaleDoubleProd = number_format(($product->variant->price * $countProductSale) * ((100 - $product_percent) / 100), 2, ".", ".");
                            $SaleDoubleProd += $countProductNotSale * $product->variant->price;
                            $TotalSaleBrands += $SaleDoubleProd;
                            $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                            $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = number_format($SaleDoubleProd / $product->amount, 2, ".", ".");
                            $percentDoubleTemp = 100 - (($SaleDoubleProd / $product->amount) * 100) / $product->variant->price;
                            $product_sale_price[$delivery->id][$item->variant->id]['percent'] = number_format($percentDoubleTemp, 0, ".", ".");
                            $ProductOut = 1;
                            continue;
                        }
                        if ($product->sale_double_item_sam && $product->sale_double_item_sam_value != 0 && $delivery->id == 2) {
                            $product_percent = $product->sale_double_item_sam_value;
                            $countProductSale = ($product->amount - $product->amount % 2) / 2;
                            $countProductNotSale = $product->amount - $countProductSale;
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $SaleDoubleProd += number_format(($product->variant->price * $countProductSale) * ((100 - $product_percent) / 100), 2, ".", ".");
                            $SaleDoubleProd += $countProductNotSale * $product->variant->price;
                            $TotalSaleBrands += $SaleDoubleProd;
                            $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                            $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = number_format($SaleDoubleProd / $product->amount, 2, ".", ".");
                            $percentDoubleTemp = 100 - (($SaleDoubleProd / $product->amount) * 100) / $product->variant->price;
                            $product_sale_price[$delivery->id][$item->variant->id]['percent'] = number_format($percentDoubleTemp, 0, ".", ".");
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
                                if ($product_row->discount_from <= $cart->total_without_discount) {
                                    $product_percent = $product_row->discount_percent;
                                    break;
                                }
                            }
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $product_percent) / 100), 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = number_format(($product->variant->price) * ((100 - $product_percent) / 100), 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                            $product_sale_price[$delivery->id][$item->variant->id]['percent'] = number_format($product_percent, 0, ".", "");
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
                                if ($brand_row->discount_from <= $cart->total_without_discount) {
                                    if (empty($brand_percent[$brand_row->date_sale])) {
                                        $brand_percent[$brand_row->date_sale] = $brand_row->discount_percent;
                                        $discount_day = ($product->variant->price) * ((100 - $brand_row->discount_percent) / 100);
                                        $product_sale_price[$delivery->id][$item->variant->id]['date'][date("Y", strtotime($brand_row->date_sale)) . date("n", strtotime($brand_row->date_sale)) . date("j", strtotime($brand_row->date_sale))]['value'] = number_format($discount_day, 2, ".", ".");
                                        $product_sale_price[$delivery->id][$item->variant->id]['date'][date("Y", strtotime($brand_row->date_sale)) . date("n", strtotime($brand_row->date_sale)) . date("j", strtotime($brand_row->date_sale))]['percent'] = number_format($brand_row->discount_percent, 0, ".", ".");
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
                                            if ($brand_row_temp->discount_from <= $cart->total_without_discount) {
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
                        $log = date('Y-m-d H:i:s') . ' deliv_id=' . $delivery->id . ' prod_id' . $product->brand_id . ' ';
                        $log .= str_replace(array('	', PHP_EOL), '', print_r($result_brand, true));
                        file_put_contents(__DIR__ . '/log_cart.txt', $log . PHP_EOL, FILE_APPEND);
                        if ($result_brand) {
                            $brand_percent = 0;
                            foreach ($result_brand as $brand_row) {
                                if ($brand_row->discount_from <= $cart->total_without_discount) {
                                    $brand_percent = $brand_row->discount_percent;
                                    break;
                                }
                            }
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            //Denis R
                            //$TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent) / 100), 2, ".", ".");

                            $TotalSaleBrands += number_format((round($product->variant->price * ((100 - $brand_percent) / 100), 2) * $product->amount), 2, ".", "");

                            $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = number_format(($product->variant->price) * ((100 - $brand_percent) / 100), 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['sale'] = 1;
                            $product_sale_price[$delivery->id][$item->variant->id]['percent'] = number_format($brand_percent, 0, ".", ".");
                            $ProductOut = 1;
                            continue;
                        }
                    }
                }
                if (!$product->variant->compare_price) {
                    $discount_percent_product = 0;
                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
                    foreach ($this->db->results() as $row) {
                        if ($cart->total_price >= $row->discount_from && $discount_percent_product < $row->discount_percent) {
                            $discount_percent_product = intval($row->discount_percent);
                            $product_sale_price[$delivery->id][$item->variant->id]['base_price'] = number_format(($product->variant->price) * ((100 - $row->discount_percent) / 100), 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['percent'] = number_format($row->discount_percent, 0, ".", ".");
                        }
                    }

                    if ($delivery->id == 1) {
                        if ($discount_percent_product <= 10) {
                            $DiscountHH = ($product->variant->price) * ((100 - 10) / 100);
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_week']['value'] = number_format($DiscountHH, 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_week']['percent'] = 10;
                        } else {
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_week']['value'] = $product_sale_price[$delivery->id][$item->variant->id]['base_price'];
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_week']['percent'] = number_format($discount_percent_product, 0, ".", ".");
                        }
                        if ($discount_percent_product <= 20) {
                            $DiscountHH = ($product->variant->price) * ((100 - 20) / 100);
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_ends']['value'] = number_format($DiscountHH, 2, ".", ".");
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_ends']['percent'] = 20;
                        } else {
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_ends']['value'] = $product_sale_price[$delivery->id][$item->variant->id]['base_price'];
                            $product_sale_price[$delivery->id][$item->variant->id]['price_hh_ends']['percent'] = number_format($discount_percent_product, 0, ".", ".");
                        }
                    }
                    //$date_sale[]=$delivery->id;
                    $this->db->query("SELECT * FROM __delivery_date WHERE delivery_id=? AND discount_from<" . $cart->total_without_discount . "  ORDER BY discount_from DESC LIMIT 1", $delivery->id);
                    $deliveries_date_temp = $this->db->results();
                    $new_date = date("Y-m-d");
                    foreach ($deliveries_date_temp as $delivery_date) {
                        if ($new_date <= $delivery_date->date_sale) {
                            $discount_day = ($product->variant->price) * ((100 - $delivery_date->discount_percent) / 100);
                            if (empty($product_sale_price[$delivery->id][$item->variant->id]['date'][date("Y", strtotime($delivery_date->date_sale)) . date("n", strtotime($delivery_date->date_sale)) . date("j", strtotime($delivery_date->date_sale))])) {
                                $product_sale_price[$delivery->id][$item->variant->id]['date'][date("Y", strtotime($delivery_date->date_sale)) . date("n", strtotime($delivery_date->date_sale)) . date("j", strtotime($delivery_date->date_sale))]['value'] = number_format($discount_day, 2, ".", ".");
                                $product_sale_price[$delivery->id][$item->variant->id]['date'][date("Y", strtotime($delivery_date->date_sale)) . date("n", strtotime($delivery_date->date_sale)) . date("j", strtotime($delivery_date->date_sale))]['percent'] = $delivery_date->discount_percent;
                            }
                        }
                    }
                }
            }

            $_SESSION['delivery_minus'][$delivery->id] = $TotalWithOutSale;
            $_SESSION['delivery_brand_plus'][$delivery->id] = $TotalSaleBrands;
            $_SESSION['delivery_brand_minus'][$delivery->id] = $TotalWithOutSaleBrands;
            $_SESSION['delivery_brand_data_plus'][$delivery->id] = $TotalSaleBrandsDate;
            $_SESSION['delivery_brand_data_minus'][$delivery->id] = $TotalWithOutSaleBrandsDate;
            $_SESSION['delivery_brand_data_minus_isk'][$delivery->id] = $TotalWithOutSaleBrandsDateIsk;
            $delivery->payments = $this->delivery->get_delivery_payments($delivery->id);
            $delivery->discount_for_order = $cart->total_without_discount - $cart->total_price;
            $delivery->discount_percent = 0;
            $delivery->total_price = $cart->total_price;
            $delivery->total_price = $cart->total_without_discount - $delivery->discount_for_order - $TotalWithOutSaleBrands + $TotalSaleBrands;
            $delivery->next_percent = 100; //ищем значение следующего порога скидки
            $delivery->next_price = 0; //ищем значение следующего порога скидки

            $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
            foreach ($this->db->results() as $row) {
                if ($cart->total_price >= $row->discount_from && $delivery->discount_percent < $row->discount_percent) {
                    $delivery->discount_percent = intval($row->discount_percent);
                    $delivery->discount_price = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $row->discount_percent) / 100);
                    $delivery->discount_for_order = ($cart->total_without_discount - $cart->total_price) + $delivery->discount_price;
                    $delivery->total_price = $cart->total_without_discount - $delivery->discount_for_order - $TotalWithOutSaleBrands + $TotalSaleBrands;
                    $delivery->discount_for_order = $cart->total_without_discount - $delivery->total_price; //получаем сумму скидки на досавку
                }
                if ($cart->total_price < $row->discount_from && $delivery->next_percent > $row->discount_percent) {
                    $delivery->next_percent = intval($row->discount_percent);
                    $delivery->next_price = $row->discount_from - $cart->total_price;
                }
            }

//            print_r($TotalWithOutSale);
            /*----price city deliver-----*/
            $this->db->query("SELECT * FROM __delivery_city WHERE delivery_id=?", $delivery->id);
            $cities_deliver = $this->db->results();
            if (!empty($cities_deliver)) {
                $delivery->sale_city = Array();
                foreach ($cities_deliver as $item_city) {
                    if ($item_city->discount_percent) {
                        $city_discount_percent = intval($item_city->discount_percent);
                        $delivery->sale_city[$item_city->city_id]['discount_percent'] = $city_discount_percent;
                        if ($item_city->check_sale_other) {
                            $city_discount_price = ($cart->total_without_discount_not_sale - $TotalWithOutSale) * (1 - (100 - $city_discount_percent) / 100);
                            $city_discount_for_order = ($cart->total_without_discount - $cart->total_price) + $city_discount_price;
                            $city_total_price = $cart->total_without_discount - $city_discount_for_order;
                            $delivery->sale_city[$item_city->city_id]['total_price'] = number_format($city_total_price, 2, ".", ".");
                            $delivery->sale_city[$item_city->city_id]['sale'] = number_format($cart->total_without_discount - $city_total_price, 2, ".", ".");
                        } else {
                            $city_discount_price = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $city_discount_percent) / 100);
                            $city_discount_for_order = ($cart->total_without_discount - $cart->total_price) + $city_discount_price;
                            $city_total_price = $cart->total_without_discount - $city_discount_for_order - $TotalWithOutSaleBrands + $TotalSaleBrands;
                            $delivery->sale_city[$item_city->city_id]['total_price'] = number_format($city_total_price, 2, ".", ".");
                            $delivery->sale_city[$item_city->city_id]['sale'] = number_format($cart->total_without_discount - $city_total_price, 2, ".", ".");
                        }
                    }
                }
            }
            /*----price city deliver-----*/

            if ($delivery->id == 1) {
                if ($delivery->discount_percent <= 10) {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - 10) / 100);
                    $DiscountHH = number_format($cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands, 2, ".", ".");
                    $this->design->assign('price_hh_week', $DiscountHH);
                    $this->design->assign('discount_hh_week', number_format($cart->total_without_discount - $DiscountHH, 2, ".", "."));
                } else {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $delivery->discount_percent) / 100);
                    $DiscountHH = number_format($cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands, 2, ".", ".");
                    $this->design->assign('price_hh_week', $DiscountHH);
                    $this->design->assign('discount_hh_week', number_format($cart->total_without_discount - $DiscountHH, 2, ".", "."));
                }
                if ($delivery->discount_percent <= 20) {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - 20) / 100);
                    $DiscountHH = number_format($cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands, 2, ".", ".");
                    $this->design->assign('price_hh_ends', $DiscountHH);
                    $this->design->assign('discount_hh_ends', number_format($cart->total_without_discount - $DiscountHH, 2, ".", "."));
                } else {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $delivery->discount_percent) / 100);
                    $DiscountHH = number_format($cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands, 2, ".", ".");
                    $this->design->assign('price_hh_ends', $DiscountHH);
                    $this->design->assign('discount_hh_ends', number_format($cart->total_without_discount - $DiscountHH, 2, ".", "."));
                }
            }
            //$date_sale[]=$delivery->id;
            $this->db->query("SELECT * FROM __delivery_date WHERE delivery_id=? AND discount_from<" . $cart->total_without_discount . "  ORDER BY discount_from DESC LIMIT 1", $delivery->id);
            $deliveries_date = $this->db->results();
            $new_date = date("Y-m-d");
            foreach ($deliveries_date as $delivery_date) {
                $price_day = 0;
                if ($new_date <= $delivery_date->date_sale) {
                    $TWOSD = empty($TotalWithOutSaleBrandsDate[$delivery_date->date_sale]) ? 0 : $TotalWithOutSaleBrandsDate[$delivery_date->date_sale];
                    $TWOSDI = empty($TotalWithOutSaleBrandsDateIsk[$delivery_date->date_sale]) ? 0 : $TotalWithOutSaleBrandsDateIsk[$delivery_date->date_sale];
                    $TSBD = empty($TotalSaleBrandsDate[$delivery_date->date_sale]) ? 0 : $TotalSaleBrandsDate[$delivery_date->date_sale];
                    $discount_day = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands - $TWOSD) * (1 - (100 - $delivery_date->discount_percent) / 100);
                    $price_day = number_format($cart->total_without_discount - $discount_day - $TotalWithOutSaleBrands - $TWOSD - $TWOSDI + $TotalSaleBrands + $TSBD, 2, ".", ".");
                    $discount_day = $cart->total_without_discount - $price_day;
                    $date_sale[$delivery->id][] = array($delivery_date->discount_percent,
                        date("Y", strtotime($delivery_date->date_sale)), date("n", strtotime($delivery_date->date_sale)), date("j", strtotime($delivery_date->date_sale)),
                        $discount_day, $price_day);
                }
            }
        }
        $this->design->assign('date_sale', $date_sale);

        $vetpreparaty = $this->settings->vetpreparaty;
        $this->design->assign('vetpreparaty', $vetpreparaty);

        $other_city = $this->settings->other_city;
        $this->design->assign('other_city', $other_city);

        $date_and_time = '';


        $times = $this->settings->times;
        if ($times) {
            foreach ($times as $delivery_time => $time) {
                if (strtotime($time)) {
                    $current_date = date('d.m');
                    $current_time = date("H:i");

                    if (strtotime($current_time) < strtotime($time)) {
                        $date_and_time .= $current_date . '(' . $delivery_time . ')' . ';';
                    }
                }
            }
        }


        $time_limits = $this->limits->get_limits();
        if ($time_limits) {
            foreach ($time_limits as $limit) {
                if ($limit->current >= $limit->products_limit) {
                    $date = date('d.m', strtotime($limit->date));
                    $time = '(' . $limit->time . ')';
                    $city = $limit->city ? $limit->city : '';
                    $date_and_time .= $date.$time.$city.';';
                }
            }
        }

        $date_and_time .= $this->settings->dateandtime;
        $this->design->assign('date_and_time', $date_and_time);
        /* История заказов в корзине */
        if (isset($_COOKIE['user_id'])) {
            $orders = $this->orders->get_orders(array('user_id' => $_COOKIE['user_id']));

            if ($orders) {
                $current_purchases = array();
                foreach ($cart->purchases as $item){
                    $current_purchases[] = $item->variant->id;
                }

                $history = array();
                foreach ($orders as $order) {
                    $purchases = $this->orders->get_purchases(array('order_id' => $order->id));
                    foreach ($purchases as $purchase) {
                        if (!in_array($purchase->variant_id, $history) && !in_array($purchase->variant_id, $current_purchases)) {
                            $variant = $this->variants->get_variant($purchase->variant_id);
                            $__product = $this->products->get_product((int)$variant->product_id);
                            if ($variant && $variant->stock > 0 && $__product->visible > 0) {
                                $history[$purchase->variant_id] = array(
                                    'variant' => $variant,
                                    'product' => $this->products->get_product((int)$variant->product_id),
                                    'brand' => $this->brands->get_brand((int)$this->products->get_product((int)$variant->product_id)->brand_id),
                                    'images' => $this->products->get_images(array('product_id' => (int)$variant->product_id))
                                );
                            }
                        }
                    }
                }
            }

        } elseif (isset($_COOKIE['user_history'])){
            $history = array();
            foreach ($_COOKIE['user_history'] as $variant_id => $amount){
                $variant = $this->variants->get_variant($variant_id);
                $__product = $this->products->get_product((int)$variant->product_id);
                if ($variant && $variant->stock > 0 && $__product->visible) {
                    $product = $this->products->get_product((int)$variant->product_id);
                    $history[$variant_id] = array(
                        'variant' => $variant,
                        'product' => $product,
                        'brand' => $this->brands->get_brand((int)$product->brand_id),
                        'images' => $this->products->get_images(array('product_id' => (int)$variant->product_id))
                    );
                }
            }
        }



        if (!empty($history)){
            $this->design->assign('history', $history);
        }

        /* Рекомендованные товары к корзине */

        $filter = [];
        $filter['category_id'] = array();
        $filter['sort'] = 'rand';
        $filter['limit'] = 12;
        $filter['id'] = array();

        $featured_products = array();
        $featured_categories = array();

        foreach ($cart->purchases as $purchase) {

            $variant = $this->variants->get_variants(intval($purchase->product->id));


            $product_category = $this->categories->get_product_categories($purchase->product->id);
            $category = $this->categories->get_category(intval($product_category[0]->category_id));
            $parent_category = $category->path;
            $parent_category = $this->categories->get_category(intval($parent_category[0]->id));


            $featured_categories = array_merge($featured_categories, $parent_category->children);


        }

        $filter['category_id'] = $featured_categories;
        $filter['featured_cart'] = true;


        $featured_products = $this->products->renders($filter);
        $this->design->assign('featured_products', $featured_products);


 

		if(isset($_COOKIE['bonus']) && !empty($_COOKIE['bonus']) && isset($_COOKIE['percent']) && !empty($_COOKIE['percent']) && $_COOKIE['percent']>0){
			$deliveries[0]->bonus_sale = number_format($deliveries[0]->total_price * $_COOKIE['percent'] / 100, 2, ".", ".");
			$deliveries[0]->bonus_price = number_format($deliveries[0]->total_price - ($deliveries[0]->total_price * $_COOKIE['percent'] / 100), 2, ".", ".");
			$cart->bonus_price = $deliveries[0]->bonus_price;
			$_COOKIE['price_without_bonus'] = $deliveries[0]->total_price;
		   }
        $this->design->assign('deliveries', $deliveries);
		
		
        // Варианты оплаты
        $payment_methods = $this->payment->get_payment_methods(array('enabled' => 1));
        $this->design->assign('payment_methods', $payment_methods);


        // Данные пользователя
        if ($this->user) {
			print_r($this->design->assign);
            $last_order = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => 1));
            $last_order = reset($last_order);
            if ($last_order) {
                /*if ($last_order->name) {$this->design->assign('name', $last_order->name);}
                    else{$this->design->assign('name', $this->user->name);}
                if ($last_order->email){$this->design->assign('email', $last_order->email);}
                    else{$this->design->assign('email', $this->user->email);}*/
                if ($last_order->phone) {
                    $this->design->assign('phone', $last_order->phone);
                } else {
                    $this->design->assign('phone', $this->user->phone);
                }
                $this->design->assign('address', $this->user->address);
                $this->design->assign('flat_num', $this->user->flat_num);
                //$this->design->assign('express', $this->user->express);
            } else {
                /*$this->design->assign('name', $this->user->name);
                $this->design->assign('email', $this->user->email);*/
                $this->design->assign('phone', $this->user->phone);
                $this->design->assign('address', $this->user->address);
                $this->design->assign('flat_num', $this->user->flat_num);
               // $this->design->assign('express', $this->user->express);
            }
        }

        //следующий блок скидок для счастливых часов
        $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from>'" . $cart->total_without_discount . "' ORDER by discount_from ASC");
        $NextDiscount = $this->db->result();
        if ($NextDiscount) {
            $NextDiscountArray = array();
            $NextDiscountArray["procent"] = intval($NextDiscount->discount_percent);
            $NextDiscountArray["sum"] = $NextDiscount->discount_from - $cart->total_without_discount;
            $NextDiscountArray["procent_week"] = intval(20);
            $NextDiscountArray["sum_week"] = 99 - $cart->total_without_discount;

            $this->design->assign('nextdiscount', $NextDiscountArray);
        } else {
            $this->design->assign('nextdiscount', '');
        }

        /*----Города доставки-----*/
        $this->db->query("SELECT * FROM __delivery_city dc LEFT JOIN __shipping_city sc ON dc.city_id=sc.id WHERE sc.visible='1' ORDER BY sc.name");
        $cities_deliver = $this->db->results();
        if (!empty($cities_deliver)) {
            $city = array();
            foreach ($cities_deliver as $item_city) {
                $check_city_deliver = true;
                foreach ($city as &$new_item_city) {
                    if ($new_item_city['city_id'] == $item_city->city_id) {
                        $new_item_city['delivery'][$item_city->delivery_id]['active'] = 1;
                        $new_item_city['delivery'][$item_city->delivery_id]['city_min'] = $item_city->from_sum;
                        $check_city_deliver = false;
                        break;
                    }
                }
                if ($check_city_deliver) {
                    $city_name = $this->city->get_city($item_city->city_id)->name;
                    $city_region_id = $this->city->get_city($item_city->city_id)->region_id;
                    $this->db->query("SELECT * FROM __shipping_area WHERE shipping_city_id=?", $item_city->city_id);
                    $city_areas = $this->db->results();
                    $city[] = array('region_id' => $city_region_id, 'city_id' => $item_city->city_id, 'delivery' => array($item_city->delivery_id => array('active' => '1', 'city_min' => $item_city->from_sum)), 'city_name' => $city_name, 'city_area' => $city_areas);
                }
            }
        }
        $this->design->assign('city', $city);
		$this->design->assign('region_short_name', $_SESSION['region_short_name']);

        /* Вывод категорий */
        $this->design->assign('categories', $this->categories->get_categories_tree(array('visible' => 1, 'products_count' => true)));

        /* Скрываем временные промежутки в выбранном городе */
        $hide_time = 0;
        if (!empty($_SESSION['region_short_name'])) {
            $this->db->query("SELECT hide_time FROM __shipping_city WHERE name LIKE ?", $_SESSION['region_short_name']);
            $result = $this->db->result()->hide_time;
            if ($result) {
                $hide_time = $result;
            }
        }
        $this->design->assign('hide_time', $hide_time);

        $city_minsk = Array();
        $this->db->query("SELECT * FROM __shipping_area WHERE shipping_city_id=?", 0);
        $city_minsk = $this->db->results();
        $this->design->assign('city_minsk', $city_minsk);
        // Если существуют валидные купоны, нужно вывести инпут для купона
        if ($this->coupons->count_coupons(array('valid' => 1)) > 0) {
            //$this->design->assign('coupon_request', true);
        }
        $product_sale_price = json_encode($product_sale_price);
        $this->design->assign('product_sale_price', $product_sale_price);
        // Выводим корзину
        return $this->design->fetch('cart.tpl');
    }
}
