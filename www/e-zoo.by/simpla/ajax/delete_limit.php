<?php
require_once('../../api/Simpla.php');

$simpla = new Simpla();

$json = '';

if ($simpla->request->post('limit_id')) {
    $limit_id = $simpla->request->post('limit_id');
    $simpla->limits->delete_limit((int)$limit_id);
    $json = 'success';
} else {
    $json = 'error';
}

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");

print json_encode($json);
