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

class SupplyDates extends Simpla
{

    /**
     * @param array $filter
     * @return array|bool|object
     */
    public function getVariantsSupplyDates($filter = array())
    {
        $filter['model_name'] = '__supply_dates_variants';
        $filter['entity_type'] = 'variant_id';

        if (isset($filter['variant_id'])){
            $filter['entity_id'] = $filter['variant_id'];
        }

        return $this->getData($filter);
    }

    /**
     * @param array $filter
     * @return array|bool|object
     */
    public function getBrandsSupplyDates($filter = array())
    {
        $filter['model_name'] = '__supply_dates_brands';
        $filter['entity_type'] = 'brand_id';

        if (isset($filter['brand_id'])){
            $filter['entity_id'] = $filter['brand_id'];
        }

        return $this->getData($filter);
    }

    /**
     * @param array $data
     * @return int
     */
    public function addBrandSupplyDate($data = array())
    {
        $data['model_name'] = '__supply_dates_brands';
        $data['entity_type'] = 'brand_id';
        $data['entity_id'] = $data['brand_id'];

        return $this->addData($data);
    }

    /**
     * @param array $data
     * @return int
     */
    public function addVariantSupplyDate($data = array())
    {
        $data['model_name'] = '__supply_dates_variants';
        $data['entity_type'] = 'variant_id';
        $data['entity_id'] = $data['variant_id'];

        return $this->addData($data);
    }

    /**
     * @param array $data
     */
    public function deleteBrandSupplyDate($data = array())
    {

        $data['model_name'] = '__supply_dates_brands';
        $data['entity_type'] = 'brand_id';
        $data['entity_id'] = $data['brand_id'];

        $this->deleteData($data);
    }

    /**
     * @param array $data
     */
    public function deleteVariantSupplyDate($data = array())
    {
        $data['model_name'] = '__supply_dates_variants';
        $data['entity_type'] = 'variant_id';
        $data['entity_id'] = $data['variant_id'];

        $this->deleteData($data);
    }

    /**
     * @param array $data
     */
    public function updateBrandSupplyDate($data = array()){
        $data['model_name'] = '__supply_dates_brands';
        $data['entity_type'] = 'brand_id';
        $data['entity_id'] = $data['brand_id'];

        $this->updateData($data);
    }

    /**
     * @param array $data
     */
    public function updateVariantSupplyDate($data = array()){
        $data['model_name'] = '__supply_dates_variants';
        $data['entity_type'] = 'variant_id';
        $data['entity_id'] = $data['variant_id'];

        $this->updateData($data);
    }

    /*
     * Общие методы для данных
     *
     * */

    /**
     * @param array $data
     * @return array|bool|object
     */
    public function getData($data = array())
    {


        try{
            if (!is_array($data)){
                throw new Exception('Data variable must be an array');
            } else if (empty($data['entity_type'])){
                throw new Exception('Data variable must contain entity type');
            } else if (empty($data['model_name'])){
                throw new Exception('Empty model name');
            }

            $filterRegion = '';
            $filterId = '';

            if (isset($data['region_id'])){
                $filterRegion = $this->db->placehold(" AND region_id=?", (int)$data['region_id']);
            }

            if (isset($data['entity_id'])){
                $filterId = $this->db->placehold(" AND " . $data['entity_type'] . "=?", (int)$data['entity_id']);
            }


            $query = $this->db->placehold("SELECT date, region_id FROM " . $data['model_name'] . " WHERE 1 $filterRegion $filterId");
            $this->db->query($query);


            return $this->db->results();

        } catch (Exception $e){
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param array $data
     */
    public function updateData($data = array()){
        try{
            if (!is_array($data)){
                throw new Exception('Data variable must be an array');
            } else if (empty($data['model_name'])){
                throw new Exception('Empty model name');
            } else if (empty($data['id'])){
                if (empty($data['entity_type'])){
                    throw new Exception('Empty entity type');
                } else if (empty($data['entity_id'])){
                    throw new Exception('Empty entity id');
                }
            }

            $condition = '';

            if (!empty($data['id'])){
                $condition = $this->db->placehold(" AND id=?", (int)$data['id']);
            } else {
                $condition = $this->db->placehold(" AND " . $data['entity_type'] . "=? AND region_id=?", $data['entity_id'], $data['region_id']);
            }

            $query = $this->db->placehold("UPDATE " . $data['model_name'] . " WHERE $condition");
            $this->db->query($query);


        } catch (Exception $e){
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param array $data
     * @return int $id
     */
    public function addData($data = array()){
        try{
            if (!is_array($data)){
                throw new Exception('Data variable must be an array');
            } else if (empty($data['entity_type'])){
                throw new Exception('Data variable must contain entity type');
            } else if (empty($data['model_name'])){
                throw new Exception('Empty model name');
            } else if (empty($data['entity_id'])){
                throw new Exception('Empty entity id');
            } else if (!isset($data['region_id'])){
                throw new Exception('Empty region id');
            } else if (empty($data['date'])){
                throw new Exception('Empty date');
            }

            $data_insert = new stdClass();
            $data_insert->{$data['entity_type']} = $data['entity_id'];
            $data_insert->region_id = $data['region_id'];
            $data_insert->date = $data['date'];

            $query = $this->db->placehold("INSERT INTO " . $data['model_name'] . " SET ?%", $data_insert);
            $this->db->query($query);

            $id = $this->db->insert_id();

            return $id;

        } catch (Exception $e){
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param array $data
     */
    public function deleteData($data = array())
    {
        try{
            if (!is_array($data)){
                throw new Exception('Data variable must be an array');
            } else if (empty($data['model_name'])){
                throw new Exception('Empty model name');
            } else if (empty($data['id'])){
                if (empty($data['entity_type'])){
                    throw new Exception('Empty entity type');
                } else if (empty($data['entity_id'])){

                    throw new Exception('Empty entity id');
                }
            }

            $condition = '';

            if (!empty($data['id'])){
                $condition = $this->db->placehold(" id=?", (int)$data['id']);
            } else {
                $condition = $this->db->placehold($data['entity_type'] . "=? AND region_id=?", $data['entity_id'], $data['region_id']);
            }

            $query = $this->db->placehold("DELETE FROM " . $data['model_name'] . " WHERE $condition");
            $this->db->query($query);

        } catch(Exception $e){
            echo $e->getMessage();
            die;
        }
    }

}
