<?php
require_once('../api/Simpla.php');

class OrderAjax extends Simpla
{
    public function fetch()
    {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

        return $this->get_polygons();
    }

    private function get_polygons()
    {
        $query = $this->db->placehold("SELECT * FROM __delivery_areas ORDER BY id");
        $this->db->query($query);

        return $this->db->results();
    }
}

$order_ajax = new OrderAjax;
$result = $order_ajax->fetch();

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("X-Robots-Tag: noindex, noarchive, nosnippet");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($result);
exit;
