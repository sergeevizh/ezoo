<?php

require_once('View.php');

class ThankyouView extends View
{
    public function __construct()
    {
        parent::__construct();
        $this->design->smarty->registerPlugin("function", "checkout_form", array($this, 'checkout_form'));
    }

    public function fetch()
    {
        // ID заказа
        if ($order_id = $this->request->get('order_id')) {
            $order = $this->orders->get_order((int)$order_id);
        } elseif (!empty($_SESSION['order_id'])) {
            $order = $this->orders->get_order(intval($_SESSION['order_id']));
        } else {
            return false;
        }

        if (!$order) {
            return false;
        }
        
        // Способ доставки
        $delivery = $this->delivery->get_delivery($order->delivery_id);
        $this->design->assign('delivery', $delivery);

        $this->design->assign('order', $order);

        // Способ оплаты
        if ($order->payment_method_id) {
            $payment_method = $this->payment->get_payment_method($order->payment_method_id);
            $payment_method_assist = $this->payment->get_payment_method(15); // принудительно грузим Assist по id
            $this->design->assign('payment_method', $payment_method);
            $this->design->assign('payment_method_assist', $payment_method_assist);
        }

        // Варианты оплаты
        $payment_methods = $this->payment->get_payment_methods(array('delivery_id' => $order->delivery_id, 'enabled' => 1));
        $this->design->assign('payment_methods', $payment_methods);

        return $this->design->fetch('thanknyou.tpl');
    }

    public function checkout_form($params, &$smarty)
    {
        $module_name = preg_replace("/[^A-Za-z0-9]+/", "", $params['module']);

        $form = '';
        if (!empty($module_name) && is_file("payment/$module_name/$module_name.php")) {
            include_once("payment/$module_name/$module_name.php");
            $module = new $module_name();
            $form = $module->checkout_form($params['order_id'], $params['button_text']);
        }
        return $form;
    }
}
