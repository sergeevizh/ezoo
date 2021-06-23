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
 * Настройки сайта
 * @property string $site_name          Имя сайта
 * @property string $company_name       Имя компании
 * @property string $date_format        Формат даты
 * @property string $admin_email        Email для восстановления пароля
 * Оповещения
 * @property string $order_email - Email для оповещение о заказах
 * @property string $comment_email - Email для оповещение о комментариях
 * @property string $notify_from_email - Обратный адрес оповещений
 * Формат цены
 * @property string $decimals_point - Разделитель копеек
 * @property string $thousands_separator - Разделитель тысяч
 * Настройки каталога
 * @property string $products_num - Товаров на странице сайта
 * @property string $products_num_admin - Товаров на странице админки
 * @property string $max_order_amount - Максимум товаров в заказе
 * @property string $units - Единицы измерения товаров
 * Изображения товаров
 * @property string $watermark_offset_x - Горизонтальное положение водяного знака
 * @property string $watermark_offset_y - Вертикальное положение водяного знака
 * @property string $watermark_transparency - Прозрачность знака (больше — прозрачней)
 * @property string $images_sharpen - Резкость изображений (рекомендуется 20%)
 *
 * @property string $theme -
 * @property string $last_1c_orders_export_date -
 * @property string $license -
 *
 * @property string $pz_server
 * @property string $pz_password
 * @property string $pz_phones
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
				`ifstatus` = '".$bonus->ifstatus."', `csv` = '".$bonus->csv."' WHERE `id_bonus` = '".$bonus->id."'";
			
		$sql = $this->db->placehold($sq);
		$this->db->query($sql);
	}
	public function updatebonuspromos($bonus)
    {	
		foreach ($bonus->promokod as $prom){
			$query = $this->db->placehold("INSERT INTO __bonus_promos (`id_bonus`, `promo`,`active`) VALUES (?,?,?)", $bonus->id, $prom['promo'],$prom['active']);
        if(!$this->db->query($query)){
			$query = $this->db->placehold("UPDATE __bonus_promos SET `id_bonus` = '".$bonus->id."', `active` = '".$prom['active']."' WHERE `promo` = '".$prom['promo']."'");
			$this->db->query($query);
			}
		}
	}
	public function getidbonus()
    {
		$query = "SELECT id FROM __bonuss WHERE  ID = (SELECT MAX(ID) FROM __bonuss)";
		$this->db->query($query);
		$id = $this->db->result()->id;
		return $id;
	}
	public function createBonus($bonus){
		$query = $this->db->placehold("INSERT INTO __bonuss (`name`, `desc_mini`,`description`,`img_preview`, `img_detail`,`status`, `percent`) VALUES ('". 
			$bonus->name."','".$bonus->desc_mini."','".$bonus->description."','".$bonus->img_preview."','".$bonus->img_detail."','".$bonus->status."','".$bonus->percent."')");
		$this->db->query($query);
		//получаем id текущего бонуса
		$bonus->id = Bonus::getidbonus();
		$query = $this->db->placehold("INSERT INTO __bonus_conditionss (`id_bonus`, `date_order`, `time_dilevery`, `cities`, `summ`, `brands`, `products`, `time_from`, 
			`time_to`, `time_from_sale`, `time_to_sale`, `ifstatus`, `csv`) VALUES ('".$bonus->id."','".$bonus->date_order."','".$bonus->time_dilevery."','".$bonus->cities."','".
			$bonus->summ."','".$bonus->brands."','".$bonus->products."','".$bonus->time_from."','".$bonus->time_to."','".$bonus->time_from_sale."','".$bonus->time_to_sale."','".
			$bonus->ifstatus."','".$bonus->csv)."')";
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
		//echo $query;
		$this->db->query($query);
		$query = $this->db->placehold("DELETE FROM __bonus_conditionss WHERE `id_bonus` = ?", $bonus->id);
		//echo $query;
		$this->db->query($query);
		$query = $this->db->placehold("DELETE FROM __bonus_promos WHERE `id_bonus` = ?", $bonus->id);
		//echo $query;
        $this->db->query($query);
    }
   
}
