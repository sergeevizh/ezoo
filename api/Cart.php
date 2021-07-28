<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

/* update 28.08.2020 */
if (defined('IS_CLIENT') == false) {
    define('IS_CLIENT', true);
}
/* update 28.08.2020 */

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
        if (!empty($_SESSION['shopping_cart']) || !empty($_COOKIE['user_id']) || isset($_COOKIE['shopping_cart'])) {
//            $session_items = $_SESSION['shopping_cart'];

            $session_items = array();

            if (isset($_COOKIE['user_id'])) {
                if ($this->get_user_cart($_COOKIE['user_id'])) {

                    $results = $this->get_user_cart($_COOKIE['user_id']);

                    foreach ($results as $result) {

                        $session_items[$result->variant_id]['amount'] = $result->amount;
                        $session_items[$result->variant_id]['check'] = $result->checked;
                    }
                }
            } else {
                $session_items = $_COOKIE['shopping_cart'];
            }

            $variants = $this->variants->get_variants(array('id' => array_keys($session_items)));
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $items[$variant->id] = new stdClass();
                    $items[$variant->id]->variant = $variant;
                    if (is_array($session_items[$variant->id])) {
                        $items[$variant->id]->amount = $session_items[$variant->id]['amount'];
                        $items[$variant->id]->check = $session_items[$variant->id]['check'];
                    } else {
                        $items[$variant->id]->amount = $session_items[$variant->id];
                        $items[$variant->id]->check = 1;
                    }
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

                        if ($item->check) {
                            $purchase->check = $item->check;
                        }
                        $cart->purchases[] = $purchase;

//          TODO (отображение цены и количества товаров в карзине + ветпрепарат)
                        if (!$purchase->product->lecense == 1) {
                            if ($item->check && $item->check != 0) {
                                $cart->total_price += $item->variant->price * $item->amount;// total price = total price + price * кол-во
                                if ($item->variant->compare_price < $item->variant->price) {
                                    $cart->total_without_discount_not_sale += $item->variant->price * $item->amount;
                                }
                                $cart->total_products += $item->amount;  // кол-во товаров в корзине
                            }
                        } else {
                            $cart->total_products += 0;
                            $cart->total_products += $item->amount;  // кол-во товаров в корзине c ветпрепаратами
                        }

                    }
                }
                $cart->total_without_discount = $cart->total_price;
            }
        }
//        var_dump($cart->purchases);

        return $cart;
    }

//    /* update 28.08.2020 */
//    public function update_regions_stocks()
//    {
//
//        //получаем варианты и проверяем на предмет наличия
//        $variants = $this->variants->get_variants(array(
//            'id' => array_keys($_SESSION['shopping_cart']),
//        ));
//
//        // если нет региона - подгружаем основной склад
//        if (!empty($_SESSION['region_id'])) {
//
//            foreach ($variants as $v) {
//                $region_stock = 'region_stock_' . intval($_SESSION['region_id']);
//
//                if ($v->{$region_stock} > 0) {
//                    /*  товар есть в наличии - сохраням и проверям на кол-во: если больше - обрезаем до максимума */
//                    if ($v->{$region_stock} < $_SESSION['shopping_cart'][$v->id]) {
//                        $_SESSION['shopping_cart'][$v->id] = $v->{$region_stock};
//                    }
//
//                } else {
//                    //товара нет в наличии - удаляем из корзины
//                    $this->delete_item($v->id);
//                }
//
//            }
//
//        } else {
//            foreach ($variants as $v) {
//
//                if ($v->stock > 0) {
//                    /*  товар есть в наличии - сохраням и проверям на кол-во: если больше - обрезаем до максимума */
//                    if ($v->stock < $_SESSION['shopping_cart'][$v->id]) {
//                        $_SESSION['shopping_cart'][$v->id] = $v->stock;
//                    }
//
//                } else {
//                    //товара нет в наличии - удаляем из корзины
//                    $this->delete_item($v->id);
//                }
//
//            }
//        }
//
//        return false;
//    }
//    /* update 28.08.2020 */
    /* update 28.08.2020 */
    public function update_regions_stocks()
    {


        //получаем варианты и проверяем на предмет наличия
//        $variants = $this->variants->get_variants(array(
//            'id' => array_keys($_SESSION['shopping_cart']),
//        ));

        if (isset($_COOKIE['user_id'])){
            $cart = $this->get_user_cart($_COOKIE['user_id']);
            if ($cart){
                $variants = array();
                foreach ($cart as $result){
                    array_push($variants, $this->variants->get_variant($result->variant_id));
                }
            }
        } else {
            $variants = $this->variants->get_variants(array(
                'id' => array_keys($_COOKIE['shopping_cart'])
            ));
        }

        // если нет региона - подгружаем основной склад
        if (!empty($_SESSION['region_id'])) {

            foreach ($variants as $v) {
                $region_stock = 'region_stock_' . intval($_SESSION['region_id']);
                if ($v->{$region_stock} > 0) {

                    /*  товар есть в наличии - сохраням и проверям на кол-во: если больше - обрезаем до максимума */
                    if (isset($_COOKIE['user_id']) && !empty($cart)){
                        if($v->{$region_stock} < $this->get_user_cart_variant($_COOKIE['user_id'], $v->id)->amount){
                            $this->update_user_cart($_COOKIE['user_id'], $v->id, $v->{$region_stock});
                        }
                    } else {
                        if ($v->{$region_stock} < $_COOKIE['shopping_cart'][$v->id]){
                            setcookie('shopping_cart[' . $v->id . ']', $v->{$region_stock}, time() + 24 * 60 * 60, "/");
                            $_COOKIE['shopping_cart'][$v->id] = $v->{$region_stock};
                        }
                    }

                } else {
                    $variant = $this->variants->get_variant((int)$v->id);
                    $product = $this->products->get_product((int)$variant->product_id);

                    // Проверяем есть ли дата поставки на этот вариант. Если нет - удаляем из корзины.
                    if ($this->dates->getVariantsSupplyDates(array('variant_id'=>$v->id, 'region_id' => $_SESSION['region_id'])) || $this->dates->getBrandsSupplyDates(array('brand_id' => $product->brand_id, 'region_id' => $_SESSION['region_id']))){

                    } else {
                        //товара нет в наличии - удаляем из корзины
                        $this->delete_item($v->id);
                    }

                }

            }
        } elseif(!empty($variants)) {
            foreach ($variants as $v) {

                if ($v->stock > 0) {
                    /*  товар есть в наличии - сохраням и проверям на кол-во: если больше - обрезаем до максимума */
                    if (isset($_COOKIE['user_id']) && !empty($cart)){
                        if($v->stock < $this->get_user_cart_variant($_COOKIE['user_id'], $v->id)->amount){
                            $this->update_user_cart($_COOKIE['user_id'], $v->id, $v->stock);
                        }
                    } else {
                        if ($v->stock < $_COOKIE['shopping_cart'][$v->id]){
                            setcookie('shopping_cart[' . $v->id . ']', $v->stock, time() + 24 * 60 * 60, "/");
                            $_COOKIE['shopping_cart'][$v->id] = $v->stock;
                        }
                    }
                } else {
                    $variant = $this->variants->get_variant($v->id);
                    $product = $this->products->get_product($variant->product_id);

                    // Проверяем есть ли дата поставки на этот вариант. Если нет - удаляем из корзины.
                    if ($this->dates->getVariantsSupplyDates(array('variant_id'=>$v->id, 'region_id' => 0)) || $this->dates->getBrandsSupplyDates(array('brand_id' => $product->brand_id, 'region_id' => 0))){

                    } else {
                        //товара нет в наличии - удаляем из корзины
                        $this->delete_item($v->id);
                    }
                }

            }
        }

        return false;
    }
    /* update 28.08.2020 */

    /**
     * Добавление варианта товара в корзину
     *
     * @param $variant_id
     * @param int $amount
     */
    public function add_item($variant_id, $amount = 1)
    {
        $amount = max(1, $amount);

        if (isset($_COOKIE['user_id'])) {
            $current_amount = $this->get_user_cart_variant($_COOKIE['user_id'], $variant_id)->amount;
            $amount = max(1, $amount + $current_amount->amount);
        } else {
            if (isset($_COOKIE['shopping_cart'])) {
                $amount = max(1, $amount + $_COOKIE['shopping_cart'][$variant_id]);
            } else {
//                $amount = max(1, $amount + $_SESSION['shopping_cart'][$variant_id]);
            }

        }

        /*Стереть после переноса*/
//        if (isset($_SESSION['shopping_cart'][$variant_id])) {
//            $amount = max(1, $amount + $_SESSION['shopping_cart'][$variant_id]);
//        }

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = $this->variants->get_variant($variant_id);

        // Если товар существует, добавим его в корзину
        if (!empty($variant) /*&& ($variant->stock > 0)*/) {
            if ($variant->stock > 0) {
                // Не дадим больше чем на складе
                $amount = min($amount, $variant->stock);
            }

            /*Стереть после переноса*/
//            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
            if (isset($_COOKIE['user_id'])) {
                if ($this->get_user_cart_variant($_COOKIE['user_id'], $variant_id)) {
                    if ($this->get_user_cart_variant($_COOKIE['user_id'], $variant_id)->check != 0) {
                        $this->update_user_cart($_COOKIE['user_id'], $variant_id, $amount);
                    } else {
                        $this->delete_user_cart($_COOKIE['user_id'], $variant_id);
                        $this->add_user_cart($_COOKIE['user_id'], $variant_id, $amount);
                    }

                } else {
                    $this->add_user_cart($_COOKIE['user_id'], $variant_id, $amount);
                }
            } else {

                setcookie('shopping_cart[' . $variant_id . ']', (int)$amount, time() + 24 * 60 * 60, "/");
                $_COOKIE['shopping_cart'][$variant_id] = $amount;

                /*Стереть после переноса*/
//                $_SESSION['shopping_cart'][$variant_id] = intval($amount);
            }


        }
    }

    public function add_item_array($variant_id, $amount)
    {
        $variant_array = explode(',', $variant_id);
        $amount_array = explode(',', $amount);

        if (isset($_COOKIE['user_id'])) {
            $current_amount = [];
            $amount = [];
            $i = 0;
            foreach($variant_array as $one_item){
                $current_amount[] = $this->get_user_cart_variant($_COOKIE['user_id'], $one_item)->amount;
                $amount[] = max(1, $amount_array[$i] + $current_amount[$i]->amount);
                $i++;
            }
        } else {
            if (isset($_COOKIE['shopping_cart'])) {
                $amount = [];
                $i = 0;
                foreach($variant_array as $one_item){
                    $amount[] = max(1, $amount_array[$i] + $_COOKIE['shopping_cart'][$one_item]);
                    $i++;
                }
            } else {
//                $amount = max(1, $amount + $_SESSION['shopping_cart'][$variant_id]);
            }

        }

        /*Стереть после переноса*/
//        if (isset($_SESSION['shopping_cart'][$variant_id])) {
//            $amount = max(1, $amount + $_SESSION['shopping_cart'][$variant_id]);
//        }

        // Выберем товар из базы, заодно убедившись в его существовании
        $variant = [];
        foreach($variant_array as $one_variant){
            $variant[] = $this->variants->get_variant($one_variant);
        }

        // Если товар существует, добавим его в корзину
        if (!empty($variant) /*&& ($variant->stock > 0)*/) {
            $amount_result = [];
            $i = 0;
            foreach($variant as $one_item){
                if ($one_item->stock > 0) {
                    // Не дадим больше чем на складе
                    $amount_result[] = min($amount[$i], $one_item->stock);
                    $i++;
                }
            }

            /*Стереть после переноса*/
//            $_SESSION['shopping_cart'][$variant_id] = intval($amount);
            if (isset($_COOKIE['user_id'])) {
                $i = 0;
                foreach($variant_array as $one_item){
                    if ($this->get_user_cart_variant($_COOKIE['user_id'], $one_item)) {
                        if ($this->get_user_cart_variant($_COOKIE['user_id'], $one_item)->check != 0) {
                            $this->update_user_cart($_COOKIE['user_id'], $one_item, $amount_result[$i]);
                        } else {
                            $this->delete_user_cart($_COOKIE['user_id'], $one_item);
                            $this->add_user_cart($_COOKIE['user_id'], $one_item, $amount_result[$i]);
                        }
                    } else {
                        $this->add_user_cart($_COOKIE['user_id'], $one_item, $amount_result[$i]);
                    }
                    $i++;
                }
            } else {

                $i = 0;
                foreach($variant_array as $one_item){
                    setcookie('shopping_cart[' . $one_item . ']', (int)$amount_result[$i], time() + 24 * 60 * 60, "/");
                    $_COOKIE['shopping_cart'][$one_item] = $amount_result[$i];
                    $i++;
                }

                /*Стереть после переноса*/
//                $_SESSION['shopping_cart'][$variant_id] = intval($amount);
            }


        }
    }

    /**
     * Получение корзины авторизированного пользователя количества товара
     *
     * @param int $user_id
     */
    public function get_user_cart($user_id)
    {
        $query = $this->db->placehold("SELECT c.variant_id, c.amount, c.checked FROM __cart c WHERE c.user_id=?", $user_id);

        $this->db->query($query);

        $results = $this->db->results();

        return $results;


    }

    public function get_user_cart_variant($user_id, $variant_id)
    {
        $query = $this->db->placehold("SELECT c.amount, c.checked FROM __cart c WHERE c.user_id=? AND c.variant_id=?", $user_id, $variant_id);
        $this->db->query($query);

        $result = $this->db->result();

        return $result;
    }

    /**
     * Обновление товара в корзине пользователя
     *
     * @param int $user_id
     * @param int $variant_id
     * @param int $amount
     */
    public function update_user_cart($user_id, $variant_id, $amount)
    {
        $query = $this->db->placehold("UPDATE __cart c SET c.amount=? WHERE c.variant_id=? AND c.user_id=?", $amount, $variant_id, $user_id);
        $this->db->query($query);
    }

    /**
     * Добавление товара в корзину пользователя
     *
     * @param int $user_id
     * @param int $variant_id
     * @param int $amount
     */
    public function add_user_cart($user_id, $variant_id, $amount)
    {
        $query = $this->db->placehold("INSERT INTO __cart SET user_id=?, amount=?, variant_id=?, checked='1'", $user_id, $amount, $variant_id);
        $this->db->query($query);
    }

    /**
     * Удаление товара из корзины пользователя
     *
     * @param int $user_id
     * @param int $variant_id
     */
    public function delete_user_cart($user_id, $variant_id)
    {
        $query = $this->db->placehold("DELETE FROM __cart WHERE user_id=? AND variant_id=?", $user_id, $variant_id);
        $this->db->query($query);
    }


    /**
     * Добавление истории заказов для незарегистрированных пользователей
     *
     * @param array $variant_id
     */
    public function add_user_history($variant_id)
    {
        setcookie('user_history[' . $variant_id . ']', 1, time() + 24 * 60 * 60, "/");
    }

    /**
     * Очистка истории покупок незарегистрированного пользователя
     */
    public function purge_user_history(){
        foreach ($_COOKIE['user_history'] as $id => $variant){
            setcookie('user_history[' . $id . ']', null, time() - 1, '/');
            unset($_COOKIE['user_history'][$id]);
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
        if (!empty($variant)/* && $variant->stock > 0*/) {
            if ($variant->stock > 0) {
                // Не дадим больше чем на складе
                $amount = min($amount, $variant->stock);
            }


//            $_SESSION['shopping_cart'][$variant_id] = intval($amount);

            if (isset($_COOKIE['user_id'])) {
                if ($this->get_user_cart_variant($_COOKIE['user_id'], $variant_id)) {
                    $this->update_user_cart($_COOKIE['user_id'], $variant_id, $amount);
                } else {
                    $this->add_user_cart($_COOKIE['user_id'], $variant_id, $amount);
                }
            } else {
                setcookie('shopping_cart[' . $variant_id . ']', intval($amount), time() + 24 * 60 * 60, "/");

                /*Удалить после переноса*/
//                $_SESSION['shopping_cart'][$variant_id] = intval($amount);
            }

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
        if (isset($_COOKIE['user_id'])) {
            $this->delete_user_cart($_COOKIE['user_id'], $variant_id);
        } else {
            setcookie('shopping_cart[' . $variant_id . ']', null, time() - 1, '/');
            unset($_SESSION['shopping_cart'][$variant_id]);
            unset($_COOKIE['shopping_cart[' . $variant_id . ']']);
        }
    }

    /**
     * Очистка корзины
     */
    public function empty_cart()
    {
        setcookie('shopping_cart', null, time() - 1, '/');
        unset($_SESSION['shopping_cart']);
        unset($_SESSION['coupon_code']);

        if (isset($_COOKIE['user_id'])) {
            $query = $this->db->placehold('DELETE FROM __cart WHERE user_id=? AND checked=1', intval($_COOKIE['user_id']));
            $this->db->query($query);
        }
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
    public function GetPriceByDelivery($product, $variant)
    {
        $ProductPriceFinal = array('delivery' => array(), 'pickup' => array());

        //Получаем варианты доставок
        $this->db->query("SELECT * FROM __delivery_discounts WHERE discount_percent>0 GROUP by delivery_id");
        $Discounts = $this->db->results();

        foreach ($Discounts as $discount) {


            $ProductExclude = 0;

            //Проверка на исключение товара из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='product' AND value=?", $discount->delivery_id, $product->id);
            if ($this->db->result()) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }

            //Проверка на исключение категорий
            $this->db->query("SELECT * FROM __products_categories WHERE product_id=?", $product->id);
            $Categorys = $this->db->results();
            foreach ($Categorys as $Category) {
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='category' AND value=?", $discount->delivery_id, $Category->category_id);
                if ($this->db->result()) {
                    if ($ProductExclude == 0) {
                        $ProductExclude = 1;
                        continue;
                    }
                }
            }


            //Проверка на исключение бренда из скидки
            if (!is_null($product->brand_id)) {
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=? AND type='brand' AND value=?", $discount->delivery_id, $product->brand_id);
                if ($this->db->result()) {
                    if ($ProductExclude == 0) {
                        $ProductExclude = 1;
                        continue;
                    }
                }
            }
            //проверка на второй товар на скидке
            if ($product->sale_double_item && $discount->delivery_id == 1 && $product->sale_double_item_value != 0) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }
            if ($product->sale_double_item_sam && $discount->delivery_id == 2 && $product->sale_double_item_sam_value != 0) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }

            $brand_percent = 0;
            //Проверка на индивидуальную скидку по товару
            if ($ProductExclude == 0) {
                if (!$variant->compare_price) {
                    $CartPrice = $this->get_cart()->total_without_discount + $variant->price;
                    $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=? AND product_id=? ORDER by discount_percent DESC", $discount->delivery_id, $product->id);
                    $result_product = $this->db->results();
                    if ($result_product) {
                        $brand_percent = 0;
                        foreach ($result_product as $product_row) {
                            if ($product_row->discount_from <= $CartPrice) {
                                $brand_percent = $product_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }

            //Проверка на индивидуальную скидку по бренду
            if ($ProductExclude == 0 && !is_null($product->brand_id)) {
                if (!$variant->compare_price) {
                    $CartPrice = $this->get_cart()->total_without_discount + $variant->price;
                    $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=? AND brands_id=? ORDER by discount_percent DESC", $discount->delivery_id, $product->brand_id);
                    $result_brand = $this->db->results();
                    if ($result_brand) {
                        $brand_percent = 0;
                        foreach ($result_brand as $brand_row) {
                            if ($brand_row->discount_from <= $CartPrice) {
                                $brand_percent = $brand_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }
            //Если товар не исключался
            if ($ProductExclude == 0) {

                //Высчитываем скидку для самовывоза
                if ($discount->delivery_id == 2) {
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1 - (100 - $discount->discount_percent) / 100)), 2, ".", ".");
                }


                //Высчитываем скидку для доставки курьера
                if ($discount->delivery_id == 1) {
                    $CartPrice = $this->get_cart()->total_without_discount + $variant->price;

                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from<=$CartPrice ORDER by discount_percent DESC LIMIT 1");


                    $DiscountDelivery = $this->db->result();


                    $ProductPriceFinal["delivery"] = number_format($variant->price - ($variant->price * (1 - (100 - $DiscountDelivery->discount_percent) / 100)), 2, ".", ".");
                }


            } elseif ($ProductExclude == 2) {
                //Высчитываем скидку для самовывоза
                if ($discount->delivery_id == 2) {
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1 - (100 - $brand_percent) / 100)), 2, ".", ".");
                }


                //Высчитываем скидку для доставки курьера
                if ($discount->delivery_id == 1) {
                    $ProductPriceFinal["delivery"] = number_format($variant->price - ($variant->price * (1 - (100 - $brand_percent) / 100)), 2, ".", ".");
                }
            }


        }
        return $ProductPriceFinal;
    }

//Высчитываем цену для доставки курьером и самовывозом для выгрузки
    public function GetPriceForPiceList($product, $variant)
    {
        $ProductPriceFinal = array('delivery' => array(), 'pickup' => array());

        //Получаем варианты доставок
        $this->db->query("SELECT * FROM __delivery_discounts WHERE discount_percent>0 GROUP by delivery_id");
        $Discounts = $this->db->results();

        foreach ($Discounts as $discount) {

            $ProductExclude = 0;

            //Проверка на исключение товара из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=" . $discount->delivery_id . " AND type='product' AND value=" . $product->id);
            if ($this->db->result()) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }

            //Проверка на исключение категорий
            $this->db->query("SELECT * FROM __products_categories WHERE product_id=" . $product->id);
            $Categorys = $this->db->results();
            foreach ($Categorys as $Category) {
                $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=" . $discount->delivery_id . " AND type='category' AND value=" . $Category->category_id);
                if ($this->db->result()) {
                    if ($ProductExclude == 0) {
                        $ProductExclude = 1;
                        continue;
                    }
                }
            }


            //Проверка на исключение бренда из скидки
            $this->db->query("SELECT * FROM __delivery_options WHERE delivery_id=" . $discount->delivery_id . " AND type='brand' AND value=" . $product->brand_id);
            if ($this->db->result()) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }

            //проверка на второй товар на скидке
            if ($product->sale_double_item && $discount->delivery_id == 1 && $product->sale_double_item_value != 0) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }
            if ($product->sale_double_item_sam && $discount->delivery_id == 2 && $product->sale_double_item_sam_value != 0) {
                if ($ProductExclude == 0) {
                    $ProductExclude = 1;
                    continue;
                }
            }

            $brand_percent = 0;
            //Проверка на индивидуальную скидку по товару
            if ($ProductExclude == 0) {
                if (!$product->variant->compare_price) {
                    $CartPrice = $variant->price;
                    $this->db->query("SELECT * FROM __delivery_products WHERE delivery_id=" . $discount->delivery_id . " AND product_id=" . $product->id . " ORDER by discount_percent DESC");
                    $result_product = $this->db->results();
                    if ($result_product) {
                        $brand_percent = 0;
                        foreach ($result_product as $product_row) {
                            if ($product_row->discount_from <= $CartPrice) {
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
                    $this->db->query("SELECT * FROM __delivery_brands WHERE delivery_id=" . $discount->delivery_id . " AND brands_id=" . $product->brand_id . " ORDER BY discount_percent DESC");
                    $result_brand = $this->db->results();
                    if ($result_brand) {
                        $brand_percent = 0;
                        foreach ($result_brand as $brand_row) {
                            if ($brand_row->discount_from <= $CartPrice) {
                                $brand_percent = $brand_row->discount_percent;
                                break;
                            }
                        }
                        $ProductExclude = 2;
                    }
                }
            }
            //Если товар не исключался
            if ($ProductExclude == 0) {

                //Высчитываем скидку для самовывоза
                if ($discount->delivery_id == 2) {
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1 - (100 - $discount->discount_percent) / 100)), 2, ".", ".");
                }


                //Высчитываем скидку для доставки курьера
                if ($discount->delivery_id == 1) {
                    $CartPrice = $variant->price;

                    $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=1 AND discount_from<=" . $CartPrice . " ORDER by discount_percent DESC LIMIT 1");



                    $DiscountDelivery = $this->db->result();


                    $ProductPriceFinal["delivery"] = number_format($variant->price - ($variant->price * (1 - (100 - $DiscountDelivery->discount_percent) / 100)), 2, ".", ".");
                }


            } elseif ($ProductExclude == 2) {
                //Высчитываем скидку для самовывоза
                if ($discount->delivery_id == 2) {
                    $ProductPriceFinal["pickup"] = number_format($variant->price - ($variant->price * (1 - (100 - $brand_percent) / 100)), 2, ".", ".");
                }


                //Высчитываем скидку для доставки курьера
                if ($discount->delivery_id == 1) {
                    $ProductPriceFinal["delivery"] = number_format($variant->price - ($variant->price * (1 - (100 - $brand_percent) / 100)), 2, ".", ".");
                }
            }


        }
        return $ProductPriceFinal;
    }

}
