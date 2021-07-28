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

        $this->design->assign('deliveries', $deliveries);

        // Содержимое корзины
        $this->design->assign('cart', $cart);

        // Категории товаров
        $this->design->assign('categories', $this->categories->get_categories_tree(array('visible' => 1, 'products_count' => true)));

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
        if ($_SESSION['add_comments_block_form']>=1){
            $add_comments_block_form = 1;
            $this->design->assign('add_comments_block_form', $add_comments_block_form);
            if ($_SESSION['add_comments_block_form']==2) {$_SESSION['add_comments_block_form']=0;}
            else{
                $_SESSION['add_comments_block_form']=2;
            }
        }
        if ($_SESSION['registration_form']>=1){
            $registration_form = 1;
            $this->design->assign('registration_form', $registration_form);
            if ($_SESSION['registration_form']==2) {$_SESSION['registration_form']=0;}
            else {
                $_SESSION['registration_form']=2;
            }

        }
        // Создаем текущую обертку сайта (обычно index.tpl)
        $wrapper = $this->design->get_var('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        if (!empty($wrapper)) {
            return $this->body = $this->design->fetch($wrapper);
        } else {
            return $this->body = $content;
        }
    }
}
