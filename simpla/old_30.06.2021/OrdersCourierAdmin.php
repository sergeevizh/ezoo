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

class OrdersCourierAdmin extends Simpla
{
    public function fetch()
    {
        $courier = new stdClass;
        if ($this->request->method('POST')) {
            $courier->id = $this->request->post('id', 'integer');
            $courier->name = $this->request->post('name');
            $courier->phone_courier = $this->request->post('phone_courier');
            $courier->start = $this->request->post('start', 'integer');
            if (empty($courier->name)) {
                $this->design->assign('message_error', 'empty_name');
            } elseif (empty($courier->id)) {
                $courier->id = $this->orders->add_courier($courier);
                $courier = $this->orders->get_courier($courier->id);
                $this->design->assign('message_success', 'added');
            } else {
                $this->orders->update_courier($courier->id, $courier);
                $courier = $this->orders->get_courier($courier->id);
                $this->design->assign('message_success', 'updated');
            }
        } else {
            $id = $this->request->get('id', 'integer');
            if (!empty($id)) {
                $courier = $this->orders->get_courier(intval($id));
            }
        }
        $this->design->assign('courier', $courier);
        return $this->design->fetch('orders_courier.tpl');
    }
}
