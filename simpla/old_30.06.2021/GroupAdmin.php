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

class GroupAdmin extends Simpla
{
    private $allowed_image_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');

    public function fetch()
    {
        $group = new stdClass;
        if ($this->request->method('post')) {
            $group->id = $this->request->post('id', 'integer');
            $group->name = $this->request->post('name');
            $group->discount = $this->request->post('discount');

            if (empty($group->name)) {
                $this->design->assign('message_error', 'name_empty');
            } else {
                if (empty($group->id)) {
                    $group->id = $this->users->add_group($group);
                    $this->design->assign('message_success', 'added');
                } else {
                    $group->id = $this->users->update_group($group->id, $group);
                    $this->design->assign('message_success', 'updated');
                }

                // Удаление изображения
                if ($this->request->post('delete_image')) {
                    $this->users->delete_image_group($group->id);
                }
                // Загрузка изображения
                $image = $this->request->files('image');
                if (!empty($image['name']) && in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), $this->allowed_image_extentions)) {
                    $this->users->delete_image_group($group->id);

                    $basename = basename($image['name']);
                    $base = $this->image->correct_filename(pathinfo($basename, PATHINFO_FILENAME));
                    $ext = pathinfo($basename, PATHINFO_EXTENSION);
                    $image_name = $base . '.' . $ext;

                    move_uploaded_file($image['tmp_name'], $this->config->root_dir . $this->config->groups_images_dir . $image_name);
                    $this->users->update_group($group->id, array('image' => $image_name));
                }

                $group = $this->users->get_group(intval($group->id));
            }
        } else {
            $id = $this->request->get('id', 'integer');
            if (!empty($id)) {
                $group = $this->users->get_group(intval($id));
            }
        }

        if (!empty($group)) {
            $this->design->assign('group', $group);
        }

        return $this->design->fetch('group.tpl');
    }
}
