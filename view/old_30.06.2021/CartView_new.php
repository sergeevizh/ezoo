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

        // Если нажали оформить заказ
        if (isset($_POST['checkout'])) {
            $order = new stdClass;
            $order->delivery_id = $this->request->post('delivery_id', 'integer');
            $order->payment_method_id = $this->request->post('payment_method_id', 'integer');
            $order->name = $this->request->post('name');
            $order->email = $this->request->post('email');
            $order->address = $this->request->post('address');
            $order->self_discharge_time = $this->request->post('self_discharge_time');
            $time = $this->request->post('time');
            $order->phone = $this->request->post('phone');
            $order->comment = $this->request->post('comment');
            $order->ip = $_SERVER['REMOTE_ADDR'];

            $this->design->assign('payment_method_id', $order->payment_method_id);
            $this->design->assign('delivery_id', $order->delivery_id);
            $this->design->assign('time', $time);
            $this->design->assign('name', $order->name);
            $this->design->assign('email', $order->email);
            $this->design->assign('phone', $order->phone);
            $this->design->assign('address', $order->address);
            $this->design->assign('self_discharge_time', $order->self_discharge_time);
			
			//бренд
			$this->design->assign('brands', $brands);

			

 //            Скидка   поправить!!!!!!!!!! ORDER BY discount_from
            $cart = $this->cart->get_cart();
			$brand_id = $this->brands->get_brand(intval($product->brand_id));     $this->design->assign('brand', $this->brands->get_brand(intval($product->brand_id)));
			
            if(!empty($order->delivery_id)){
                $delivery = $this->delivery->get_delivery($order->delivery_id);

                $timeId = intval(substr($time, 0, 2));

                if(!empty($delivery)){
                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=? AND discount_from >= 0 AND discount_percent > 0 AND discount_period_code=? ORDER BY discount_from", $delivery->id, $timeId);
                    $delivery_discounts = $this->db->results();
                    if(!empty($delivery_discounts)){



                        $selected_discount_percent = 0;
                        foreach ($delivery_discounts as $row) {
							
							
						if ($cart->total_price >= $row->discount_from && $row->discount_percent > $selected_discount_percent) {
                               $selected_discount_percent = $row->discount_percent;
							   
						
												
						 $discount_price = ($cart->total_without_discount_not_sale-$_SESSION['delivery_minus'][$delivery->id]) * (1-(100 - $row->discount_percent) / 100);


                              
                              $order->coupon_discount = round(($cart->total_without_discount-$cart->total_price)+$discount_price, 2);

						  
                           }
						   
					
                        }

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
            }

            if (empty($order->name)) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($order->email)) {
                $this->design->assign('error', 'empty_email');
//            } elseif ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code)) {
//                $this->design->assign('error', 'captcha');
            } else {
                $order->self_discharge_time .= ' ' . $time;
                // Добавляем заказ в базу
                $order_id = $this->orders->add_order($order);
                $_SESSION['order_id'] = $order_id;

                // Если использовали купон, увеличим количество его использований
                if ($cart->coupon) {
                    $this->coupons->update_coupon($cart->coupon->id, array('usages' => $cart->coupon->usages + 1));
                }

                // Добавляем товары к заказу
                foreach ($this->request->post('amounts') as $variant_id => $amount) {
                    $this->orders->add_purchase(array(
                        'order_id' => $order_id,
                        'variant_id' => intval($variant_id),
                        'amount' => intval($amount)
                    ));
                }
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

                // Очищаем корзину (сессию)
                $this->cart->empty_cart();

                // Перенаправляем на страницу заказа
                header('Location: ' . $this->config->root_url . '/order/' . $order->url);
            }
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






        foreach ($deliveries as &$delivery) {


            //Пересчитываем стоимость товаров учитывая исключения и скидки
            $TotalWithOutSale = 0;

            foreach ($cart->purchases as $variant_id => $item) {
                $ProductOut = 0;
                $product = new stdClass();
                $product->id = $item->product->id;
                $product->brand_id = $item->product->brand_id;
                $product->variant = $item->variant;
                $product->amount = $item->amount;

                //Проверка на исключение товара из скидки
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='product' AND value=?", $delivery->id,$product->id);
                if($this->db->result()){
                    if($ProductOut==0){
                        $TotalWithOutSale += $product->variant->price * $product->amount;
                        $ProductOut=1;
                        continue;
                    }

                }

                //Проверка на исключение категорий
                $this->db->query("SELECT * FROM __products_categories WHERE product_id=?", $product->id);
                $Categorys = $this->db->results();
                foreach ($Categorys as $Category) {
                    $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='category' AND value=?", $delivery->id,$Category->category_id);
                    if($this->db->result()){
                        if($ProductOut==0){
                            $TotalWithOutSale += $product->variant->price * $product->amount;
                            $ProductOut=1;
                            continue;
                        }
                    }
                }

                //Проверка на исключение бренда из скидки
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='brand' AND value=?", $delivery->id,$product->brand_id);
                if($this->db->result()){
                    if($ProductOut==0){
                        $TotalWithOutSale += $product->variant->price * $product->amount;
                        $ProductOut=1;
                        continue;
                    }
                }
            }


            $_SESSION['delivery_minus'][$delivery->id] = $TotalWithOutSale;




            $delivery->payments = $this->delivery->get_delivery_payments($delivery->id);
            $delivery->discount_for_order = $cart->total_without_discount-$cart->total_price;
            $delivery->discount_percent = 0;
            $delivery->total_price = $cart->total_price;
            $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=? ORDER BY discount_from", $delivery->id);
            foreach ($this->db->results() as $row) {
                //старые скидки для доставки без учёта времени
                /*if ($cart->total_price >= $row->discount_from && $delivery->discount_percent < $row->discount_percent && ($row->discount_period_code == 0 || $row->discount_period_code == null)) {
                   $delivery->discount_percent = $row->discount_percent;
                    $delivery->discount_price = ($cart->total_without_discount_not_sale-$TotalWithOutSale) * (1-(100 - $row->discount_percent) / 100);
                    $delivery->discount_for_order = ($cart->total_without_discount-$cart->total_price)+$delivery->discount_price;
                    $delivery->total_price = $cart->total_without_discount- $delivery->discount_for_order;
                }*/
                if ($cart->total_price >= $row->discount_from && $delivery->discount_percent < $row->discount_percent && ($row->discount_period_code != 0 || $row->discount_period_code != null)) {
                    $delivery->discount_time_array[$row->discount_period_code]['discount_percent'] = $row->discount_percent;
                    $delivery->discount_time_array[$row->discount_period_code]['discount_price'] = ($cart->total_without_discount_not_sale-$TotalWithOutSale) * (1-(100 - $delivery->discount_time_array[$row->discount_period_code]['discount_percent']) / 100);
                    $delivery->discount_time_array[$row->discount_period_code]['discount_for_order'] = ($cart->total_without_discount-$cart->total_price)+ $delivery->discount_time_array[$row->discount_period_code]['discount_price'];
                    $delivery->discount_time_array[$row->discount_period_code]['total_price'] = $cart->total_without_discount - $delivery->discount_time_array[$row->discount_period_code]['discount_for_order'];
                }
            }
        }


        $this->design->assign('deliveries', $deliveries);
        $this->design->assign('js_deliveries', json_encode($deliveries));

        // Варианты оплаты
        $payment_methods = $this->payment->get_payment_methods(array('enabled' => 1));
        $this->design->assign('payment_methods', $payment_methods);

        // Данные пользователя
        if ($this->user) {
            $last_order = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => 1));
            $last_order = reset($last_order);
            if ($last_order) {
                $this->design->assign('name', $last_order->name);
                $this->design->assign('email', $last_order->email);
                $this->design->assign('phone', $last_order->phone);
                $this->design->assign('address', $last_order->address);
            } else {
                $this->design->assign('name', $this->user->name);
                $this->design->assign('email', $this->user->email);
                $this->design->assign('phone', $this->user->phone);
                $this->design->assign('address', $this->user->address);
            }
        }

        // Если существуют валидные купоны, нужно вывести инпут для купона
        if ($this->coupons->count_coupons(array('valid' => 1)) > 0) {
            //$this->design->assign('coupon_request', true);
        }

        // Выводим корзину
        return $this->design->fetch('cart.tpl');
    }
}
