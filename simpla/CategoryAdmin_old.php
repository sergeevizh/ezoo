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

class CategoryAdmin extends Simpla
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $category = new stdClass;
        $related_categories = array();
        if ($this->request->method('post')) {
            $category->id = $this->request->post('id', 'integer');
            $category->parent_id = $this->request->post('parent_id', 'integer');
            $category->name = $this->request->post('name');
            $category->visible = $this->request->post('visible', 'boolean');
            $category->visible_is_main = $this->request->post('visible_is_main', 'boolean');
            $category->visible_childs = $this->request->post('visible_childs', 'boolean');

            $category->url = trim($this->request->post('url', 'string'));
            $category->meta_title = $this->request->post('meta_title');
            $category->meta_keywords = $this->request->post('meta_keywords');
            $category->meta_description = $this->request->post('meta_description');

            $category->description = $this->request->post('description');
            // Связанные товары
            if (is_array($this->request->post('related_categories'))) {
                $rp = array();
                foreach ($this->request->post('related_categories') as $p) {
                    $rp[$p] = new stdClass;
                    $rp[$p]->category_id = $category->id;
                    $rp[$p]->related_id = $p;
                }
                $related_categories = $rp;
            }
            // Не допустить одинаковые URL разделов.
            if (($c = $this->categories->get_category($category->url)) && $c->id != $category->id) {
                $this->design->assign('message_error', 'url_exists');
            } elseif (empty($category->name)) {
                $this->design->assign('message_error', 'name_empty');
            } elseif (empty($category->url)) {
                $this->design->assign('message_error', 'url_empty');
            } else {
                if (empty($category->id)) {
                    $category->id = $this->categories->add_category($category);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->categories->update_category($category->id, $category);
                    $this->design->assign('message_success', 'updated');
                }
                // Удаление изображения
                if ($this->request->post('delete_image')) {
                    $this->categories->delete_image($category->id);
                }
                // Загрузка изображения
                $image = $this->request->files('image');
                if (!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->categories->delete_image($category->id);
                    move_uploaded_file($image['tmp_name'], $this->config->root_dir . $this->config->categories_images_dir . $image['name']);
                    $this->categories->update_category($category->id, array('image' => $image['name']));
                }
                if (!empty($category->id)) {
                    // Смежные/похожие категории
                    $query = $this->db->placehold('DELETE FROM __related_categories WHERE category_id=?', $category->id);
                    $this->db->query($query);
                    if (is_array($related_categories)) {
                        $pos = 0;
                        foreach ($related_categories as $i => $related_category) {
                            $this->categories->add_related_category($category->id, $related_category->related_id, $pos++);
                        }
                    }
                }
                $category = $this->categories->get_category(intval($category->id));
            }
        } else {
            $category->id = $this->request->get('id', 'integer');
            $category = $this->categories->get_category($category->id);
            if (!empty($category)) {
                $related_categories = $this->categories->get_related_categories(array('category_id' => $category->id));
            }
        }

        if (!empty($related_categories)) {
            $r_categories = array();
            foreach ($related_categories as &$r_p) {
                $r_categories[$r_p->related_id] = &$r_p;
            }


            $query = $this->db->placehold("SELECT *
                                            FROM __categories c
                                            WHERE c.id IN (?@)
                                            ORDER BY c.parent_id, c.position", array_keys($r_categories));
            $this->db->query($query);
            $temp_categories = $this->db->results();
            foreach ($temp_categories as $temp_category) {
                $r_categories[$temp_category->id] = $temp_category;
            }
        }

        $categories = $this->categories->get_categories_tree();

        $this->design->assign('category', $category);
        $this->design->assign('categories', $categories);
        $this->design->assign('related_categories', $related_categories);

        return $this->design->fetch('category.tpl');
    }
}
