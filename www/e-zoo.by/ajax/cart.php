<?php
session_start();
require_once('../api/Simpla.php');

class CartAjax extends Simpla
{
    public function fetch()
    {
        $this->cart->add_item($this->request->get('variant', 'integer'), $this->request->get('amount', 'integer'));
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
                $product->sale_double_item =  $item->product->sale_double_item;
                $product->sale_double_item_value = $item->product->sale_double_item_value;
                $product->sale_double_item_sam =  $item->product->sale_double_item_sam;
                $product->sale_double_item_sam_value = $item->product->sale_double_item_sam_value;

                if (!$item->check || $item->check == 0) {
                    $ProductOut = 1;
                    continue;
                }

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

                //Проверка скидки на второй товар
                if ($ProductOut == 0) {
                    if (!$product->variant->compare_price) {
                        if ($product->sale_double_item && $product->sale_double_item_value!=0 && $delivery->id == 1) {
                            $product_percent = $product->sale_double_item_value;
                            $countProductSale = ($product->amount - $product->amount % 2) / 2;
                            $countProductNotSale = $product->amount - $countProductSale;
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $TotalSaleBrands += number_format(($product->variant->price * $countProductSale) * ((100 - $product_percent) / 100), 2, ".", ".");
                            $TotalSaleBrands += $countProductNotSale * $product->variant->price;
                            $ProductOut = 1;
                            continue;
                        }
                        if ($product->sale_double_item_sam && $product->sale_double_item_sam_value!=0 && $delivery->id == 2) {
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

							//DENIS R
							//$TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent) / 100), 2, ".", ".");
                            $TotalSaleBrands += number_format((round($product->variant->price * ((100 - $brand_percent) / 100),2) * $product->amount) , 2, ".", "");


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
            $delivery->total_price = $cart->total_without_discount - $delivery->discount_for_order-$TotalWithOutSaleBrands+$TotalSaleBrands;
            $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
            foreach ($this->db->results() as $row) {
                if ($cart->total_price >= $row->discount_from && $delivery->discount_percent < $row->discount_percent) {
                    $delivery->discount_percent = intval($row->discount_percent);
                    $delivery->discount_price = ($cart->total_without_discount_not_sale-$TotalWithOutSale-$TotalWithOutSaleBrands) * (1-(100 - $row->discount_percent) / 100);
                    $delivery->discount_for_order = ($cart->total_without_discount-$cart->total_price)+$delivery->discount_price;
                    $delivery->total_price = $cart->total_without_discount - $delivery->discount_for_order-$TotalWithOutSaleBrands+$TotalSaleBrands;
                }
            }
        }

        /*   echo"<pre>";
               echo print_r($deliveries,1);
           echo"</pre>";*/
		//бонус
		if(isset($_COOKIE['bonus']) && !empty($_COOKIE['bonus']) && isset($_COOKIE['percent']) && !empty($_COOKIE['percent']) && $_COOKIE['percent']>0){
			$deliveries[0]->bonus_sale = $deliveries[0]->total_price * $_COOKIE['percent'] / 100;
			$deliveries[0]->bonus_price = $deliveries[0]->total_price - ($deliveries[0]->total_price * $_COOKIE['percent'] / 100);
			$cart->bonus_price = $deliveries[0]->bonus_price;
		   }
		//$deliveries[0]->total_price = $deliveries[0]->bonus_price;
		//$cart->total_price = $deliveries[0]->bonus_price;
		
        $this->design->assign('deliveries', $deliveries);
        $this->design->assign('cart', $cart);

        $currencies = $this->money->get_currencies(array('enabled' => 1));
        if (isset($_SESSION['currency_id'])) {
            $currency = $this->money->get_currency($_SESSION['currency_id']);
        } else {
            $currency = reset($currencies);
        }

        $this->design->assign('currency', $currency);

        return $this->design->fetch('cart_informer.tpl');
    }
}

$cart_ajax = new CartAjax();
$result = $cart_ajax->fetch();

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("X-Robots-Tag: noindex");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($result);
exit;
