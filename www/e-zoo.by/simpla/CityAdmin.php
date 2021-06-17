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

class CityAdmin extends Simpla
{

    public function fetch()
    {
        if ($this->request->method('post')) {
            $city = new stdClass();
            $city_areas = array();
            if ($this->request->post('city_areas')) {
                foreach ($this->request->post('city_areas') as $n => $va) {
                    foreach ($va as $i => $v) {
                        if (empty($city_areas[$i])) {
                            $city_areas[$i] = new stdClass;
                        }
                        $city_areas[$i]->$n = $v;
                    }
                }
            }
            if ($this->request->post('city_id') != "") {
                $id = $this->request->post('city_id','integer');
                if ($this->request->post('save')){
                    if ($this->request->post('name') != "") {
                        $city->id = $id;
                        $city->name = $this->request->post('name');
                        $city->region_id = $this->request->post('region_id');
                        $city->latin_name = $this->request->post('latin_name');
                        $city->visible = $this->request->post('visible');
                        $city->hide_time = $this->request->post('hide_time');
                        $this->city->update_city($id,$city);
                            $this->db->query('DELETE FROM __shipping_area WHERE shipping_city_id=?', $id);

                            foreach ($city_areas as $city_area) {
                                $city_area->shipping_city_id =$id;
                                $this->db->query("INSERT INTO __shipping_area SET ?%", $city_area);
                            }
                    }
                }
                elseif ($this->request->post('delete')){
                    $this->db->query('DELETE FROM __shipping_area WHERE shipping_city_id=?', $id);
                    $city_areas = array();
                    $this->city->delete_city($id);
                    $this->design->assign('mess', "Город удалён");
                }
            }
            else{
                if ($this->request->post('save')){
                    if ($this->request->post('name') != "") {
                        $city->name = $this->request->post('name');
                        $city->id = $this->city->add_city($city);
                        $this->db->query('DELETE FROM __shipping_area WHERE shipping_city_id=?', $city->id);

                        foreach ($city_areas as $city_area) {
                            $city_area->shipping_city_id =$city->id;
                            $this->db->query("INSERT INTO __shipping_area SET ?%", $city_area);
                        }
                    }
                }
            }
            $city_id = $this->request->post('city_id');
            if (!empty($city_id)) {
                $city = $this->city->get_city($city_id);
            }
        } else {
            $city_id = $this->request->get('id');
            if (!empty($city_id)) {
                $city = $this->city->get_city($city_id);
                $this->db->query("SELECT * FROM __shipping_area WHERE shipping_city_id=?", $city_id);
                $city_areas = $this->db->results();
            }
        }

        if (!empty($city)) {
            $this->design->assign('city', $city);
        }
        if (empty($city_areas)) {
            $city_areas = array();
        }

        $regions = $this->regions->get_regions();

        $this->design->assign('regions', $regions);

        $this->design->assign('city_areas', $city_areas);
        return $this->design->fetch('city.tpl');
    }
}
