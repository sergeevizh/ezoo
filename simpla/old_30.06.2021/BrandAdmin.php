<?php

/**
 * Simpla CMS
 *
 * @copyright	2016 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */

require_once('api/Simpla.php');

class BrandAdmin extends Simpla
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $brand = new stdClass;
        if ($this->request->method('post')) {
            $brand->id = $this->request->post('id', 'integer');
            $brand->name = $this->request->post('name');
            $brand->h1_head = $this->request->post('h1_head');
            $brand->description = $this->request->post('description');

            $brand->clear_name = $this->request->post('clear-name');
            $brand->importer = $this->request->post('importer');
            $brand->manufacturer = $this->request->post('manufacturer');

            $brand->url = trim($this->request->post('url', 'string'));
            $brand->meta_title = $this->request->post('meta_title');
            $brand->meta_keywords = $this->request->post('meta_keywords');
            $brand->meta_description = $this->request->post('meta_description');

            $brand->sort = $this->request->post('sort');

            $brand->color = $this->request->post('color');

            $brand->license_link = $this->request->post('license_link');

            $brand->label_new = $this->request->post('label_new', 'boolean');
            $brand->visible_is_main = $this->request->post('visible_is_main', 'boolean');

            // Не допустить одинаковые URL разделов.
            if (($c = $this->brands->get_brand($brand->url)) && $c->id != $brand->id) {
                $this->design->assign('message_error', 'url_exists');
            } elseif (empty($brand->name)) {
                $this->design->assign('message_error', 'name_empty');
            } elseif (empty($brand->url)) {
                $this->design->assign('message_error', 'url_empty');
            } else {
                if (empty($brand->id)) {
                    $brand->id = $this->brands->add_brand($brand);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->brands->update_brand($brand->id, $brand);
                    $this->design->assign('message_success', 'updated');
                }
                // Удаление баннера
                if ($this->request->post('delete_banner')) {
                    $this->brands->delete_banner($brand->id);
                }
                // Удаление изображения
                if ($this->request->post('delete_image')) {
                    $this->brands->delete_image($brand->id);
                }

                // Удаление фонового изображения
                if ($this->request->post('delete_background')) {
                    $this->brands->delete_background($brand->id);
                }

                // Загрузка изображения
                $image = $this->request->files('image');

                // Загрузка баннера
                $banner = $this->request->files('banner');

                // Загрузка фонового изображения
                $background = $this->request->files('background');

                if (!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->brands->delete_image($brand->id);
                    move_uploaded_file($image['tmp_name'], $this->config->root_dir.$this->config->brands_images_dir.$image['name']);
                    $this->brands->update_brand($brand->id, array('image'=>$image['name']));
                }

                if (!empty($banner['name']) && in_array(strtolower(pathinfo($banner['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->brands->delete_banner($brand->id);
                    move_uploaded_file($banner['tmp_name'], $this->config->root_dir.$this->config->brands_images_dir.$banner['name']);
                    $this->brands->update_brand($brand->id, array('banner'=>$banner['name']));
                }

                if (!empty($background['name']) && in_array(strtolower(pathinfo($background['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->brands->delete_background($brand->id);
                    move_uploaded_file($background['tmp_name'], $this->config->root_dir.$this->config->brands_background_images_dir.$background['name']);
                    $this->brands->update_brand($brand->id, array('background'=>$background['name']));
                }

                $brand_supply_dates = $this->request->post('supply_dates');

                if ($brand_supply_dates) {
                    foreach ($brand_supply_dates as $region => $date) {
                        $date_record = $this->dates->getBrandsSupplyDates(array('brand_id' => (int)$brand->id, 'region_id' => $region));
                        if (empty($date_record) && !empty($date)) {
                            $this->dates->addBrandSupplyDate(array('brand_id' => (int)$brand->id, 'region_id' => $region, 'date' => $date));
                        } else {
                            if (!empty($date)) {
                                $this->dates->updateBrandSupplyDate(array('brand_id' => (int)$brand->id, 'region_id' => $region, 'date' => $date));
                            } else {
                                $this->dates->deleteBrandSupplyDate(array('brand_id' => (int)$brand->id, 'region_id' => $region));
                            }
                        }
                    }
                }

                $brand = $this->brands->get_brand($brand->id);
            }
        } else {
            $brand->id = $this->request->get('id', 'integer');
            $brand = $this->brands->get_brand($brand->id);
        }

        $supply_dates = array();
        foreach ($this->dates->getBrandsSupplyDates(array('brand_id' => (int)$brand->id)) as $date){
            $supply_dates[$date->region_id] = $date->date;
        }
        $this->design->assign('supply_dates', $supply_dates);

        $regions = $this->regions->get_regions();

        $this->design->assign('regions', $regions);

        $this->design->assign('brand', $brand);

        return  $this->design->fetch('brand.tpl');
    }
}
