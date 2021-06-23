<?php

/**
 * Simpla CMS
 *
 * @copyright    2021
 * @link        
 * @author        
 *
 */

require_once('api/Simpla.php');
require 'vendor/autoload.php';


class BonusOneAdmin extends Simpla
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');
    private $passwd_file;
    private $htaccess_file;

    public function fetch()
    {
        $this->passwd_file = $this->config->root_dir . '/simpla/.passwd';
        $this->htaccess_file = $this->config->root_dir . '/simpla/.htaccess';
        $managers = $this->managers->get_managers();
		$bon_id = $this->request->get('id');
        $this->design->assign('managers', $managers);
        $this->design->assign('promo_count', $this->promo->get_codes_count());
        $this->design->assign('promo_count_second', $this->promo_second->get_codes_count());
        $this->design->assign('delivery_city', $this->city->get_cities());
        $this->design->assign('brands', $this->brands->get_brands());
        $this->design->assign('times', $this->settings->times);
		$bonus = $this->bonus->getBonusbyId($bon_id);
		//статусы условий
		if(!empty($bonus)){
			$tmpif = explode(';',$bonus->ifstatus);
			$tr = array();
			$tr2 = array();
			foreach ($tmpif as $val){
				$tr = explode(':',$val);
				$tr2[$tr[0]] = $tr[1];
			}
			$bonus->ifstatus = $tr2;
			print_r($bonus->ifstatus);
		}
		$this->design->assign('bonus', $bonus);
		$this->design->assign('boncity', explode(';',$bonus->cities));
		$this->design->assign('bonbrands', explode(';',$bonus->brands));
		//формируем продукты
		$related_products = array();
		if (!empty($bonus->products)) {
			$prod_id = explode(';',$bonus->products);
			foreach ($prod_id as $pr){$related_products[]['id'] = $pr;}
			$temp_products = $this->products->get_products(array('id' => $prod_id, 'limit' => count($prod_id)));
			foreach ($temp_products as $key => $temp_product) {
				$related_products[$key]['name'] = $temp_product->name;
			}
			$related_products_images = $this->products->get_images(array('product_id' => $prod_id));
			foreach ($related_products_images as $image) {
				foreach ($related_products as $key => $related_product){
					if($related_product['id'] == $image->product_id )
						$related_products[$key]['imgname'] = $image->filename;
				}
			}
		}
		$this->design->assign('related_products', $related_products);//передаем в шаблон
	

		
		if ($this->request->method('POST')) {	
				$bonus = new stdClass;
				$bonus->name = $this->request->post('bonus_name');
				$bonus->desc_mini = $this->request->post('bonus_preview');
				$bonus->description = $this->request->post('bonus_description');
				$bonus->summ = $this->request->post('bonus_summ');
				$bonus->percent = $this->request->post('bonus_percent');
				$bonus->status = $this->request->post('bonus_status');
				$bonus->time_from_sale = $this->request->post('bonus_date_ac_from');
				$bonus->time_to_sale = $this->request->post('bonus_date_ac_to');
				$bonus->time_from = $this->request->post('bonus_date_from');
				$bonus->time_to = $this->request->post('bonus_date_to');
				$bonus->date_order = $this->request->post('bonus_date_order');
				$bonus->time_dilevery = $this->request->post('bonus_time_dilevery');
				if(!empty($this->request->post('city')))
					$bonus->cities = implode(';',$this->request->post('city'));
				if(!empty($this->request->post('brend')))
					$bonus->brands = implode(';',$this->request->post('brend'));
				if(isset($_POST['related_products']))
					$bonus->products = implode(';',$this->request->post('related_products'));
				
				$host = '/simpla/files/bonus/';	
				$uploaddir = $this->config->root_dir.'simpla/files/bonus/';
				
				//print_r($_POST);
				//загрузка файла csv
				if (!empty($_FILES['csv_file']['tmp_name']) && !empty($_FILES['csv_file']['name'])) {

                    $file_data = fopen($_FILES['csv_file']['tmp_name'], 'r');
                    $column = fgetcsv($file_data,0, ";");
                    while($column = fgetcsv($file_data,0, ";"))
                    {
                        $row_data[] = array(
                            'promo' => $column[0],
                            'active'  => $column[1]
                        );
                    }
					$index = rand(0,10000);
                    $attachment_tmp_name = $_FILES['csv_file']['tmp_name'];
					$uploadfile = $index.'_'.basename($_FILES['csv_file']['name']);
                    if(move_uploaded_file($attachment_tmp_name, $uploaddir.$uploadfile))
						$bonus->csv = $host.$uploadfile;
					else $bonus->csv = NULL;
					$bonus->promokod = $row_data;
                }
				//загрузка картинки img_preview
				if (!empty($_FILES['img_preview']['tmp_name']) && !empty($_FILES['img_preview']['name'])) {
					$index = rand(0,10000);
                    $attachment_tmp_name = $_FILES['img_preview']['tmp_name'];
					$uploadfile = $index.'_'.basename($_FILES['img_preview']['name']);
                    if(move_uploaded_file($attachment_tmp_name, $uploaddir.$uploadfile))
						$bonus->img_preview = $host.$uploadfile;
					else $bonus->img_preview = NULL;
                }
				//загрузка картинки img_detail
				if (!empty($_FILES['img_detail']['tmp_name']) && !empty($_FILES['img_detail']['name'])) {
					$index = rand(0,10000);
                    $attachment_tmp_name = $_FILES['img_detail']['tmp_name'];
					$uploadfile = $index.'_'.basename($_FILES['img_detail']['name']);
                    if(move_uploaded_file($attachment_tmp_name, $uploaddir.$uploadfile))
						$bonus->img_detail = $host.$uploadfile;
					else $bonus->img_detail = NULL;
                }

				echo "<pre>";
				print_r($bonus);
				echo "</pre>";
	
			
				
				//print_r($bonus);	
			//запись в бд
			if(isset($bonus->id)){//если обновляем
				//update
				$this->bonus->updateSbonuss($bonus);
				$this->bonus->updateBbonusConditionss($bonus);
				//update кодов+ условий
				
				//подумать с кодами
			}else{// или создаем новый
				//insert S_bonuss
				//добавить новый объект id к $bonus через select из s_bonuss
				// еще 2 инсерат
			}




				
		}
		/*
        if ($this->request->method('POST')) {
            $parameters = '';

            $this->settings->site_name = $this->request->post('site_name');
            $this->settings->company_name = $this->request->post('company_name');
            $this->settings->date_format = $this->request->post('date_format');
            $this->settings->admin_email = $this->request->post('admin_email');

            $this->settings->order_email = $this->request->post('order_email');
            $this->settings->comment_email = $this->request->post('comment_email');
            $this->settings->notify_from_email = $this->request->post('notify_from_email');

            $this->settings->decimals_point = $this->request->post('decimals_point');
            $this->settings->thousands_separator = $this->request->post('thousands_separator');

            $this->settings->products_num = $this->request->post('products_num');
            $this->settings->products_num_mobi = $this->request->post('products_num_mobi');
            $this->settings->products_num_admin = $this->request->post('products_num_admin');
            $this->settings->max_order_amount = $this->request->post('max_order_amount');
            $this->settings->units = $this->request->post('units');
            $this->settings->mask_title = $this->request->post('mask_title');
            $this->settings->mask_desc = $this->request->post('mask_desc');

            $this->settings->promo_time = $this->request->post('promo_time');
            $this->settings->promo_time_second = $this->request->post('promo_time_second');

            $this->settings->promo_price = $this->request->post('promo_price');
            $this->settings->promo_price_second = $this->request->post('promo_price_second');

            $this->settings->times = $this->request->post('time_opening');

            //Описание для ONLINER.BY
            $this->settings->onliner_descr = $this->request->post('onliner_descr');

            //Сообщение при попытке заказать ветпрепараты
            $this->settings->vetpreparaty = $this->request->post('vetpreparaty');

            //Сообщение при  выборе Другого города
            $this->settings->other_city = $this->request->post('other_city');

            //Отключение дат и времени
            $this->settings->dateandtime = $this->request->post('dateandtime');

            //Приветственное сообщение
            $this->settings->welcome = $this->request->post('welcome');
            $this->settings->hide_welcome = $this->request->post('hide_welcome');

            $this->settings->license_button = $this->request->post('license_button');

            //смс
            $this->settings->mts_login = $this->request->post('mts_login');
            $this->settings->mts_password = $this->request->post('mts_password');
            $this->settings->mts_client_id = $this->request->post('mts_client_id');
            $this->settings->api_unisend_key = $this->request->post('api_unisend_key');
            $this->settings->api_unisend_name = $this->request->post('api_unisend_name');
            $this->settings->mask_sms_1 = $this->request->post('mask_sms_1');
            $this->settings->mask_sms_2_courier = $this->request->post('mask_sms_2_courier');
            $this->settings->mask_sms_2_sklad = $this->request->post('mask_sms_2_sklad');
            $this->settings->mask_sms_2_courier_without_time = $this->request->post('mask_sms_2_courier_without_time');
            $this->settings->mask_sms_promo = $this->request->post('mask_sms_promo');
            $this->settings->mask_sms_promo_second = $this->request->post('mask_sms_promo_second');
            //смс

            // Контакты менеджера (для мобильных)
            $this->settings->manager_contacts_text = $this->request->post('manager_contacts_text');
            $this->settings->manager_contacts_operator_1 = $this->request->post('manager_contacts_operator_1');
            $this->settings->manager_contacts_phone_1 = $this->request->post('manager_contacts_phone_1');
            $this->settings->manager_contacts_operator_2 = $this->request->post('manager_contacts_operator_2');
            $this->settings->manager_contacts_phone_2 = $this->request->post('manager_contacts_phone_2');
            $this->settings->manager_contacts_chat_text = $this->request->post('manager_contacts_chat_text');
            $this->settings->manager_contacts_chat = $this->request->post('manager_contacts_chat');

            // Простые звонки
            $this->settings->pz_server = $this->request->post('pz_server');
            $this->settings->pz_password = $this->request->post('pz_password');
            $this->settings->pz_phones = $this->request->post('pz_phones');

            //текст заказа
            $this->settings->text_top_order = $this->request->post('text_top_order');
            $this->settings->text_bottom_order = $this->request->post('text_bottom_order');

            // Водяной знак
            $clear_image_cache = false;
            $watermark = $this->request->files('watermark_file', 'tmp_name');
            if (!empty($watermark) && in_array(pathinfo($this->request->files('watermark_file', 'name'), PATHINFO_EXTENSION), $this->allowed_image_extentions)) {
                if (@move_uploaded_file($watermark, $this->config->root_dir . $this->config->watermark_file)) {
                    $clear_image_cache = true;
                } else {
                    $this->design->assign('message_error', 'watermark_is_not_writable');
                }
            }

            if ($this->settings->watermark_offset_x != $this->request->post('watermark_offset_x')) {
                $this->settings->watermark_offset_x = $this->request->post('watermark_offset_x');
                $clear_image_cache = true;
            }
            if ($this->settings->watermark_offset_y != $this->request->post('watermark_offset_y')) {
                $this->settings->watermark_offset_y = $this->request->post('watermark_offset_y');
                $clear_image_cache = true;
            }
            if ($this->settings->watermark_transparency != $this->request->post('watermark_transparency')) {
                $this->settings->watermark_transparency = $this->request->post('watermark_transparency');
                $clear_image_cache = true;
            }
            if ($this->settings->images_sharpen != $this->request->post('images_sharpen')) {
                $this->settings->images_sharpen = $this->request->post('images_sharpen');
                $clear_image_cache = true;
            }



            $promo_codes = $this->request->files('codes_file');
            $promo_codes_second = $this->request->files('codes_file_second');
            if ($promo_codes && !empty($promo_codes['tmp_name'])) {
                $allow_types = array('xlsx', 'xls', 'csv');
                $fileType = pathinfo($promo_codes['name'],PATHINFO_EXTENSION);
                $this->promo->clear_codes();
                if (in_array($fileType, $allow_types)) {
                    $excel = PHPExcel_IOFactory::load($promo_codes['tmp_name']);
                    $rows_count = $excel->getActiveSheet()->getHighestRow();
                    for ($i=1; $i <= $rows_count; $i++) {
                        $promo= new stdClass();
                        $promo->code = $excel->getActiveSheet()->getCell('A' . $i)->getValue();
                        $this->promo->add_code($promo);
                    }
                    $parameters .= '&promo=imported';
//                    $this->design->assign('message_success_promo', 'saved');
                }
            } else if ($promo_codes_second && !empty($promo_codes_second['tmp_name'])) {
                $allow_types = array('xlsx', 'xls', 'csv');
                $fileType = pathinfo($promo_codes_second['name'],PATHINFO_EXTENSION);
                $this->promo_second->clear_codes();
                if (in_array($fileType, $allow_types)) {
                    $excel = PHPExcel_IOFactory::load($promo_codes_second['tmp_name']);
                    $rows_count = $excel->getActiveSheet()->getHighestRow();
                    for ($i=1; $i <= $rows_count; $i++) {
                        $promo_second= new stdClass();
                        $promo_second->code = $excel->getActiveSheet()->getCell('A' . $i)->getValue();
                        $this->promo_second->add_code($promo_second);
                    }
                    $parameters .= '&promo_second=imported';
                }
            }


            // Удаление заресайзеных изображений
            if ($clear_image_cache) {
                $dir = $this->config->resized_images_dir;
                if ($handle = opendir($dir)) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            @unlink($dir . "/" . $file);
                        }
                    }
                    closedir($handle);
                }
            }

            // Лимиты товаров
            $limits = $this->request->post('limits');

            if ($limits) {
                foreach ($limits as $limit) {
                    if ($limit['id']){
                        $this->limits->update_limit($limit, $limit['id']);
                    }else {
                        $this->limits->add_limit($limit);
                    }

                };
                header("Location: ".$this->config->root_url.'/simpla/index.php?module=BonusOneAdmin' . $parameters);
            }

            $this->design->assign('message_success', 'saved');
        } else {
            $this->design->assign('limits', $this->limits->get_limits());
            if ($this->request->get('promo') === 'imported') {
                $this->design->assign('message_success_promo', 'success');
            } else if ($this->request->get('promo_second') === 'imported') {
                $this->design->assign('message_success_promo_second', 'success');
            }
        }*/
		
		
        return $this->design->fetch('bonusOne.tpl');
    }
}
