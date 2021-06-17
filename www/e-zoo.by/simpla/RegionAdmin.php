<?PHP
require_once 'api/Simpla.php';

class RegionAdmin extends Simpla {
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch() {
        if ($this->request->method('POST')) {
            $region             = new stdClass;
            $region->id         = $this->request->post('id', 'integer');
            $region->enabled    = $this->request->post('enabled', 'boolean');
            $region->name       = $this->request->post('name');
            $region->short_name = $this->request->post('short_name');
            $region->code_is    = $this->request->post('code_is');

            /*     ## Не допустить такой же код
            $this->db->query('SELECT count(*) as count FROM __regions WHERE code_is=? AND id!=?', $region->code_is, $region->id);
            $region_exists = $this->db->result('count'); */

            if (empty($region->name)) {
                $this->design->assign('message_error', 'empty_name');
            } elseif (empty($region->short_name)) {
                $this->design->assign('message_error', 'empty_short_name');
            } elseif (empty($region->code_is)) {
                $this->design->assign('message_error', 'empty_code_is');
            } else {
                if (empty($region->id)) {
                    $region->id = $this->regions->add_region($region);
                    $region     = $this->regions->get_region($region->id);
                    $this->design->assign('message_success', 'added');
                    $this->regions->add_variant_price_column(intval($region->id));
                } else {
                    $this->regions->update_region($region->id, $region);
                    $region = $this->regions->get_region($region->id);
                    $this->design->assign('message_success', 'updated');
                }
                $_GET['id'] = $region->id;
            }
        }

        $id = $this->request->get('id', 'integer');
        if (!empty($id)) {
            $region = $this->regions->get_region(intval($id));
        }

        if (!empty($region)) {
            $this->design->assign('region', $region);
        }

        return $this->design->fetch('region.tpl');
    }

}
