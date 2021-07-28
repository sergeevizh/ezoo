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

class UserOrdersView extends View
{
    public function fetch()
    {
        if (empty($this->user)) {
            header('Location: ' . $this->config->root_url . '/user/login');
            exit();
        }
        $count_orders = $this->orders->count_orders(array('user_id' => $this->user->id));
        $this->design->assign('count_orders', $count_orders);

        $orders = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => $count_orders));
        $this->design->assign('orders', $orders);

        return $this->design->fetch('user_orders.tpl');
    }
}



