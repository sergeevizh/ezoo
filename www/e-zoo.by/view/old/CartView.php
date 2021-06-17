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
            if (!empty($this->request->post('press_check'))){return $this->design->fetch('cart.tpl');}
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

            $CustomDiscount = 0;
            $DayNumber = date('N',strtotime(   $order->self_discharge_time));
            if($order->delivery_id==1){
                if($DayNumber>=1&&$DayNumber<=4&&($time=="10:00 - 12:00"||$time=='12:00 - 15:00')){
                    $CustomDiscount = 10;
                }elseif($DayNumber==5||$DayNumber==6||$DayNumber==7){
                    $CustomDiscount = 20;
                }

            }


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

			

 //            Скидка
            $cart = $this->cart->get_cart();
			$brand_id = $this->brands->get_brand(intval($product->brand_id));
			$this->design->assign('brand', $this->brands->get_brand(intval($product->brand_id)));
			
            if(!empty($order->delivery_id)){
                $delivery = $this->delivery->get_delivery($order->delivery_id);


                if(!empty($delivery)){
                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=? AND discount_from >= 0 AND discount_percent > 0", $delivery->id);
                    $delivery_discounts = $this->db->results();
                    //по дате доставки
                    $this->db->query("SELECT * FROM __delivery_date WHERE delivery_id=?  AND  discount_from<".$cart->total_without_discount, $delivery->id);
                    $deliveries_date = $this->db->results();
                    $new_date = $order->self_discharge_time;
                    $date_sale_check = false;
                    foreach ($deliveries_date as $delivery_date){
                        $price_day = 0;
                        if (strtotime($new_date) == strtotime($delivery_date->date_sale)) {
                            $TWOSD = empty($_SESSION['delivery_brand_data_minus'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_minus'][$delivery->id][$delivery_date->date_sale];
                            $TWOSDI = empty($_SESSION['delivery_brand_data_minus_isk'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_minus_isk'][$delivery->id][$delivery_date->date_sale];
                            $TSBD = empty($_SESSION['delivery_brand_data_plus'][$delivery->id][$delivery_date->date_sale]) ? 0 : $_SESSION['delivery_brand_data_plus'][$delivery->id][$delivery_date->date_sale];
                            $discount_day = ($cart->total_without_discount_not_sale - $_SESSION['delivery_minus'][$delivery->id]-$_SESSION['delivery_brand_minus'][$delivery->id]-$TWOSD) * (1 - (100 - $delivery_date->discount_percent) / 100);
                            $order->coupon_discount = round(($cart->total_without_discount-$cart->total_price)+ $discount_day+$_SESSION['delivery_brand_minus'][$delivery->id]+$TWOSD+$TWOSDI-$_SESSION['delivery_brand_plus'][$delivery->id]-$TSBD, 2);
                            $date_sale_check = true;
                            break;
                        }
                    }
                    if(!empty($delivery_discounts) && !$date_sale_check){
                        $selected_discount_percent = 0;
                        foreach ($delivery_discounts as $row) {
							
						if ($cart->total_price >= $row->discount_from && $row->discount_percent > $selected_discount_percent) {
                               $selected_discount_percent = $row->discount_percent;
												if($CustomDiscount==0 || $row->discount_percent>$CustomDiscount){
												    $discount_price = ($cart->total_without_discount_not_sale-$_SESSION['delivery_minus'][$delivery->id]-$_SESSION['delivery_brand_minus'][$delivery->id]) * (1-(100 - $row->discount_percent) / 100);}
												else{
												    $discount_price = ($cart->total_without_discount_not_sale-$_SESSION['delivery_minus'][$delivery->id]-$_SESSION['delivery_brand_minus'][$delivery->id]) * (1-(100 - $CustomDiscount) / 100);}
                              $order->coupon_discount = round(($cart->total_without_discount-$cart->total_price)+$discount_price+$_SESSION['delivery_brand_minus'][$delivery->id]-$_SESSION['delivery_brand_plus'][$delivery->id], 2);
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
            }else{
				$UserByEmail = $this->users->get_users(array('keyword'=>$order->email));
				if(!empty($UserByEmail)&&!empty($UserByEmail[0]))
				{
					$order->user_id = $UserByEmail[0]->id;
				}
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
            $TotalWithOutSaleBrands = 0;
            $TotalSaleBrands = 0;
            $TotalWithOutSaleBrandsDate = Array();
            $TotalSaleBrandsDate = Array();
            $TotalWithOutSaleBrandsDateIsk = Array();

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
                //Проверка на индивидуальную скидку по товару
                if ($ProductOut == 0) {
                    if (!$product->variant->compare_price) {
                        $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=? AND product_id=? ORDER by discount_percent DESC", $delivery->id, $product->id);
                        $result_product = $this->db->results();
                        if ($result_product) {
                            $product_percent = 0;
                            foreach ($result_product as $product_row) {
                                if ($product_row->discount_from<=$cart->total_without_discount){
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
                                if ($brand_row->discount_from<=$cart->total_without_discount){
                                    if (empty($brand_percent[$brand_row->date_sale])){
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
                                            if ($brand_row_temp->discount_from<=$cart->total_without_discount){
                                                $brand_percent_temp = $brand_row_temp->discount_percent;
                                                break;
                                            }
                                        }
                                        $TotalSaleBrandsTemp = number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent_temp) / 100), 2, ".", ".");
                                    }
                                }
                            }

                            foreach ($brand_percent as $key=>$brand_percent_item){
                                $TotalWithOutSaleBrandsDateIsk[$key] += $TotalSaleBrandsTemp;
                                $TotalWithOutSaleBrandsDate[$key] +=  ($TotalSaleBrandsTemp==0) ? $product->variant->price * $product->amount : 0;
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
                                if ($brand_row->discount_from<=$cart->total_without_discount){
                                    $brand_percent = $brand_row->discount_percent;
                                    break;
                                }
                            }
                                    $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                                    $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent) / 100), 2, ".", ".");
                                    $ProductOut = 1;
                                    continue;
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
            $delivery->discount_for_order = $cart->total_without_discount-$cart->total_price;
            $delivery->discount_percent = 0;
            $delivery->total_price = $cart->total_price;
            $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
            foreach ($this->db->results() as $row) {
                if ($cart->total_price >= $row->discount_from && $delivery->discount_percent < $row->discount_percent) {
                    $delivery->discount_percent = intval($row->discount_percent);
                    $delivery->discount_price = ($cart->total_without_discount_not_sale-$TotalWithOutSale-$TotalWithOutSaleBrands) * (1-(100 - $row->discount_percent) / 100);
                    $delivery->discount_for_order = ($cart->total_without_discount-$cart->total_price)+$delivery->discount_price;
                    $delivery->total_price = $cart->total_without_discount - $delivery->discount_for_order-$TotalWithOutSaleBrands+$TotalSaleBrands;
                }
            }
            if($delivery->id==1){
                if ($delivery->discount_percent<=10) {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - 10) / 100);
                    $this->design->assign('discount_hh_week', $DiscountHH);
                    $this->design->assign('price_hh_week', $cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands);
                }
                else{
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $delivery->discount_percent) / 100);
                    $this->design->assign('discount_hh_week', $DiscountHH);
                    $this->design->assign('price_hh_week', $cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands);
                }
                if ($delivery->discount_percent<=20) {
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - 20) / 100);
                    $this->design->assign('discount_hh_ends', $DiscountHH);
                    $this->design->assign('price_hh_ends', $cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands);
                }
                else{
                    $DiscountHH = ($cart->total_without_discount_not_sale - $TotalWithOutSale - $TotalWithOutSaleBrands) * (1 - (100 - $delivery->discount_percent) / 100);
                    $this->design->assign('discount_hh_week', $DiscountHH);
                    $this->design->assign('price_hh_week', $cart->total_without_discount - $DiscountHH - $TotalWithOutSaleBrands + $TotalSaleBrands);
                }
            }
            //$date_sale[]=$delivery->id;
            $this->db->query("SELECT * FROM __delivery_date WHERE delivery_id=? AND discount_from<".$cart->total_without_discount, $delivery->id);
            $deliveries_date = $this->db->results();
            $new_date = date("Y-m-d");
            foreach ($deliveries_date as $delivery_date) {
                $price_day = 0;
                if ($new_date <= $delivery_date->date_sale) {
                    $TWOSD = empty($TotalWithOutSaleBrandsDate[$delivery_date->date_sale]) ? 0 : $TotalWithOutSaleBrandsDate[$delivery_date->date_sale];
                    $TWOSDI = empty($TotalWithOutSaleBrandsDateIsk[$delivery_date->date_sale]) ? 0 : $TotalWithOutSaleBrandsDateIsk[$delivery_date->date_sale];
                    $TSBD = empty($TotalSaleBrandsDate[$delivery_date->date_sale]) ? 0 : $TotalSaleBrandsDate[$delivery_date->date_sale];
                    $discount_day = ($cart->total_without_discount_not_sale - $TotalWithOutSale-$TotalWithOutSaleBrands-$TWOSD) * (1 - (100 - $delivery_date->discount_percent) / 100);
                    $price_day = number_format($cart->total_without_discount - $discount_day - $TotalWithOutSaleBrands - $TWOSD - $TWOSDI + $TotalSaleBrands+$TSBD, 2, ".", ".");
                    $date_sale[$delivery->id][] = array($delivery_date->discount_percent,
                        date( "Y", strtotime($delivery_date->date_sale)),date( "n", strtotime($delivery_date->date_sale)),date( "j", strtotime($delivery_date->date_sale)),
                        $discount_day,$price_day);
                }
            }
        }
        $this->design->assign('date_sale', $date_sale);



     /*   echo"<pre>";
        	echo print_r($deliveries,1);
        echo"</pre>";*/

        $this->design->assign('deliveries', $deliveries);

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

        $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from>'".$cart->total_without_discount."' ORDER by discount_from ASC");
        $NextDiscount = $this->db->result();
        if($NextDiscount){
            $NextDiscountArray = array();
            $NextDiscountArray["procent"] = intval($NextDiscount->discount_percent);
            $NextDiscountArray["sum"] = $NextDiscount->discount_from-$cart->total_without_discount;

            $NextDiscountArray["procent_week"] = intval(20);
            $NextDiscountArray["sum_week"] = 99-$cart->total_without_discount;

            $this->design->assign('nextdiscount', $NextDiscountArray);
        }else{
            $this->design->assign('nextdiscount', '');
        }





        // Если существуют валидные купоны, нужно вывести инпут для купона
        if ($this->coupons->count_coupons(array('valid' => 1)) > 0) {
            //$this->design->assign('coupon_request', true);
        }

        // Выводим корзину
        return $this->design->fetch('cart.tpl');
    }
}
