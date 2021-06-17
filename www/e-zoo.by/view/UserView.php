<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('View.php');

class UserView extends View
{
    public function fetch()
    {
        if (empty($this->user)) {
            header('Location: ' . $this->config->root_url . '/user/login');
            exit();
        }

        if ($this->request->method('post')) {
            $name = $this->request->post('name');
            $phone = $this->request->post('phone');
            $address = $this->request->post('address');
            $date_birthday = $this->request->post('date_birthday');
            $email = $this->request->post('email');
            $password = $this->request->post('password');

            $this->design->assign('name', $name);
            $this->design->assign('phone', $phone);
            $this->design->assign('address', $address);
            $this->design->assign('date_birthday', $date_birthday);
            $this->design->assign('email', $email);

            $this->db->query('SELECT count(*) as count FROM __users WHERE email=? AND id!=?', $email, $this->user->id);
            $user_exists = $this->db->result('count');
            $error=false;
            if ($user_exists) {
                $this->design->assign('error', 'user_exists');
                $error=true;
            }
             elseif (empty($name)) {
                $this->design->assign('error', 'empty_name');
                $error=true;
            }
            elseif (empty($email)) {
                $this->design->assign('error', 'empty_email');
                $error=true;
            } elseif ($date_birthday>date("Y-m-d")) {
                $this->design->assign('error', 'empty_date_birthday');
                $error=true;
            } elseif ($error==false){
                if ($user_id = $this->users->update_user($this->user->id, array('name' => $name, 'email' => $email, 'phone' => $phone, 'date_birthday' => $date_birthday, 'address'=>$address))) {
                $this->user = $this->users->get_user(intval($user_id));
                $this->design->assign('name', $this->user->name);
                $this->design->assign('user', $this->user);
                $this->design->assign('email', $this->user->email);
                $this->design->assign('address', $this->user->address);
                $this->design->assign('date_birthday', $this->user->date_birthday);
                $this->design->assign('phone', $this->user->phone);
            } else {
                $this->design->assign('error', 'unknown error');
            }
            }

            if (!empty($password)) {
                $this->users->update_user($this->user->id, array('password' => $password));
            }
        } else {
            // Передаем в шаблон
            $this->design->assign('name', $this->user->name);
            $this->design->assign('email', $this->user->email);
            $this->design->assign('address', $this->user->address);
            $this->design->assign('date_birthday', $this->user->date_birthday);
            $this->design->assign('phone', $this->user->phone);

        }

        //$orders = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => 6));
        $orders = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => 6));
        $this->design->assign('orders', $orders);

        $count_orders = $this->orders->count_orders(array('user_id' => $this->user->id));
        $this->design->assign('count_orders', $count_orders);

        $sum_price = $this->orders->sum_price(array('user_id' => $this->user->id));
        $this->design->assign('sum_price', $sum_price);

        $sum_sale = $this->orders->sum_sale(array('user_id' => $this->user->id));
        $this->design->assign('sum_sale', $sum_sale);

        $this->design->assign('meta_title', $this->user->name);
      
      
        return $this->design->fetch('user.tpl');
    }
}
