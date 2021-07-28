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

class DeliveryAdmin extends Simpla
{

    public function fetch()
    {
        $delivery = new stdClass;
        $deliveries_discount = array();
        if ($this->request->method('post')) {







            $delivery->id = $this->request->post('id', 'intgeger');
            $delivery->enabled = $this->request->post('enabled', 'boolean');
            $delivery->name = $this->request->post('name');
            $delivery->description = $this->request->post('description');
            $delivery->price = $this->request->post('price');
            $delivery->free_from = $this->request->post('free_from');
            $delivery->separate_payment = $this->request->post('separate_payment');




            //Очищаем все старые данные об ограничениях
            $query = $this->db->placehold('DELETE FROM __delivery_options WHERE delivery_id=?', $delivery->id );
            $this->db->query($query);


            //Добавляем бренды в исключения
            if($this->request->post('brands')) {
                foreach ($this->request->post('brands') as $value) {
                    $Insert["delivery_id"] = $delivery->id;
                    $Insert["type"] = 'brand';
                    $Insert["value"] = $value;
                    $this->db->query("INSERT INTO __delivery_options SET ?%", $Insert);
                }
            }

            //Добавляем категории в исключения
            if($this->request->post('categories')) {
                foreach ($this->request->post('categories') as $value) {
                    $Insert["delivery_id"] = $delivery->id;
                    $Insert["type"] = 'category';
                    $Insert["value"] = $value;
                    $this->db->query("INSERT INTO __delivery_options SET ?%", $Insert);
                }
            }

            //Добавляем товары в исключения
            if($this->request->post('related_products')) {
                foreach ($this->request->post('related_products') as $value) {
                    $Insert["delivery_id"] = $delivery->id;
                    $Insert["type"] = 'product';
                    $Insert["value"] = $value;
                    $this->db->query("INSERT INTO __delivery_options SET ?%", $Insert);
                }
            }




            if ($this->request->post('deliveries_discount')) {
                foreach ($this->request->post('deliveries_discount') as $n => $va) {
                    foreach ($va as $i => $v) {
                        if (empty($deliveries_discount[$i])) {
                            $deliveries_discount[$i] = new stdClass;
                        }
                        $deliveries_discount[$i]->$n = $v;
                    }
                }
            }

            if (!$delivery_payments = $this->request->post('delivery_payments')) {
                $delivery_payments = array();
            }

            if (empty($delivery->name)) {
                $this->design->assign('message_error', 'empty_name');
            } else {
                if (empty($delivery->id)) {
                    $delivery->id = $this->delivery->add_delivery($delivery);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->delivery->update_delivery($delivery->id, $delivery);
                    $this->design->assign('message_success', 'updated');
                }

                if (!empty($delivery->id)) {
                    $this->db->query('DELETE FROM __delivery_discounts WHERE delivery_id=?', $delivery->id);

                    foreach ($deliveries_discount as $delivery_discount) {
                        $delivery_discount->delivery_id = $delivery->id;
                        $this->db->query("INSERT INTO __delivery_discounts SET ?%", $delivery_discount);
                    }

                }


                $this->delivery->update_delivery_payments($delivery->id, $delivery_payments);
            }
        } else {
            $delivery->id = $this->request->get('id', 'integer');
            if (!empty($delivery->id)) {
                $delivery = $this->delivery->get_delivery($delivery->id);
                $this->db->query("SELECT * FROM __delivery_discounts WHERE delivery_id=?", $delivery->id);
                $deliveries_discount = $this->db->results();
            }
            $delivery_payments = $this->delivery->get_delivery_payments($delivery->id);


        }


        //Вывод брендов
        $query = $this->db->placehold("SELECT value FROM __delivery_options WHERE delivery_id=? AND type='brand'", $delivery->id);
        $this->db->query($query);
        $BrandSelected = $this->db->results();

        //Вывод категорий
        $query = $this->db->placehold("SELECT value FROM __delivery_options WHERE delivery_id=? AND type='category'", $delivery->id);
        $this->db->query($query);
        $CategorySelected = $this->db->results();

        //Вывод товаров
        $query = $this->db->placehold("SELECT value FROM __delivery_options WHERE delivery_id=? AND type='product'", $delivery->id);
        $this->db->query($query);
        $ProductSelected = $this->db->results();


        $this->design->assign('brand_selected', (array)$BrandSelected);
        $this->design->assign('category_selected', (array)$CategorySelected);
        $this->design->assign('product_selected', (array)$ProductSelected);



        if (!empty($ProductSelected)) {
            $r_products = array();
            foreach ($ProductSelected as &$r_p) {
                $r_products[$r_p->value] = &$r_p;
            }
            $temp_products = $this->products->get_products(array('id' => array_keys($r_products), 'limit' => count(array_keys($r_products))));



            foreach ($temp_products as $temp_product) {
                $r_products[$temp_product->id] = $temp_product;
            }

            $related_products_images = $this->products->get_images(array('product_id' => array_keys($r_products)));
            foreach ($related_products_images as $image) {
                $r_products[$image->product_id]->images[] = $image;
            }
        }

        $this->design->assign('related_products', $r_products);


        /*   echo"<pre>";
               echo print_r($BrandSelected,1);
               echo print_r($CategorySelected,1);
               echo print_r($ProductSelected,1);
           echo"</pre>";*/





        $this->design->assign('delivery_payments', $delivery_payments);

        // Все способы оплаты
        $payment_methods = $this->payment->get_payment_methods();
        $this->design->assign('payment_methods', $payment_methods);

        $this->design->assign('delivery', $delivery);

        if (empty($deliveries_discount)) {
            $deliveries_discount = array(array(), array());
        }


        $this->design->assign('deliveries_discount', $deliveries_discount);


        // Все бренды
        $brands = $this->brands->get_brands();
        $this->design->assign('brands', $brands);


        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);


        return $this->design->fetch('delivery.tpl');
    }
}
