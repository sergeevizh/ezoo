<?php
require_once('../api/Simpla.php');

class BonusPromoAjax extends Simpla
{
    public function fetch($promo, $id_bonus,$user_id)
    {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

        return $this->insert_active_promo($promo, $id_bonus,$user_id);
    }

    private function insert_active_promo($promo, $id_bonus,$user_id)
    {
		$query = $this->db->placehold("UPDATE __bonus_promos SET `active` = 0 WHERE promo = '".$promo."' AND id_bonus = ".$id_bonus);
		$this->db->query($query);
		$query = $this->db->placehold("SELECT `id_bonus` FROM __users WHERE id=".$user_id);								
        $this->db->query($query);
		$bonuses = explode(';',$this->db->result()->id_bonus);
		foreach($bonuses as $key => $bonus){
			if($bonus == $id_bonus)
				unset($bonuses[$key]);
		}
		$bonuses = implode(';',$bonuses);
		$query = $this->db->placehold("UPDATE __users SET `id_bonus` = '".$bonuses."' WHERE id=".$user_id);	
		return $this->db->query($query);
    }
}

$id_bonus = $_POST['id_bonus'];
$promo = $_POST['promo'];
$user_id = $_POST['user_id'];
$order_ajax = new BonusPromoAjax;
$result = $order_ajax->fetch($promo, $id_bonus, $user_id);
print $result;