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

class OrdersCouriersAdmin extends Simpla
{

    public function fetch()
    {
        // Обработка действий
        if ($this->request->method('post')) {
            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            sort($positions);
            foreach ($positions as $i => $position) {
                $this->orders->update_courier($ids[$i], array('position' => $position));
            }

            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete': {
                        foreach ($ids as $id) {
                            $this->orders->delete_courier($id);
                        }
                        break;
                    }
                }
            }
        }

        // Отображение
        $couriers = $this->orders->get_couriers();

        $this->design->assign('couriers', $couriers);
        return $this->design->fetch('orders_couriers.tpl');
    }
}
