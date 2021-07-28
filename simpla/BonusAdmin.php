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

use PHPExcel;
use PHPExcel_IOFactory;

class BonusAdmin extends Simpla
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
        $this->design->assign('managers', $managers);
        $this->design->assign('promo_count', $this->promo->get_codes_count());
        $this->design->assign('promo_count_second', $this->promo_second->get_codes_count());
        $this->design->assign('promo_count_second', $this->promo_second->get_codes_count());
        //$this->design->assign('delivery_city', $this->city->get_cities());
		
		$bonus->cities = $this->city->get_cities();
		$bonus->bonuses = $this->bonus->getNameIdBonuses();
		
		$this->design->assign('bonusall', $bonus->bonuses);
		$this->design->assign('bonus_count', count($bonus->bonuses));

        return $this->design->fetch('bonus.tpl');
    }
}
