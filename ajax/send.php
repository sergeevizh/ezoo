<?php
session_start();
require_once('../api/Simpla.php');

class SendAjax extends Simpla
{
    public function subscribe()
    {
        $this->notify->email_subscribe_admin();
        echo json_encode(array('success'=>1));

    }
    public function sendlatemassage() {
        $this->notify->mail_late_courier();
        echo json_encode(array('success'=>1));
    }
}

if($_POST["sendsubscribe"]==1){

    $send = new SendAjax();
    $result = $send->subscribe();

}
if ($_POST["sendlateorder"] == 1) {
    $send = new SendAjax();
    $result = $send->sendlatemassage();
}
