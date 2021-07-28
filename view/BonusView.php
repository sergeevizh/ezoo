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

class BonusView extends View
{
    public function fetch()
    {

        $url = $this->request->get('bonus_url', 'string');

        $page = $this->pages->get_page($url);

        // Отображать скрытые страницы только админу
        if (empty($page) || (!$page->visible && empty($_SESSION['admin']))) {
            return false;
        }
		$page->url = 'bonus';


		$page->header = 'Бонусы и подарки от Ezoo';
		$page->meta_title .= 'Бонусы и подарки от Ezoo';

		//формируем бонусы для всех
		//$bonus_all = Bonus::getBonusbyStatus(1);
		$bonus_all = $this->bonus->getBonusbyStatus(1);
        //print_r($bonus_all);
		
		//$bonus_my = Bonus::getbonusbyUser($this->user->id_bonus);
		$bonus_my = $this->bonus->getbonusbyUser($this->user->id_bonus,$this->user->id);
		$page->body = '<p style="">Мы решили поднять вам настроение и по этому дарим вам крутыe бонусы,зарабатывайте бонусы выполняя условия их получения</p>';

		
		
		$ret = array();
		foreach ($bonus_all as $mas){
			if(substr_count($mas->ifstatus,'st_sale')==0){
				
				$ret[] = $mas;
			}elseif(!empty($mas) && strtotime($mas->time_to_sale) >= time())
				$ret[] = $mas;
		}
		$bonus_all = $ret;
		
		//верстка
		$i=1;
		foreach($bonus_all as $key=>$bonus){
			if(empty($bonus->img_preview)){
				$bonus_all[$key]->img_preview = '/simpla/files/nophoto.jpg';		
			}
			if(empty($bonus->img_detail)){
				$bonus_all[$key]->img_detail = '/simpla/files/nophoto.jpg';		
			}
			if($i==5) $i=0;
			if($i==2 || $i==3)
				$bonus_all[$key]->verst = 2;
			else $bonus_all[$key]->verst = 1;
			if($i==4) $i=0;
			$i++;
		}
		$i=1;
		if(!empty($bonus_my )){
			foreach($bonus_my as $key=>$bonus){
				if(empty($bonus->img_preview)){
					$bonus_my[$key]->img_preview = '/simpla/files/nophoto.jpg';		
				}
				if(empty($bonus->img_detail)){
					$bonus_my[$key]->img_detail = '/simpla/files/nophoto.jpg';		
				}
				if($i==5) $i=0;
				if($i==2 || $i==3)
					$bonus_my[$key]->verst = 2;
				else $bonus_my[$key]->verst = 1;
				if($i==4) $i=0;
				$i++;
			}
		}
		/*
		echo '<pre>';
		print_r($bonus_my);
		//print_r($bonus_all);
		//print_r($this->user);
		echo '</pre>';
		*/
        $this->design->assign('page', $page);
        $this->design->assign('bonus_all', $bonus_all);
        $this->design->assign('bonus_my', $bonus_my);
        $this->design->assign('count_bonus', count($bonus_all));
        $this->design->assign('count_bonus_my', count($bonus_my));
        $this->design->assign('meta_title', $page->meta_title);
        $this->design->assign('meta_keywords', $page->meta_keywords);
        $this->design->assign('meta_description', $page->meta_description);

        return $this->design->fetch('bonus.tpl');
    }
}
