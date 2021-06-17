<?php

require_once('Simpla.php');

class Limits extends Simpla
{
    public function get_limits() {
        $query = $this->db->placehold("SELECT * FROM __products_limits pl ORDER BY pl.id");
        $this->db->query($query);
        return $this->db->results();
    }

    public function get_limit_by_date($date, $time, $city) {
        $query = $this->db->placehold("SELECT id, current FROM __products_limits WHERE date=? AND time=? AND city=? LIMIT 1", $date, $time, $city);
        $this->db->query($query);
        return $this->db->result();
    }

    public function add_limit($limit, $limit_id = null) {
        if ($limit_id){
            $this->db->placehold("DELETE FROM __products_limits WHERE `id` = ?", (int)$limit_id);
        }
        $query = $this->db->placehold("INSERT INTO __products_limits (`date`, `time`, `products_limit`, `city`) VALUES (?,?,?,?)", html_entity_decode(trim($limit['date'])), $limit['time'], $limit['products_limit'], $limit['city']);
        $this->db->query($query);
        $id = $this->db->insert_id();
        return $id;
    }

    public function update_limit($limit, $limit_id) {
        $query = $this->db->placehold("UPDATE __products_limits SET ?% WHERE id=? LIMIT 1", $limit, (int)$limit_id);
        $this->db->query($query);
        return $limit_id;
    }

    public function delete_limit($id) {
        $query = $this->db->placehold("DELETE FROM __products_limits WHERE id=?", $id);
        $this->db->query($query);
    }
}
