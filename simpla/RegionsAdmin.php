<?PHP

require_once 'api/Simpla.php';

########################################
class RegionsAdmin extends Simpla {

    public function fetch() {

        // Обработка действий
        if ($this->request->method('post')) {
            // Сортировка
            $positions = $this->request->post('positions');
            $ids       = array_keys($positions);
            sort($positions);
            foreach ($positions as $i => $position) {
                $this->regions->update_region($ids[$i], array('position' => $position));
            }

            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                case 'disable':
                    {
                        $this->regions->update_region($ids, array('enabled' => 0));
                        break;
                    }
                case 'enable':
                    {
                        $this->regions->update_region($ids, array('enabled' => 1));
                        break;
                    }
                case 'delete':
                    {
                        foreach ($ids as $id) {
                            $this->regions->delete_region($id);
                        }

                        break;
                    }
                }
            }

        }

        // Отображение
        $regions = $this->regions->get_regions();
        $this->design->assign('regions', $regions);

        return $this->design->fetch('regions.tpl');
    }
}

?>