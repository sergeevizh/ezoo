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

class PhonesAdmin extends Simpla
{
    public function fetch()
    {


        $filter = array();

        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        $date_start = $this->request->get('date_start');
        if (!empty($date_start)) {
            $filter['date_start'] = $date_start;
            $this->design->assign('date_start', $date_start);
        }

        $date_end = $this->request->get('date_end');
        if (!empty($date_end)) {
            $filter['date_end'] = $date_end;
            $this->design->assign('date_end', $date_end);
        }

        // Сортировка пользователей, сохраняем в сессии, чтобы текущая сортировка не сбрасывалась
        if ($sort = $this->request->get('sort', 'string')) {
            $_SESSION['users_admin_sort'] = $sort;
        }
        if (!empty($_SESSION['users_admin_sort'])) {
            $filter['sort'] = $_SESSION['users_admin_sort'];
        } else {
            $filter['sort'] = 'name';
        }
        $this->design->assign('sort', $filter['sort']);

        $registration = $this->request->get('registration');
        if (!empty($registration)) {
            $filter['registration'] = $registration;
        }



        $contr_name = $this->request->get('contr_name');
        if (!empty($contr_name)) {
            $filter['contr_name'] = $contr_name;
        }
        $this->design->assign('contr_name', $filter['contr_name']);

        $contr_phone = $this->request->get('contr_phone');
        if (!empty($contr_phone)) {
            $filter['contr_phone'] = $contr_phone;
        }
        $this->design->assign('contr_phone', $filter['contr_phone']);

        /*      if ($this->request->method('post')) {
                  // Действия с выбранными
                  $ids = $this->request->post('check');
                  if (is_array($ids)) {
                      switch ($this->request->post('action')) {
                          case 'disable': {
                              foreach ($ids as $id) {
                                  $this->users->update_user($id, array('enabled' => 0));
                              }
                              break;
                          }
                          case 'enable': {
                              foreach ($ids as $id) {
                                  $this->users->update_user($id, array('enabled' => 1));
                              }
                              break;
                          }
                          case 'delete': {
                              foreach ($ids as $id) {
                                  $this->users->delete_user($id);
                              }
                              break;
                          }
                      }
                  }
              }

              $groups = array();
              foreach ($this->users->get_groups() as $g) {
                  $groups[$g->id] = $g;
              }


              $group = null;
              $filter = array();


              $group_id = $this->request->get('group_id', 'integer');
              if ($group_id) {
                  $group = $this->users->get_group($group_id);
                  $filter['group_id'] = $group->id;
              }

              // Поиск
              $keyword = $this->request->get('keyword', 'string');
              if (!empty($keyword)) {
                  $filter['keyword'] = $keyword;
                  $this->design->assign('keyword', $keyword);
              }



              $users_count = $this->users->count_users($filter);
              // Показать все страницы сразу
              if ($this->request->get('page') == 'all') {
                  $filter['limit'] = $users_count;
              }

              $users = $this->users->get_users($filter);
              $this->design->assign('pages_count', ceil($users_count / $filter['limit']));
              $this->design->assign('current_page', $filter['page']);
              $this->design->assign('groups', $groups);
              $this->design->assign('group', $group);
              $this->design->assign('users', $users);
              $this->design->assign('users_count', $users_count);*/
        // Показать все страницы сразу


        $phones_count = $this->phones->count_phones($filter);

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $phones_count;
        }

        $this->design->assign('pages_count', ceil($phones_count / $filter['limit']));

        $phones = $this->phones->get_phones($filter);

        $registration_filter = '';

        if (!empty($registration)) {
            switch ($registration){
                case 'registered':{
                    $registration_filter = 'registered';
                }
                    break;
                case 'unregistered':{
                    $registration_filter = 'unregistered';
                }
                    break;
                case 'all':{
                    $registration_filter = '';
                }
                    break;
                default: {
                    $registration_filter = '';
                }
                    break;

            };
            $this->design->assign('registration_filter', $registration_filter);
        }


        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('phones', $phones);

        return $this->design->fetch('phones.tpl');
    }
}
