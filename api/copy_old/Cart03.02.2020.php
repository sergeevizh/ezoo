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




class Cart extends Simpla
{

    /**
     * Функция возвращает корзину
     *
     * @return stdClass
     */
    public function get_cart()
    {
        $cart = new stdClass();
        $cart->purchases = array();
        $cart->total_price = 0;
        $cart->total_without_discount = 0;
        $cart->total_without_discount_not_sale = 0;
        $cart->total_products = 0;
        $cart->coupon = null;
        $cart->discount = 0;
        $cart->coupon_discount = 0;
		$delivery = 0;

        // Берем из сессии список variant_id=>amount
        if (!empty($_SESSION['shopping_cart'])) {
            $session_items = $_SESSION['shopping_cart'];

            $variants = $this->variants->get_variants(array('id' => array_keys($session_items)));
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $items[$variant->id] = new stdClass();
                    $items[$variant->id]->variant = $variant;
                    $items[$variant->id]->amount = $session_items[$variant->id];
                    $products_ids[] = $variant->product_id;
                }

                $products = array();
                foreach ($this->products->get_products(array(
                    'id' => $products_ids,
                    'limit' => count($products_ids)
                )) as $p) {
                    $products[$p->id] = $p;
                }

                $images = $this->products->get_images(array('product_id' => $products_ids));
                foreach ($images as $image) {
                    $products[$image->product_id]->images[$image->id] = $image;
                }


                foreach ($items as $variant_id => $item) {
                    $purchase = null;
                    if (!empty($products[$item->variant->product_id])) {
                        $purchase = new stdClass();
                        $purchase->product = $products[$item->variant->product_id];
                        $purchase->variant = $item->variant;
                        $purchase->amount = $item->amount;

                        $cart->purchases[] = $purchase;
                        $cart->total_price += $item->variant->price * $item->amount; // total price = total price + price * кол-во

			if ($item->variant->compare_price < $item->variant->price)
					{
                          $cart->total_without_discount_not_sale += $item->variant->price * $item->amount;
                      }



                        $cart->total_products += $item->amount;  // кол-во товаров в корзине
                    }
                }

                $cart->total_without_discount = $cart->total_price;

            }
        }

        return $cart;
    }

    /**
     * Добавление варианта товара в корзину
     *
     * @param $variant_id
     * @param int $amount
     */
    public function add_item($variant_id, $amount = 1)
    {
        $amount = max(1, $amount);

        if (isset($_SESSION['shopping_cart'][$variant_id])) {
            $amount = max(1, $amount + $_SESSION['shopping_cart'][$variant_id]);
        }

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = $this->variants->get_variant($variant_id);

        // Если товар существует, добавим его в корзину
        if (!empty($variant) && ($variant->stock > 0)) {
            // Не дадим больше чем на складе
            $amount = min($amount, $variant->stock);

            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
        }
    }

    /**
     * Обновление количества товара
     *
     * @param $variant_id
     * @param int $amount
     */
    public function update_item($variant_id, $amount = 1)
    {
        $amount = max(1, $amount);

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = $this->variants->get_variant($variant_id);

        // Если товар существует, добавим его в корзину
        if (!empty($variant) && $variant->stock > 0) {
            // Не дадим больше чем на складе
            $amount = min($amount, $variant->stock);

            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
        }
    }


    /**
     * Удаление товара из корзины
     *
     * @param $variant_id
     */
    public function delete_item($variant_id)
    {
        unset($_SESSION['shopping_cart'][$variant_id]);
    }

    /**
     * Очистка корзины
     */
    public function empty_cart()
    {
        unset($_SESSION['shopping_cart']);
        unset($_SESSION['coupon_code']);
    }

    /**
     * Применить купон
     *
     * @param $coupon_code
     */
    public function apply_coupon($coupon_code)
    {
        $coupon = $this->coupons->get_coupon((string)$coupon_code);
        if ($coupon && $coupon->valid) {
            $_SESSION['coupon_code'] = $coupon->code;
        } else {
            unset($_SESSION['coupon_code']);
        }
    }


    //Высчитываем цену для доставки курьером и самовывозом
    public function GetPriceByDelivery($product,$variant){
        $ProductPriceFinal = array('delivery'=>array(),'pickup'=>array());

        //Получаем варианты доставок
        $this->db->query("SELECT * FROM __delivery_discounts WHERE discount_percent>0 GROUP by delivery_id");
        $Discounts = $this->db->results();

        foreach($Discounts as $discount){



            $ProductExclude = 0;

            //Проверка на исключение товара из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='product' AND value=?", $discount->delivery_id,$product->id);
            if($this->db->result()){
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            //Проверка на исключение категорий
            $this->db->query("SELECT * FROM __products_categories WHERE product_id=?", $product->id);
            $Categorys = $this->db->results();
            foreach ($Categorys as $Category) {
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='category' AND value=?", $discount->delivery_id,$Category->category_id);
                if($this->db->result()){
                    if($ProductExclude==0){
                        $ProductExclude=1;
                        continue;
                    }
                }
            }


            //Проверка на исключение бренда из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='brand' AND value=?",  $discount->delivery_id,$product->brand_id);
            if($this->db->result()){
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            //проверка на второй товар на скидке
            if ($product->sale_double_item && $discount->delivery_id==1 && $product->sale_double_item_value!=0) {
                if($ProductExclude==0){
                $ProductExclude=1;
                continue;
            }
            }
            if ($product->sale_double_item_sam && $discount->delivery_id==2 && $product->sale_double_item_sam_value!=0) {
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            $brand_percent = 0;
            //Проверка на индивидуальную скидку по товару
            if ($ProductExclude == 0) {
                if (!$product->variant->compare_price) {
                    $CartPrice = $this->get_cart()->total_without_discount+$variant->price;
                    $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=? AND product_id=? ORDER by discount_percent DESC", $discount->delivery_id, $product->id);
                    $result_product = $this->db->results();
                    if ($result_product) {
                        $brand_percent = 0;
                        foreach ($result_product as $product_row) {
                            if ($product_row->discount_from<=$CartPrice){
                                $brand_percent = $product_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }

            //Проверка на индивидуальную скидку по бренду
            if ($ProductExclude == 0) {
                    if (!$variant->compare_price) {
                        $CartPrice = $this->get_cart()->total_without_discount+$variant->price;
                        $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=? AND brands_id=? ORDER by discount_percent DESC", $discount->delivery_id, $product->brand_id);
                        $result_brand = $this->db->results();
                        if ($result_brand) {
                            $brand_percent = 0;
                            foreach ($result_brand as $brand_row) {
                                if ($brand_row->discount_from<=$CartPrice){
                                    $brand_percent = $brand_row->discount_percent;
                                    break;
                                }
                            }
                                $ProductExclude = 2;
                        }
                    }
                }
            //Если товар не исключался
            if($ProductExclude==0){

                //Высчитываем скидку для самовывоза
                if($discount->delivery_id==2){
                    $ProductPriceFinal["pickup"] =  number_format($variant->price - ($variant->price * (1-(100 - $discount->discount_percent) / 100 )),2,".",".");
                }


                //Высчитываем скидку для доставки курьера
                if($discount->delivery_id==1){
                    $CartPrice = $this->get_cart()->total_without_discount+$variant->price;

                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from<=$CartPrice ORDER by discount_percent DESC LIMIT 1");



                    $DiscountDelivery = $this->db->result();





                    $ProductPriceFinal["delivery"] =  number_format($variant->price - ($variant->price * (1-(100 - $DiscountDelivery->discount_percent) / 100 )),2,".",".");
                }



            }
            elseif($ProductExclude == 2){
                //Высчитываем скидку для самовывоза
                if($discount->delivery_id==2){
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1-(100 - $brand_percent) / 100 )),2,".",".");
                }


                //Высчитываем скидку для доставки курьера
                if($discount->delivery_id==1){
                    $ProductPriceFinal["delivery"] =  number_format($variant->price - ($variant->price * (1-(100 - $brand_percent) / 100 )),2,".",".");
                }
            }


        }
        return $ProductPriceFinal;
    }

//Высчитываем цену для доставки курьером и самовывозом для выгрузки
    public function GetPriceForPiceList($product,$variant){
        $ProductPriceFinal = array('delivery'=>array(),'pickup'=>array());

        //Получаем варианты доставок
        $this->db->query("SELECT * FROM __delivery_discounts WHERE discount_percent>0 GROUP by delivery_id");
        $Discounts = $this->db->results();

        foreach($Discounts as $discount){

            $ProductExclude = 0;

            //Проверка на исключение товара из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=".$discount->delivery_id." AND type='product' AND value=".$product->id);
            if($this->db->result()){
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            //Проверка на исключение категорий
            $this->db->query("SELECT * FROM __products_categories WHERE product_id=".$product->id);
            $Categorys = $this->db->results();
            foreach ($Categorys as $Category) {
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=".$discount->delivery_id." AND type='category' AND value=".$Category->category_id);
                if($this->db->result()){
                    if($ProductExclude==0){
                        $ProductExclude=1;
                        continue;
                    }
                }
            }


            //Проверка на исключение бренда из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=".$discount->delivery_id." AND type='brand' AND value=".$product->brand_id);
            if($this->db->result()){
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            //проверка на второй товар на скидке
            if ($product->sale_double_item && $discount->delivery_id==1 && $product->sale_double_item_value!=0) {
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }
            if ($product->sale_double_item_sam && $discount->delivery_id==2 && $product->sale_double_item_sam_value!=0) {
                if($ProductExclude==0){
                    $ProductExclude=1;
                    continue;
                }
            }

            $brand_percent = 0;
            //Проверка на индивидуальную скидку по товару
            if ($ProductExclude == 0) {
                if (!$product->variant->compare_price) {
                    $CartPrice = $variant->price;
                    $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=".$discount->delivery_id." AND product_id=".$product->id." ORDER by discount_percent DESC");
                    $result_product = $this->db->results();
                    if ($result_product) {
                        $brand_percent = 0;
                        foreach ($result_product as $product_row) {
                            if ($product_row->discount_from<=$CartPrice){
                                $brand_percent = $product_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }

            //Проверка на индивидуальную скидку по бренду
            if ($ProductExclude == 0) {
                if (!$variant->compare_price) {
                    $CartPrice = $variant->price;
                    $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=".$discount->delivery_id." AND brands_id=".$product->brand_id." ORDER BY discount_percent DESC");
                    $result_brand = $this->db->results();
                    if ($result_brand) {
                        $brand_percent = 0;
                        foreach ($result_brand as $brand_row) {
                            if ($brand_row->discount_from<=$CartPrice){
                                $brand_percent = $brand_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }
            //Если товар не исключался
            if($ProductExclude==0){

                //Высчитываем скидку для самовывоза
                if($discount->delivery_id==2){
                    $ProductPriceFinal["pickup"] =  number_format($variant->price - ($variant->price * (1-(100 - $discount->discount_percent) / 100 )),2,".",".");
                }


                //Высчитываем скидку для доставки курьера
                if($discount->delivery_id==1){
                    $CartPrice = $variant->price;

                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from<=".$CartPrice." ORDER by discount_percent DESC LIMIT 1");



                    $DiscountDelivery = $this->db->result();





                    $ProductPriceFinal["delivery"] =  number_format($variant->price - ($variant->price * (1-(100 - $DiscountDelivery->discount_percent) / 100 )),2,".",".");
                }



            }
            elseif($ProductExclude == 2){
                //Высчитываем скидку для самовывоза
                if($discount->delivery_id==2){
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1-(100 - $brand_percent) / 100 )),2,".",".");
                }


                //Высчитываем скидку для доставки курьера
                if($discount->delivery_id==1){
                    $ProductPriceFinal["delivery"] =  number_format($variant->price - ($variant->price * (1-(100 - $brand_percent) / 100 )),2,".",".");
                }
            }


        }
        return $ProductPriceFinal;
    }

}
