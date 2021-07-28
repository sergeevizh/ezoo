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

class CitiesAdmin extends Simpla
{
    public function fetch()
    {
        $cities = $this->city->get_cities();
        $this->design->assign('cities', $cities);

        return $this->design->fetch('cities.tpl');
    }
}
