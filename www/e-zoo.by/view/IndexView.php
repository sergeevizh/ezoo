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

class IndexView extends View
{
    public $modules_dir = 'view/';

    public function __construct()
    {
        parent::__construct();
    }


    public function fetch()
    {
        //стоимость при доставке
        $cart = $this->cart->get_cart();
        // Способы доставки
        $deliveries = $this->delivery->get_deliveries(array('enabled' => 1));
        $selected_city = $this->request->post('city-selected');

        $this->city_select = $this->getHumanNameCity($this->city_select_lat);

        $this->db->query("SELECT * FROM __shipping_city");
        $cities = $this->db->results();

        if (isset($_SESSION['City'])) {

            if (isset($selected_city)){

                $city = $selected_city;


            } else {
                $city = $this->getHumanNameCity($_SESSION['City']);
            }



        } else {

            $city = $this->city_select;

        }

/*        if (!in_array($city, $cities)){
            $city = $this->getRegionCity($city);
        }*/

        $_SESSION['City'] = $this->getLatNameCity($city);




        $this->design->assign('cities', $cities);
        $this->design->assign('city_select', $city);

        /*
        echo $TotalWithOut;
        echo '<br/>'.$cart->total_without_discount_not_sale;*/






        foreach ($deliveries as &$delivery) {


            //Пересчитываем стоимость товаров учитывая исключения и скидки
            $TotalWithOutSale = 0;
            $TotalWithOutSaleBrands = 0;
            $TotalSaleBrands = 0;

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

                //Проверка на индивидуальную скидку по бренду
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
                        if ($ProductOut == 0) {
                            $TotalWithOutSaleBrands += $product->variant->price * $product->amount;
                            $TotalSaleBrands += number_format(($product->variant->price * $product->amount) * ((100 - $brand_percent) / 100), 2, ".", ".");
                            $ProductOut = 1;
                            continue;
                        }
                    }
                }
            }

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

        /* Выводим категорию каждого товара в корзине. Нужно для аналитики сеошникам */
        if ($cart->purchases){
            foreach ($cart->purchases as $purchase){
                $category_id = $this->categories->get_product_categories((int)$purchase->product->id)[0]->category_id;
                $this->db->query("SELECT name FROM __categories WHERE id=?", $category_id);
                $purchase->category = $this->db->result()->name;
            }
        }

        $this->design->assign('deliveries', $deliveries);

        // Содержимое корзины
        $this->design->assign('cart', $cart);

        // Категории товаров
        $this->design->assign('categories', $this->categories->get_categories_tree(array('visible' => 1, 'products_count' => true)));

        /*regions*/
        $this->design->assign('regions', $this->regions->get_regions(array('enabled' => 1)));
        /*/regions*/



        // Страницы
        $pages = $this->pages->get_pages(array('visible' => 1));
        $this->design->assign('pages', $pages);

        // Текущий модуль (для отображения центрального блока)
        $module = $this->request->get('module', 'string');
        $module = preg_replace("/[^A-Za-z0-9]+/", "", $module);

        // Если не задан - берем из настроек
        if (empty($module)) {
            return false;
        }
        //$module = $this->settings->main_module;

        // Создаем соответствующий класс
        if (is_file($this->modules_dir . "$module.php")) {
            include_once($this->modules_dir . "$module.php");
            if (class_exists($module)) {
                $this->main = new $module($this);
            } else {
                return false;
            }
        } else {
            return false;
        }

        // Создаем основной блок страницы
        if (!$content = $this->main->fetch()) {
            return false;
        }

        // Передаем основной блок в шаблон
        $this->design->assign('content', $content);

        // Передаем название модуля в шаблон, это может пригодиться
        $this->design->assign('module', $module);

        // Переменные для вывода кода целей
        if (isset($_SESSION['add_comments_block_form']) && $_SESSION['add_comments_block_form']>=1){
            $add_comments_block_form = 1;
            $this->design->assign('add_comments_block_form', $add_comments_block_form);
            if ($_SESSION['add_comments_block_form']==2) {$_SESSION['add_comments_block_form']=0;}
            else{
                $_SESSION['add_comments_block_form']=2;
            }
        }
        if (isset($_SESSION['registration_form']) && $_SESSION['registration_form']>=1){
            $registration_form = 1;
            $this->design->assign('registration_form', $registration_form);
            if ($_SESSION['registration_form']==2) {$_SESSION['registration_form']=0;}
            else {
                $_SESSION['registration_form']=2;
            }

        }

        $current_url = $_SERVER['REQUEST_URI'];
        // Редирект для удаленных брендов
        $query = $this->db->placehold("SELECT * FROM __deleted_brands WHERE url=?",str_replace('/brands/', '', $current_url));
        $this->db->query($query);
        if ($this->db->result()){
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /brands');
        }

        // Редирект для удаленных товаров
        $query = $this->db->placehold("SELECT * FROM __deleted_products WHERE url=?",str_replace('/products/', '', $current_url));
        $this->db->query($query);
        if ($this->db->result()){
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /products');
        }


        // Редирект для отключенных товаров
        $query = $this->db->placehold("SELECT * FROM __products WHERE visible=0 AND url=?",str_replace('/products/', '', $current_url));
        $this->db->query($query);
        if ($this->db->result()){
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /products');
        }



        // Создаем текущую обертку сайта (обычно index.tpl)
        $wrapper = $this->design->get_var('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        $cities = array();
        foreach ($this->city->get_cities(array('visible' => 1)) as $city) {
            $cities[$city->latin_name] = $city->name;
        }
        $this->design->assign('cities', $cities);

        if (!empty($wrapper)) {
            return $this->body = $this->design->fetch($wrapper);
        } else {
            return $this->body = $content;
        }
    }
}
