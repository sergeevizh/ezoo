<?php

require_once('../../api/Simpla.php');

$simpla = new Simpla();

$json = [];
$json_lecense = [];

$checkbox =  $simpla->request->post('setting_pickup_global') ? 1 : null;
$checkbox_lecense =  $simpla->request->post('setting_lecense_global') ? 1 : null;

$productsWithPickups = $simpla->settings->getProductsWithPickups(1);
$productsWithLecense = $simpla->settings->getProductsWithLecense(1);

$data = generateData($checkbox, $productsWithPickups);
$data_lecense = generateDataLecense($checkbox_lecense, $productsWithLecense);

$checkSetting = $simpla->settings->checkPickupSetting('setting_pickup_global');
$checkSettingLecense = $simpla->settings->checkLecenseSetting('setting_lecense_global');

$idSetting = getIdSettings($checkSetting);
$idSettingLecense = getIdSettingsLecense($checkSettingLecense);

if ($checkbox){

    $simpla->settings->insertSetting('setting_pickup_global', $data);
    $simpla->settings->updatePickupForProductsAndSetNull();
    $json['success'] = true;
    $json['answer'] = 'Все товары доступны для доставки';

}else{

    $value = unserialize($checkSetting->value)['products'];
    if (isset($value) && $value){
        foreach ($value as $item) {
            $simpla->settings->updatePickupForProducts($item->product_id);
        }
    }
    $simpla->settings->deleteSettings('setting_pickup_global');
    $json['success'] = true;
    $json['answer'] = 'Доставка для ранее открытых товарах отменена';
}
if ($checkbox_lecense){

    $simpla->settings->insertSettingLecense('setting_lecense_global', $data_lecense);
    $simpla->settings->updateLecenseForProductsAndSetNull();
    $json_lecense['success_lecense'] = true;
    $json_lecense['answer_lecense'] = 'Все товары доступны для доставки';

}else{

    $value = unserialize($checkSettingLecense->value)['products'];
    if (isset($value) && $value){
        foreach ($value as $item) {
            $simpla->settings->updateLecenseForProducts($item->product_id);
        }
    }
    $simpla->settings->deleteSettingsLecense('setting_lecense_global');
    $json_lecense['success_lecense'] = true;
    $json_lecense['answer_lecense'] = 'Доставка для ранее открытых товарах отменена';
}


function generateData($checkbox, $productsWithPickups)
{
    return [
        'checkbox' => $checkbox,
        'products' => $productsWithPickups
    ];
}
function generateDataLecense($checkbox_lecense, $productsWithLecense)
{
    return [
        'checkbox_lecense' => $checkbox_lecense,
        'products_lecense' => $productsWithLecense
    ];
}

function getIdSettings($checkSetting)
{
    if ($checkSetting){

        $idSetting = $checkSetting->setting_id;

    }else{

        $idSetting = false;

    }

    return $idSetting;
}
function getIdSettingsLecense($checkSettingLecense)
{
    if ($checkSettingLecense){

        $idSettingLecense = $checkSettingLecense->setting_id;

    }else{

        $idSettingLecense = false;

    }

    return $idSettingLecense;
}

print_r(json_encode($json));
exit;
