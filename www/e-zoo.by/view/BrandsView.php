<?PHP

require_once('View.php');

class BrandsView extends View
{
    public function fetch()
    {

        $brands = $this->brands->get_brands(array('visible' => 1, 'is_image' => true));
        //$this->design->assign('brands', $brands);
        foreach ($brands as $brand) {
            $key = mb_strtoupper(mb_substr($brand->name, 0, 1, 'UTF-8'), 'UTF-8');
            if (!isset($alf_brands[$key])) {
                $alf_brands[$key] = array();
            }
            $alf_brands[$key][$brand->name] = $brand;
            ksort($alf_brands[$key]);
        }
        ksort($alf_brands);
        $this->design->assign('alf_brands', $alf_brands);


        // Метатеги
        if ($this->page) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
        }

        return $this->design->fetch('brands.tpl');
    }
}
