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


class OrdersDeliveryArea extends Simpla
{

    public function fetch()
    {

        if ($this->request->method('post')) {

            $polygons = $this->request->post('polygons');
            $polygons_ids = [];

            if ($polygons) {
                $existing_polygons = $this->areas->get_areas();

                foreach ($polygons as $polygon) {
                    if ($polygon['id']) {
                        $this->areas->edit_area($polygon['id'], $polygon['area']);
                        $polygons_ids[] = $polygon['id'];
                    } else {
                        $polygons_ids[] = $this->areas->add_area($polygon['area']);
                    }
                }

                if ($existing_polygons) {
                    foreach ($existing_polygons as $existing_polygon) {
                        if (!in_array($existing_polygon->id, $polygons_ids)) {
                            $this->areas->delete_area_courier($existing_polygon->id);
                            $this->areas->delete_area($existing_polygon->id);
                        }
                    }
                }
            }

            $couriers = $this->request->post('couriers');

            if ($couriers) {
                foreach ($couriers as $area_id => $courier_id) {
                    if (!$courier_id) {
                        $this->areas->delete_area_courier($area_id);
                    } else {
                        if ($this->areas->get_area_courier($area_id)) {
                            $this->areas->edit_area_courier($area_id, $courier_id);
                        }
                        else {
                            $this->areas->set_area_courier($courier_id, $area_id);
                        }
                    }
                }
            }
        }

        $managers = $this->managers->get_managers();
        $this->design->assign('managers', $managers);

        $delivery_areas = $this->areas->get_areas();
        $this->design->assign('delivery_areas', $delivery_areas);
        $this->design->assign('polygons', json_encode($delivery_areas));
        $this->design->assign('couriers', $this->orders->get_couriers());

        return $this->design->fetch('delivery_area.tpl');

    }
}
