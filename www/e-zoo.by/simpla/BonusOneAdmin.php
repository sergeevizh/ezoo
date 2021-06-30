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
		$bonus = new stdClass;
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
			$bonus->count_promo = $this->bonus->getcountpromo($bon_id);
			$tmpif = explode(';',$bonus->ifstatus);
			$tr = array();
			$tr2 = array();
			foreach ($tmpif as $val){
				$tr = explode(':',$val);
				$tr2[$tr[0]] = $tr[1];
			}
			$bonus->ifstatus = $tr2;
			
		}
		$this->design->assign('bonus', $bonus);
		/*
		echo "<pre>";
		print_r($bonus);
		echo "</pre>";
		*/
		//Время доставки, города, бренды
		$this->design->assign('time_dilevery', explode(';',$bonus->time_dilevery));
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
		if(isset($_GET['save']))$this->design->assign('message_success', 'saved');

		
		if ($this->request->method('POST')) {	
				if(isset($_POST['delete'])){
					$this->bonus->deleteBonus($bonus);
					header("Location: ".$this->config->root_url.'/simpla/index.php?module=BonusAdmin');
					exit();
				}
					
				/*
				echo "<pre>";
				print_r($_POST);
				print_r($_FILES);
				echo "</pre>";
				*/
				$bonus = new stdClass;
				$bonus->id = $this->request->get('id');
				$bonus->name = $this->request->post('bonus_name');
				$bonus->desc_mini = $this->request->post('bonus_desc_mini');
				$bonus->description = $this->request->post('bonus_description');
				$bonus->summ = $this->request->post('bonus_summ');
				$bonus->percent = $this->request->post('bonus_percent');
				$bonus->status = $this->request->post('bonus_status');
				$bonus->service = $this->request->post('bonus_service');
				if(empty($bonus->status)) $bonus->status = 0;
				$bonus->time_from_sale = $this->request->post('bonus_date_ac_from');
				$bonus->time_to_sale = $this->request->post('bonus_date_ac_to');
				$bonus->time_from = $this->request->post('bonus_date_from');
				$bonus->time_to = $this->request->post('bonus_date_to');
				$bonus->date_order = $this->request->post('bonus_date_order');
				//$bonus->time_dilevery = $this->request->post('bonus_time_dilevery');
				if(!empty($this->request->post('bonus_time_dilevery')))
					$bonus->time_dilevery = implode(';',$this->request->post('bonus_time_dilevery'));
				if(!empty($this->request->post('city')))
					$bonus->cities = implode(';',$this->request->post('city'));
				if(!empty($this->request->post('brend')))
					$bonus->brands = implode(';',$this->request->post('brend'));
				if(isset($_POST['related_products']))
					$bonus->products = implode(';',$this->request->post('related_products'));
				//подготовка массива включений
				$tr = '';
				foreach ($_POST as $key => $stat){
					if(substr_count($key,'st_')>0){
						if(!empty($tr)) $tr.= ';'.$key.':'.$stat;
						else $tr.=$key.':'.$stat;
					}
				}
				$bonus->ifstatus = $tr;
				
				
				//Загрузка фалов
				$host = '/simpla/files/bonus/';	
				$uploaddir = $this->config->root_dir.'simpla/files/bonus/';
				//загрузка файла csv
				$bonus->promokod = array();
				$bonus->csv = $this->request->post('csv');
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
				$bonus->img_preview = NULL; 
				if (!empty($_FILES['img_preview']['tmp_name']) && !empty($_FILES['img_preview']['name'])) {
					$index = rand(0,10000);
                    $attachment_tmp_name = $_FILES['img_preview']['tmp_name'];
					$uploadfile = $index.'_'.basename($_FILES['img_preview']['name']);
                    if(move_uploaded_file($attachment_tmp_name, $uploaddir.$uploadfile))
						$bonus->img_preview = $host.$uploadfile;
                }
				//загрузка картинки img_detail
				$bonus->img_detail = NULL;
				if (!empty($_FILES['img_detail']['tmp_name']) && !empty($_FILES['img_detail']['name'])) {
					$index = rand(0,10000);
                    $attachment_tmp_name = $_FILES['img_detail']['tmp_name'];
					$uploadfile = $index.'_'.basename($_FILES['img_detail']['name']);
                    if(move_uploaded_file($attachment_tmp_name, $uploaddir.$uploadfile))
						$bonus->img_detail = $host.$uploadfile;
                }

				/*
				echo "<pre>";
				print_r($bonus);
				echo "</pre>";
				*/
			
				
				//print_r($bonus);	
			//запись в бд
			
			if(!empty($bonus->id)){//если обновляем
				//update
				$this->bonus->updateSbonuss($bonus);
				$this->bonus->updatebonusConditionss($bonus);
				$this->bonus->updatebonuspromos($bonus);
				
				//подумать с кодами
			}else{// или создаем новый
				//insert
				$idGet = $this->bonus->createBonus($bonus);
				//echo $idGet;
				$this->bonus->updatebonuspromos($bonus);
				header("Location: ".$this->config->root_url.'/simpla/index.php?module=BonusOneAdmin&id='.$idGet.'&save');
				exit();
				
			}
				header("Location: ".$this->config->root_url.'/simpla/index.php?' .$_SERVER['QUERY_STRING'].'&save');
				
            }     

        return $this->design->fetch('bonusOne.tpl');
    }
}
