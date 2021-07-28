<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('Simpla.php');

class City extends Simpla
{
    /**
     * Функция возвращает массив брендов, удовлетворяющих фильтру
     *
     * @param array $filter
     * @return array|bool
     */
    public function get_cities($filter = array())
    {
        $visible = '';

        if ($filter['visible']) {
            $visible = " AND b.visible='1'";
        }
        // Выбираем все города
        $query = $this->db->placehold("SELECT DISTINCT b.id, b.name, b.latin_name, b.visible
										FROM __shipping_city b
										WHERE 1
										$visible
										ORDER BY b.name");
        $this->db->query($query);

        return $this->db->results();
    }

    /**
     * Функция возвращает бренд по его id или url
     * (в зависимости от типа аргумента, int - id, string - url)
     *
     * @param  int|string $id
     * @return bool|object
     */
    public function get_city($id)
    {
        if (is_int($id)) {
            $filter = $this->db->placehold('b.id = ?', $id);
        } else {
            $filter = $this->db->placehold('b.id = ?', $id);
        }

        $query = $this->db->placehold("SELECT b.id, b.name, b.region_id, b.latin_name, b.visible, b.hide_time
										FROM __shipping_city b
										WHERE $filter
										LIMIT 1");
        $this->db->query($query);
        return $this->db->result();
    }

    /**
     * Добавление бренда
     *
     * @param  array|object $brand
     * @return mixed
     */
    public function add_city($brand)
    {
        $brand = (array)$brand;
        $this->db->query("INSERT INTO __shipping_city SET ?%", $brand);
        return $this->db->insert_id();
    }

    /**
     * Обновление бренда(ов)
     *
     * @param  int $id
     * @param  array|object $brand
     * @return mixed
     */
    public function update_city($id, $city)
    {
        $query = $this->db->placehold("UPDATE __shipping_city SET ?% WHERE id=? LIMIT 1", $city, intval($id));
        $this->db->query($query);
        return $id;
    }

    /**
     * Удаление бренда
     *
     * @param int $id
     * @return void
     */
    public function delete_city($id)
    {
        if (!empty($id)) {
            $query = $this->db->placehold("DELETE FROM __shipping_city WHERE id=? LIMIT 1", $id);
            $this->db->query($query);

            $query = $this->db->placehold("UPDATE __shipping_city SET brand_id=NULL WHERE brand_id=?", $id);
            $this->db->query($query);
        }
    }
}
