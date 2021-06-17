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

class ProductView extends View
{

    public function fetch()
    {
        $product_url = $this->request->get('product_url', 'string');

        if (empty($product_url)) {
            return false;
        }

        // Выбираем товар из базы
        $product = $this->products->get_product((string)$product_url);
        if (empty($product) || (!$product->visible && empty($_SESSION['admin']))) {
            return false;
        }

        $product->images = $this->products->get_images(array('product_id' => $product->id));
        $product->image = reset($product->images);
        $product->comments_count = 0;
        $product->price = 0;
        $product->rating = 0;
        $product->variants = array();
        $variants = $this->variants->get_variants(array('product_id' => $product->id));




        $this->db->query("SELECT * FROM __delivery_discounts WHERE discount_percent>0 GROUP by delivery_id");
        $Discounts = $this->db->results();

        foreach ($variants as $v) {




            if (empty($product->price)) {
                $product->price = $v->price;
            }
            if (empty($product->compare_price)) {
                $product->compare_price = $v->compare_price;
            }
            if ($v->stock > 0) {

                if($v->price>$v->compare_price){
                    $v->sale = $this->cart->GetPriceByDelivery($product,$v);
                }

                $product->variants[$v->id] = $v;
            }
        }

        // Вариант по умолчанию
        if (($v_id = $this->request->get('variant', 'integer')) > 0 && isset($variants[$v_id])) {
            $product->variant = $variants[$v_id];
        } else {
            $product->variant = reset($variants);
        }


        $product->features = $this->features->get_product_options(array('product_id' => $product->id));

        // Автозаполнение имени для формы комментария
        if (!empty($this->user)) {
            $this->design->assign('comment_name', $this->user->name);
        }

        // Принимаем комментарий
        if ($this->request->method('post') && $this->request->post('comment')) {
            $secret = '6Lcxgb8UAAAAAM2IlxzkP-AR8xbYP3biSjfpQlS9';
            require_once('recaptcha/autoload.php');
            if (isset($_POST['g-recaptcha-response'])) {
                $recaptcha = new \ReCaptcha\ReCaptcha($secret);
                $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            }
            if ($resp->isSuccess()){
                $comment = new stdClass;
            $comment->name = $this->request->post('name');
            $comment->text = $this->request->post('text');
            $comment->rating = $this->request->post('rating', 'integer');
            //$captcha_code =  $this->request->post('captcha_code', 'string');

            // от умелых рук которые умеют изменять DOM на лету
            if ($comment->rating > 5) {
                $comment->rating = 5;
            } elseif ($comment->rating < 1) {
                $comment->rating = null;
            }

            // Передадим комментарий обратно в шаблон - при ошибке нужно будет заполнить форму
            $this->design->assign('comment_text', $comment->text);
            $this->design->assign('comment_name', $comment->name);
            $this->design->assign('comment_rating', $comment->rating);

            // Проверяем капчу и заполнение формы
            /*            if ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code)) {
                            $this->design->assign('error', 'captcha');
                        } else*/
            if (empty($comment->name)) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($comment->text)) {
                $this->design->assign('error', 'empty_comment');
            } else {
                // Создаем комментарий
                $comment->object_id = $product->id;
                $comment->type = 'product';
                $comment->ip = $_SERVER['REMOTE_ADDR'];
                // Если были одобренные комментарии от текущего ip, одобряем сразу
                $this->db->query("SELECT 1 FROM __comments WHERE approved=1 AND ip=? LIMIT 1", $comment->ip);
                if ($this->db->num_rows() > 0) {
                    $comment->approved = 1;
                }

                // Добавляем комментарий в базу
                $comment_id = $this->comments->add_comment($comment);

                // Отправляем email
                $this->notify->email_comment_admin($comment_id);

                // Приберем сохраненную капчу, иначе можно отключить загрузку рисунков и постить старую
                unset($_SESSION['captcha_code']);
                header('location: ' . $_SERVER['REQUEST_URI'] . '#comment_' . $comment_id);
            }
        }
        }

        // Отзывы о товаре
        $comments = $this->comments->get_comments(array('type' => 'product', 'object_id' => $product->id, 'approved' => 1, 'ip' => $_SERVER['REMOTE_ADDR']));
        $ratings = array();
        foreach ($comments as $com) {
            if(!empty($com->rating)) {
                $ratings[] = $com->rating;
            }

            $product->rating = (float)(array_sum( $ratings) / count($ratings));
        }


        $product->categories = $this->categories->get_categories(array('product_id' => $product->id));
        $product->category = reset($product->categories);

        // Связанные товары
        $related_products = array();
        $related_ids = array();
        foreach ($this->products->get_related_products($product->id) as $p) {
            $related_ids[] = $p->related_id;
        }
        if (!empty($related_ids)) {
            $related_products = $this->products->renders(array('id' => $related_ids, 'limit' => count($related_ids), 'in_stock' => 1, 'visible' => 1));
        }

        if( count( $related_products) < 8 && !empty($product->category)){

            $this->db->query('SELECT related_id FROM __related_categories WHERE category_id=? ORDER BY position', $product->category->id);
            $categories_ids = $this->db->results('related_id');
            if(!empty($categories_ids)){
                $_related_products = $this->products->renders(array('sort'=>'rand','not_id' => array_values($related_products), 'category_id'=>$categories_ids, 'limit' => 8-count($related_products), 'in_stock' => 1, 'visible' => 1));

                $related_products = array_merge($_related_products, $related_products);
            }


        }

        $this->design->assign('related_products', $related_products);

        // Соседние товары
        $this->design->assign('next_product', $this->products->get_next_product($product->id));
        $this->design->assign('prev_product', $this->products->get_prev_product($product->id));

        // И передаем его в шаблон
        $this->design->assign('product', $product);
        $this->design->assign('comments', $comments);

        // Категория и бренд товара

        $this->design->assign('brand', $this->brands->get_brand(intval($product->brand_id)));
        $this->design->assign('category',  $product->category);
		
//		 if ($product->brand_id == 286001) {
//			$product->compare_price = 0;
//			$v->compare_price = 0;
             //   $product->compare_price = 99;
//		 }
//		 else exit;

        // Добавление в историю просмотров товаров
        $max_visited_products = 100; // Максимальное число хранимых товаров в истории
        $expire = time() + 60 * 60 * 24 * 30; // Время жизни - 30 дней
        if (!empty($_COOKIE['browsed_products'])) {
            $browsed_products = explode(',', $_COOKIE['browsed_products']);
            // Удалим текущий товар, если он был
            if (($exists = array_search($product->id, $browsed_products)) !== false) {
                unset($browsed_products[$exists]);
            }
        }
        // Добавим текущий товар
        $browsed_products[] = $product->id;
        $cookie_val = implode(',', array_slice($browsed_products, -$max_visited_products, $max_visited_products));
        setcookie("browsed_products", $cookie_val, $expire, "/");

        $this->design->assign('meta_title', $product->meta_title);
        $this->design->assign('meta_keywords', $product->meta_keywords);
        $this->design->assign('meta_description', $product->meta_description);

        return $this->design->fetch('product.tpl');
    }
}
