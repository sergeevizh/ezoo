<?php

require_once('Simpla.php');

class Promos_second extends Simpla
{
    public function get_codes() {
        $query = $this->db->placehold("SELECT * FROM __promo_codes_second ORDER BY id");
        $this->db->query($query);
        return $this->db->results();
    }

    public function get_first_code() {
        $query = $this->db->placehold("SELECT * FROM __promo_codes_second ORDER BY id ASC LIMIT 1");
        $this->db->query($query);
        return $this->db->result();
    }

    public function add_code($code) {
        $query = $this->db->placehold("INSERT INTO __promo_codes_second SET ?%", $code);
        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function delete_code($id) {
        $query = $this->db->placehold("DELETE FROM __promo_codes_second WHERE id=?", $id);
        $this->db->query($query);
    }

    public function clear_codes() {
        $query = $this->db->placehold("TRUNCATE TABLE __promo_codes_second");
        $this->db->query($query);
    }

    public function update_code($code, $id) {
        $query = $this->db->placehold("UPDATE __promo_codes_second SET ?% WHERE id=?", $code, (int)$id);
        $this->db->query($query);
        return $id;
    }

    public function get_codes_count() {
        $query = $this->db->placehold("SELECT count(distinct id) as count FROM __promo_codes_second");
        $this->db->query($query);
        return $this->db->result('count');
    }
}
