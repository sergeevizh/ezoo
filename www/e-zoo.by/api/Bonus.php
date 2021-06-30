<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */


require_once('Simpla.php');

/**
 * Class Settings
 *
 *
 * Бонусная программа
*/
class Bonus extends Simpla
{
    /**
     * @var array $vars
     */
    private $vars = array();

    public function __construct()
    {
        parent::__construct();

        // Выбираем из базы настройки
        $this->db->query('SELECT name, value FROM __settings');

        // и записываем их в переменную
        foreach ($this->db->results() as $result) {
            if (!($this->vars[$result->name] = @unserialize($result->value))) {
                $this->vars[$result->name] = $result->value;
            }
        }
    }

    public function __get($name)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        } elseif ($res = parent::__get($name)) {
            return $res;
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->vars[$name] = $value;

        if (is_array($value)) {
            $value = serialize($value);
        } else {
            $value = (string)$value;
        }

        $this->db->query('SELECT count(*) as count FROM __settings WHERE name=?', $name);

        if ($this->db->result('count') > 0) {
            $this->db->query('UPDATE __settings SET value=? WHERE name=?', $value, $name);
        } else {
            $this->db->query('INSERT INTO __settings SET value=?, name=?', $value, $name);
        }
    }
	
	
	public function getNameIdBonuses()
	{
		$query = $this->db->placehold("SELECT `name`, `id`, `status` FROM __bonuss");
        $this->db->query($query);
        return $this->db->results();
	}
	public function getBonusbyId($id)
	{
		$query = $this->db->placehold("SELECT * FROM __bonuss as `bs` left join __bonus_conditionss as `bc` ON bs.id=bc.id_bonus where bs.id=".$id);
        $this->db->query($query);
        return $this->db->result();
	}
	public function getBonusbyIdtoDay($id)
	{
		$query = $this->db->placehold("SELECT * FROM __bonuss as `bs` inner join __bonus_conditionss as `bc` ON bs.id=bc.id_bonus where bs.id=".$id." and bs.status = 1");
        $this->db->query($query);
        return $this->db->result();
	}
	public function getBonusbyStatus($st)
	{
		$query = $this->db->placehold("SELECT * FROM __bonuss as `bs` left join __bonus_conditionss as `bc` ON bs.id=bc.id_bonus where bs.status=".$st);
        $this->db->query($query);
        return $this->db->results();
	}
	
	
	public function getBonusbyStatusNotNull($st)
	{
		$query = $this->db->placehold("SELECT * FROM __bonuss as `bs` left join __bonus_conditionss as `bc` ON bs.id=bc.id_bonus where bs.status=".$st." AND bc.ifstatus !='' ");
        $this->db->query($query);
        return $this->db->results();
	}
	public function updateSbonuss($bonus)
    {
		$sq = "UPDATE __bonuss SET `name` = '".$bonus->name."', `desc_mini` = '".$bonus->desc_mini."', `description` = '".$bonus->description.
				"', `status` = '".$bonus->status."', `percent` = '".$bonus->percent."'";
		if(!empty($bonus->img_preview))
			$sq.=", `img_preview` = '".$bonus->img_preview."'";	
		if(!empty($bonus->img_detail))
			$sq.=", `img_detail` = '".$bonus->img_detail."'";	
		$where = " WHERE `id` = '".$bonus->id."'";
		$sql = $this->db->placehold($sq.$where);
		$this->db->query($sql);
    } 
	public function updatebonusconditionss($bonus)
    {	
		
		$sql = $this->db->placehold("SELECT id FROM `__bonus_conditionss` WHERE `id_bonus` = '".$bonus->id."'");
		$this->db->query($sql);

			$sq = "UPDATE __bonus_conditionss SET `date_order` = '".$bonus->date_order."', `time_dilevery` = '".$bonus->time_dilevery."', `cities` = '".$bonus->cities."', 
				`summ` = '".$bonus->summ."', `brands` = '".$bonus->brands."', `products` = '".$bonus->products."', `time_from` = '".$bonus->time_from."', 
				`time_to` = '".$bonus->time_to."', `time_from_sale` = '".$bonus->time_from_sale."', `time_to_sale` ='".$bonus->time_to_sale."', 
				`ifstatus` = '".$bonus->ifstatus."', `csv` = '".$bonus->csv."', `service` = '".$bonus->service."' WHERE `id_bonus` = '".$bonus->id."'";
			
		$sql = $this->db->placehold($sq);
		$this->db->query($sql);
	}
	public function updatebonuspromos($bonus)
    {	
		if(!empty($bonus->promokod)){
			foreach ($bonus->promokod as $prom){
				$query = $this->db->placehold("INSERT INTO __bonus_promos (`id_bonus`, `promo`,`active`,`service`) VALUES (?,?,?,?)", $bonus->id, $prom['promo'],$prom['active'],$bonus->service);
				$this->db->query($query);
			/*if(!$this->db->query($query)){
				$query = $this->db->placehold("UPDATE __bonus_promos SET `id_bonus` = '".$bonus->id."', `active` = '".$prom['active']."', `service` = '".$bonus->service."', `user_id` = 0 WHERE `promo` = '".$prom['promo']."'");
				$this->db->query($query);
				}*/
			}
			}else {
				$query = $this->db->placehold("UPDATE __bonus_promos SET `service` = '".$bonus->service."' WHERE `id_bonus` = '".$bonus->id."'");
				$this->db->query($query);
			}
	}
	public function getidbonus()
    {
		$query = "SELECT id FROM __bonuss WHERE  ID = (SELECT MAX(ID) FROM __bonuss)";
		$this->db->query($query);
		$id = $this->db->result()->id;
		return $id;
	}
	public function getbonusbyUser($ids_bonus)
	{
		if(empty($ids_bonus)) return;
		$ids_bonus = explode(';',$ids_bonus);
		$mass = array();
		foreach ($ids_bonus as $key=>$id_bonus){
			$mass[$key] = Bonus::getBonusbyIdtoDay($id_bonus);
			$query = $this->db->placehold("SELECT `promo`, `service` FROM __bonus_promos WHERE id_bonus = ".$id_bonus." AND active = 1 and user_id=".$this->user->id);
			$this->db->query($query);
			$str = $this->db->result();
			if(!empty($str)){
				$mass[$key]->promo = $str->promo;
				$mass[$key]->service = $str->service;
			}
		}
		$ret = array();
		foreach ($mass as $mas){
			if(empty($mas)){ continue;}
			if($mas->time_to == '0000-00-00')
				$kk = time()+86400;
			else $kk = strtotime($mas->time_to)+86400;
			if(!empty($mas) && $kk >= time() && strtotime($mas->time_from) <= time() ){
				if(!empty($mas->percent) && $mas->percent > 0 )
					$ret[] = $mas;
				if(!empty($mas->promo))
					$ret[] = $mas;
			}	
		}
		return $ret;
	}
	public function getcountpromo($id)
    {
		//$query = "SELECT count(promo) as count_promo FROM `s_bonus_promos` where active = 1 and id_bonus = ".$id;
		$query = "SELECT count(promo) as count_promo FROM `s_bonus_promos` where user_id = 0 and id_bonus = ".$id;
		$this->db->query($query);
		return $this->db->result()->count_promo;
	}
	public function createBonus($bonus){
		$query = $this->db->placehold("INSERT INTO __bonuss (`name`, `desc_mini`,`description`,`img_preview`, `img_detail`,`status`, `percent`) VALUES ('". 
			$bonus->name."','".$bonus->desc_mini."','".$bonus->description."','".$bonus->img_preview."','".$bonus->img_detail."','".$bonus->status."','".$bonus->percent."')");
		$this->db->query($query);
		//получаем id текущего бонуса
		$bonus->id = Bonus::getidbonus();
		$query = $this->db->placehold("INSERT INTO __bonus_conditionss (`id_bonus`, `date_order`, `time_dilevery`, `cities`, `summ`, `brands`, `products`, `time_from`, 
			`time_to`, `time_from_sale`, `time_to_sale`, `ifstatus`, `csv`,`service`) VALUES ('".$bonus->id."','".$bonus->date_order."','".$bonus->time_dilevery."','".$bonus->cities."','".
			$bonus->summ."','".$bonus->brands."','".$bonus->products."','".$bonus->time_from."','".$bonus->time_to."','".$bonus->time_from_sale."','".$bonus->time_to_sale."','".
			$bonus->ifstatus."','".$bonus->csv."','".$bonus->service)."')";
		$this->db->query($query);
		return $bonus->id;
	}
	
	
	
	
	
    public function __isset($name)
    {
        return isset($this->vars[$name]);
    }

    
    public function deleteBonus($bonus)
    {	
        $query = $this->db->placehold("DELETE FROM __bonuss  WHERE `id` = ?", $bonus->id);
		$this->db->query($query);
		$query = $this->db->placehold("DELETE FROM __bonus_conditionss WHERE `id_bonus` = ?", $bonus->id);
		$this->db->query($query);
		$query = $this->db->placehold("DELETE FROM __bonus_promos WHERE `id_bonus` = ?", $bonus->id);
        $this->db->query($query);
    }
	 public function deleteBonusByUser($id_bonus, $id_user)
    {	
		$query = $this->db->placehold("SELECT id_bonus FROM __users WHERE id = ".$id_user);
		$this->db->query($query);
		$bb = explode(';',$this->db->result()->id_bonus);
		foreach($bb as $key=> $bbb){
			if($bbb == $id_bonus) 
				unset($bb[$key]);
		}
		$bb = implode(';',$bb);
		$query = $this->db->placehold("UPDATE __users SET `id_bonus` = '".$bb."' WHERE id = ".$id_user);
		$this->db->query($query);		
	}
   
}
