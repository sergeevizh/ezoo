<?php
    require_once('../../api/Simpla.php');
    $simpla = new Simpla();
    $limit = 100;
    echo json_encode($_POST);

    if (!$simpla->managers->access('delivery_areas')) {
        return false;
    }
    $simpla->db->query('SELECT polygon FROM __delivery_areas ', $limit);
