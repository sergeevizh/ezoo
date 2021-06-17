<?php

require_once('Simpla.php');

class DeliveryArea extends Simpla
{
    public function get_areas(){
        $query = $this->db->placehold("SELECT da.id as id, da.polygon, dc.id as courier_id, dc.name as courier  FROM __delivery_areas da LEFT JOIN __delivery_courier_to_area dcta ON da.id=dcta.area_id LEFT JOIN __delivery_couriers dc ON dcta.courier_id=dc.id ORDER BY id");
        $this->db->query($query);

        return $this->db->results();
    }

    public function get_area($id) {
        $query = $this->db->placehold("SELECT * FROM __delivery_areas WHERE id=? LIMIT 1", (int)$id);
        $this->db->query($query);

        return $this->db->result();
    }

    public function add_area($area) {
        $query = $this->db->placehold("INSERT INTO __delivery_areas SET polygon=?", $area);
        $this->db->query($query);

        return $this->db->insert_id();
    }

    public function edit_area($id, $area) {
        $query = $this->db->placehold("UPDATE __delivery_areas SET polygon=? WHERE id=? ", $area, (int)$id);
        $this->db->query($query);

        return $id;
    }

    public function delete_area($id) {
        $query = $this->db->placehold("DELETE FROM __delivery_areas WHERE id=? LIMIT 1", (int)$id);
        $this->db->query($query);
    }

    public function get_area_courier($area_id) {
        $query = $this->db->placehold("SELECT * FROM __delivery_courier_to_area WHERE area_id=? LIMIT 1", (int)$area_id);
        $this->db->query($query);

        return $this->db->result();
    }

    public function set_area_courier($courier_id, $area_id) {
        $query = $this->db->placehold("INSERT INTO __delivery_courier_to_area SET courier_id=?, area_id=?", (int)$courier_id, (int)$area_id);
        $this->db->query($query);

        return $this->db->insert_id();
    }

    public function edit_area_courier($area_id, $courier_id) {
        $query = $this->db->placehold("UPDATE __delivery_courier_to_area SET courier_id=? WHERE area_id=?", (int)$courier_id, (int)$area_id);
        $this->db->query($query);
    }

    public function delete_area_courier($area_id) {
        $query = $this->db->placehold("DELETE FROM __delivery_courier_to_area WHERE area_id=? LIMIT 1", (int)$area_id);
        $this->db->query($query);
    }
}
