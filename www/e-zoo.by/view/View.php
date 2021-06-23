<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('api/Simpla.php');

class View extends Simpla
{
    /* Смысл класса в доступности следующих переменных в любом View */
    private static $view_instance;
    public $currency;
    public $currencies;
    public $user;
    public $group;
    public $brand_out_discont=286001;
    public $brand_out_discont_metka=222222;
    //public $brands;
    public $city_select;
    public $city_select_lat;
    public $city_region;
    /*regions*/
    public $region;
    /*/regions*/


    /* Класс View похож на синглтон, храним статически его инстанс */
    public $page;

    public function __construct()
    {
        parent::__construct();

        /*regions*/
        if (defined('IS_CLIENT')==false){
            define('IS_CLIENT', true);
        }
        /*/regions*/

        // Если инстанс класса уже существует - просто используем уже существующие переменные
        if (self::$view_instance) {
            $this->currency = &self::$view_instance->currency;
            $this->currencies = &self::$view_instance->currencies;
            $this->user = &self::$view_instance->user;
            $this->group = &self::$view_instance->group;
            $this->page = &self::$view_instance->page;
            //		$this->brands = &self::$view_instance->brands;
        } else {
            // Сохраняем свой инстанс в статической переменной,
            // чтобы в следующий раз использовать его
            self::$view_instance = $this;

            //Город по IP
//Hostfly
/*            $ip_data = @json_decode(file_get_contents("http://ip-api.com/json/" . $_SERVER['REMOTE_ADDR']));
            if ($ip_data){
                $this->city_select_lat = $ip_data->city;
                $this->city_region = $ip_data->regionName;
           }*/
//End Hostfly

//            $this->city_select = $this->getHumanNameCity($ip_data->city);


            // Все валюты
            $this->currencies = $this->money->get_currencies(array('enabled' => 1));

            // Выбор текущей валюты
            if ($currency_id = $this->request->get('currency_id', 'integer')) {
                $_SESSION['currency_id'] = $currency_id;
                header("Location: " . $this->request->url(array('currency_id' => null)));
            }

            // Берем валюту из сессии
            if (isset($_SESSION['currency_id'])) {
                $this->currency = $this->money->get_currency($_SESSION['currency_id']);
            } // Или первую из списка
            else {
                $this->currency = reset($this->currencies);
            }

            // Пользователь, если залогинен
            if (isset($_COOKIE['user_id'])) {
                $u = $this->users->get_user(intval($_COOKIE['user_id']));
                if ($u && $u->enabled) {
                    $this->user = $u;
                    $this->group = $this->users->get_group($this->user->group_id);
                }
            }

            // Текущая страница (если есть)
            $subdir = substr(dirname(dirname(__FILE__)), strlen($_SERVER['DOCUMENT_ROOT']));
            $page_url = trim(substr($_SERVER['REQUEST_URI'], strlen($subdir)), "/");
            if (strpos($page_url, '?') !== false) {
                $page_url = substr($page_url, 0, strpos($page_url, '?'));
            }
            $this->page = $this->pages->get_page((string)$page_url);
            $this->design->assign('page', $this->page);

            /*regions*/

            if( empty($_SESSION['region_id']) ){


                $client  = @$_SERVER['HTTP_CLIENT_IP'];
                $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
                $remote  = @$_SERVER['REMOTE_ADDR'];
                $result  = array('country'=>'', 'city'=>'');

                if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
                elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
                else $ip = $remote;

                if( $this->request->get('ip')) $ip =  $this->request->get('ip');

                function curl_get_contents($url)
                {
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $url);

                    $data = curl_exec($ch);
                    curl_close($ch);

                    return $data;
                }

//                $ip_data = @json_decode(file_get_contents("http://ipwhois.app/json/" . $ip));
/*                $ip_data = @json_decode(curl_get_contents("http://ip-api.com/json/" . $ip));



                if ($ip_data){
//                    print_r($ip_data);
                    if($ip_data && $ip_data->country != null)
                    {
                       $result = $ip_data->city;
                       $this->design->assign('dadata_city', $result);
                    }
                }*/

            }

            /* update 28.08.2020 */
            if($short_name = $this->request->get('short_name')) {


                $_SESSION['region_short_name'] = $short_name;

                $this->db->query('SELECT id FROM __regions WHERE short_name=? LIMIT 1', $short_name);
                $region_id = $this->db->result('id');

                if( $region_id ){
                    //Город совпал из магазина
                    $_SESSION['region_id'] = $region_id;

                }else{
                    unset($_SESSION['region_id']);
                }

                //обновляем остатки по регионы
                $this->cart->update_regions_stocks();

                header("Location: " . $this->request->url(array('short_name' => null)));
            }
            /* update 28.08.2020 */

            if (isset($_SESSION['region_id'])){
                $this->design->assign('region_id', $_SESSION['region_id']);
            }

            if (isset($_SESSION['region_short_name'])){
                $this->design->assign('region_short_name', $_SESSION['region_short_name']);

            }
//            var_dump($_SESSION['region_short_name']);
            /*/regions*/

            // Передаем в дизайн то, что может понадобиться в нем
            $this->design->assign('currencies', $this->currencies);
            $this->design->assign('currency', $this->currency);
            $this->design->assign('user', $this->user);
            $this->design->assign('group', $this->group);

            $this->design->assign('config', $this->config);
            $this->design->assign('settings', $this->settings);
            $this->design->assign('bonus', $this->bonus);

            //Бренд
            //	$this->design->assign('brands', $this->brands);

            //Для разных телефонов
            /*if($_SESSION["lid"]=="google"){
                $this->design->assign('phone', " +375(29)135-76-34");//Телефон для гугла
            }elseif($_SESSION["lid"]=="yandex"){
                $this->design->assign('phone', " +375(29)135-76-24");//Телефон для яндекса
            }else{
                $this->design->assign('phone', " 7255");//Телефон по умолчанию
            }*/
            $this->design->assign('phone_shop', " 7255");//Телефон по умолчанию

            // Настраиваем плагины для смарти
            $this->design->smarty->registerPlugin("function", "get_posts", array($this, 'get_posts_plugin'));
            $this->design->smarty->registerPlugin("function", "get_brands", array($this, 'get_brands_plugin'));
            $this->design->smarty->registerPlugin("function", "get_browsed_products", array($this, 'get_browsed_products'));
            $this->design->smarty->registerPlugin("function", "get_featured_products", array($this, 'get_featured_products_plugin'));
            $this->design->smarty->registerPlugin("function", "get_new_products", array($this, 'get_new_products_plugin'));
            $this->design->smarty->registerPlugin("function", "get_discounted_products", array($this, 'get_discounted_products_plugin'));
        }
    }


    public function getHumanNameCity($code)
    {
        switch ($code) {
            /*
             * Минская область
             * */
            case 'Minsk' :
                $title = 'Минск';
                break;

            case 'Barysaw' :
                $title = 'Борисов';
                break;

            case 'Salihorsk' :
                $title = 'Солигорск';
                break;

            case 'Maladzyechna':
                $title = 'Молодечно';
                break;
            case 'Zhodzina':
                $title = 'Жодино';
                break;
            case 'Slutsk':
                $title = 'Слуцк';
                break;
            case 'Vileyka':
                $title = 'Вилейка';
                break;
            case 'Dzyarzhynsk':
                $title = 'Держинск';
                break;
            case 'Stowbtsy' :
                $title = 'Столбцы';
                break;

            case 'Maryina Horka' :
                $title = 'Марьина горка';
                break;

            case 'Smalyavichy':
                $title = 'Смолевичи';
                break;
            case 'Zaslawye':
                $title = 'Заславье';
                break;
            case 'Nyasvizh':
                $title = 'Несвиж';
                break;
            case 'Fanipal':
                $title = 'Фанипаль';
                break;
            case 'Byerazino':
                $title = 'Березино';
                break;

            case 'Lyuban':
                $title = 'Любань';
                break;

            case 'Staryya Darohi':
                $title = 'Старые дорого';
                break;

            case 'Kletsk':
                $title = 'Клецк';
                break;

            case 'Lahoysk':
                $title = 'Логойск';
                break;

            case 'Valozhyn':
                $title = 'Воложин';
                break;

            case 'Kapyl':
                $title = 'Копыль';
                break;

            case 'Uzda':
                $title = 'Узда';
                break;

            case 'Krupki':
                $title = 'Крупки';
                break;

            case 'Myadzyel':
                $title = 'Мядель';
                break;
            /*
             * Витебская
             * */
            case 'Vitebsk':
                $title = 'Витебск';
                break;

            case 'Orsha' :
                $title = 'Орша';
                break;

            case 'Novopolotsk' :
                $title = 'Новополоцк';
                break;

            case 'Polotsk':
                $title = 'Полоцк';
                break;

            case 'Pastavy':
                $title = 'Поставы';
                break;
            case 'Hlybokaye':
                $title = 'Глубокое';
                break;
            case 'Lepiel':
                $title = 'Лепель';
                break;

            case 'Haradok' :
                $title = 'Городок';
                break;

            case 'Baran' :
                $title = 'Барань';
                break;

            case 'Talachyn':
                $title = 'Толочин';
                break;
            case 'Braslaw':
                $title = 'Браслав';
                break;
            case 'Chashniki':
                $title = 'Чашники';
                break;
            case 'Myory':
                $title = 'Миоры';
                break;
            case 'Dubrowna':
                $title = 'Дубровно';
                break;
            case 'Verkhnyadzvinsk' :
                $title = 'Верхнедвинск';
                break;

            case 'Dokshytsy' :
                $title = 'Докшицы';
                break;

            /*
             * Могилевская область
             * */
            case 'Mogilev':
                $title = 'Могилёв';
                break;

            case 'Babruysk':
                $title = 'Бобруйск';
                break;
            case 'Horki':
                $title = 'Горки';
                break;
            case 'Asipovichy':
                $title = 'Осиповичи';
                break;
            case 'Bykhaw':
                $title = 'Быхав';
                break;
            case 'Klimavichy':
                $title = 'Климковичи';
                break;

            case 'Shklow':
                $title = 'Шклов';
                break;

            case 'Kastsyukovichy':
                $title = 'Кастюковичи';
                break;

            case 'Mstsislaw':
                $title = 'Мстислав';
                break;

            case 'Kirawsk':
                $title = 'Кировск';
                break;

            case 'Klichaw':
                $title = 'Кличев';
                break;
            /*
             * Гомельская область
             * */

            case 'Gomel':
                $title = 'Гомель';
                break;

            case 'Mazyr':
                $title = 'Мозырь';
                break;

            case 'Zhlobin':
                $title = 'Жлобин';
                break;

            case 'Svietlahorsk':
                $title = 'Светлогорск';
                break;

            case 'Rechytsa':
                $title = 'Речица';
                break;

            case 'Kalinkavichy':
                $title = 'Калинкавичи';
                break;

            case 'Rahachow':
                $title = 'Рогачев';
                break;

            case 'Dobrush':
                $title = 'Добруж';
                break;

            case 'Zhytkavichy':
                $title = 'Житкавичи';
                break;

            case 'Khoyniki':
                $title = 'Хойники';
                break;

            case 'Pietrykaw':
                $title = 'Петриков';
                break;

            case 'Yel\'sk':
                $title = 'Ельск';
                break;

            case 'Buda-Kashalyova':
                $title = 'Буда-Кошелев';
                break;

            case 'Naroulia':
                $title = 'Наровля';
                break;

            case 'Vietka':
                $title = 'Ветка';
                break;

            case 'Chachersk':
                $title = 'Чечерск';
                break;

            /*
             * Бкестская область
             * */
            case 'Brest':
                $title = 'Брест';
                break;

            case 'Baranavichy' :
                $title = 'Барановичи';
                break;
            case 'Pinsk' :
                $title = 'Пинск';
                break;
            case 'Kobryn':
                $title = 'Кобрин';
                break;

            case 'Byaroza' :
                $title = 'Береза';
                break;

            case 'Luninets' :
                $title = 'Лунинец';
                break;

            case 'Ivatsevichy':
                $title = 'Ивацевичи';
                break;
            case 'Pruzhany':
                $title = 'Пружаны';
                break;
            case 'Ivanava':
                $title = 'Иваново';
                break;
            case 'Drahichyn':
                $title = 'Драгичин';
                break;
            case 'Hantsavichy':
                $title = 'Ганцевичи';
                break;
            case 'Zhabinka' :
                $title = 'Жабинка';
                break;

            case 'Mikashevichy' :
                $title = 'Микашевичи';
                break;

            case 'Byelaazyorsk':
                $title = 'Белоозерск';
                break;
            case 'Stolin':
                $title = 'Столин';
                break;
            case 'Lyakhavichy':
                $title = 'Ляховичи';
                break;
            case 'Kamyenyets':
                $title = 'Каменец';
                break;
            case 'Davyd-Haradok':
                $title = 'Давыд-Городок';
                break;
            case 'Vysokaye':
                $title = 'Высокое';
                break;

            /*
             * Гродненская область
             * */
            case 'Grodno':
                $title = 'Гродно';
                break;

            case 'Lida':
                $title = 'Лида';
                break;

            case 'Slonim':
                $title = 'Слоним';
                break;

            case 'Vawkavysk':
                $title = 'Волковыск';
                break;

            case 'Smarhon\'':
                $title = 'Сморгонь';
                break;

            case 'Navahrudak':
                $title = 'Новогрудок';
                break;

            case 'Masty':
                $title = 'Мосты';
                break;

            case 'Shchuchyn':
                $title = 'Щучин';
                break;

            case 'Ashmyany':
                $title = 'Ошмяны';
                break;

            case 'Skidzyel\'':
                $title = 'Скидель';
                break;

            case 'Byarozawka' :
                $title = 'Березовка';
                break;

            case 'Iwye' :
                $title = 'Ивье';
                break;

            case 'Dzyatlava':
                $title = 'Дятлово';
                break;

            case 'Svislach':
                $title = 'Свислочь';
                break;

            case 'Gol\'shany' :
                $title = 'Гольшаны';
                break;

            default:
                $title = $code;
                break;
        }

        return $title;
    }

    public function getLatNameCity($code)
    {
        switch ($code) {
            /*
             * Минская область
             * */
            case 'Минск' :
                $title = 'Minsk';
                break;

            case 'Борисов' :
                $title = 'Barysaw';
                break;

            case 'Солигорск' :
                $title = 'Salihorsk';
                break;

            case 'Молодечно':
                $title = 'Maladzyechna';
                break;
            case 'Жодино':
                $title = 'Zhodzina';
                break;
            case 'Слуцк':
                $title = 'Slutsk';
                break;
            case 'Вилейка':
                $title = 'Vileyka';
                break;
            case 'Держинск':
                $title = 'Dzyarzhynsk';
                break;
            case 'Столбцы' :
                $title = 'Stowbtsy';
                break;

            case 'Марьина горка' :
                $title = 'Maryina Horka';
                break;

            case 'Смолевичи':
                $title = 'Smalyavichy';
                break;
            case 'Заславье':
                $title = 'Zaslawye';
                break;
            case 'Несвиж':
                $title = 'Nyasvizh';
                break;
            case 'Фанипаль':
                $title = 'Fanipal';
                break;
            case 'Березино':
                $title = 'Byerazino';
                break;

            case 'Любань':
                $title = 'Lyuban';
                break;

            case 'Старые дороги':
                $title = 'Staryya Darohi';
                break;

            case 'Клецк':
                $title = 'Kletsk';
                break;

            case 'Логойск':
                $title = 'Lahoysk';
                break;

            case 'Воложин':
                $title = 'Valozhyn';
                break;

            case 'Копыль':
                $title = 'Kapyl';
                break;

            case 'Узда':
                $title = 'Uzda';
                break;

            case 'Крупки':
                $title = 'Krupki';
                break;

            case 'Мядель':
                $title = 'Myadzyel';
                break;
            /*
             * Витебская
             * */
            case 'Витебск':
                $title = 'Vitebsk';
                break;

            case 'Орша' :
                $title = 'Orsha';
                break;

            case 'Новополоцк' :
                $title = 'Novopolotsk';
                break;

            case 'Полоцк':
                $title = 'Polotsk';
                break;

            case 'Поставы':
                $title = 'Pastavy';
                break;
            case 'Глубокое':
                $title = 'Hlybokaye';
                break;
            case 'Лепель':
                $title = 'Lepiel';
                break;

            case 'Городок' :
                $title = 'Haradok';
                break;

            case 'Барань' :
                $title = 'Baran';
                break;

            case 'Толочин':
                $title = 'Talachyn';
                break;
            case 'Браслав':
                $title = 'Braslaw';
                break;
            case 'Чашники':
                $title = 'Chashniki';
                break;
            case 'Миоры':
                $title = 'Myory';
                break;
            case 'Дубровно':
                $title = 'Dubrowna';
                break;
            case 'Верхнедвинск' :
                $title = 'Verkhnyadzvinsk';
                break;

            case 'Докшицы' :
                $title = 'Dokshytsy';
                break;

            /*
             * Могилевская область
             * */
            case 'Могилёв':
                $title = 'Mogilev';
                break;

            case 'Бобруйск':
                $title = 'Babruysk';
                break;
            case 'Горки':
                $title = 'Horki';
                break;
            case 'Осиповичи':
                $title = 'Asipovichy';
                break;
            case 'Быхав':
                $title = 'Bykhaw';
                break;
            case 'Климковичи':
                $title = 'Klimavichy';
                break;

            case 'Шклов':
                $title = 'Shklow';
                break;

            case 'Кастюковичи':
                $title = 'Kastsyukovichy';
                break;

            case 'Мстислав':
                $title = 'Mstsislaw';
                break;

            case 'Кировск':
                $title = 'Kirawsk';
                break;

            case 'Кличев':
                $title = 'Klichaw';
                break;
            /*
             * Гомельская область
             * */

            case 'Гомель':
                $title = 'Gomel';
                break;

            case 'Мозырь':
                $title = 'Mazyr';
                break;

            case 'Жлобин':
                $title = 'Zhlobin';
                break;

            case 'Светлогорск':
                $title = 'Svietlahorsk';
                break;

            case 'Речица':
                $title = 'Rechytsa';
                break;

            case 'Калинкавичи':
                $title = 'Kalinkavichy';
                break;

            case 'Рогачев':
                $title = 'Rahachow';
                break;

            case 'Добруж':
                $title = 'Dobrush';
                break;

            case 'Житкавичи':
                $title = 'Zhytkavichy';
                break;

            case 'Хойники':
                $title = 'Khoyniki';
                break;

            case 'Петриков':
                $title = 'Pietrykaw';
                break;

            case 'Ельск':
                $title = 'Yel\'sk';
                break;

            case 'Буда-Кошелев':
                $title = 'Buda-Kashalyova';
                break;

            case 'Наровля':
                $title = 'Naroulia';
                break;

            case 'Ветка':
                $title = 'Vietka';
                break;

            case 'Чечерск':
                $title = 'Chachersk';
                break;

            /*
             * Бкестская область
             * */
            case 'Брест':
                $title = 'Brest';
                break;

            case 'Барановичи' :
                $title = 'Baranavichy';
                break;
            case 'Пинск' :
                $title = 'Pinsk';
                break;
            case 'Кобрин':
                $title = 'Kobryn';
                break;

            case 'Береза' :
                $title = 'Byaroza';
                break;

            case 'Лунинец' :
                $title = 'Luninets';
                break;

            case 'Ивацевичи':
                $title = 'Ivatsevichy';
                break;
            case 'Пружаны':
                $title = 'Pruzhany';
                break;
            case 'Иваново':
                $title = 'Ivanava';
                break;
            case 'Драгичин':
                $title = 'Drahichyn';
                break;
            case 'Ганцевичи':
                $title = 'Hantsavichy';
                break;
            case 'Жабинка' :
                $title = 'Zhabinka';
                break;

            case 'Микашевичи' :
                $title = 'Mikashevichy';
                break;

            case 'Белоозерск':
                $title = 'Byelaazyorsk';
                break;
            case 'Столин':
                $title = 'Stolin';
                break;
            case 'Ляховичи':
                $title = 'Lyakhavichy';
                break;
            case 'Каменец':
                $title = 'Kamyenyets';
                break;
            case 'Давыд-Городок':
                $title = 'Davyd-Haradok';
                break;
            case 'Высокое':
                $title = 'Vysokaye';
                break;

            /*
             * Гродненская область
             * */
            case 'Гродно':
                $title = 'Grodno';
                break;

            case 'Лида':
                $title = 'Lida';
                break;

            case 'Слоним':
                $title = 'Slonim';
                break;

            case 'Волковыск':
                $title = 'Vawkavysk';
                break;

            case 'Сморгонь':
                $title = 'Smarhon\'';
                break;

            case 'Новогрудок':
                $title = 'Navahrudak';
                break;

            case 'Мосты':
                $title = 'Masty';
                break;

            case 'Щучин':
                $title = 'Shchuchyn';
                break;

            case 'Ошмяны':
                $title = 'Ashmyany';
                break;

            case 'Скидель':
                $title = 'Skidzyel\'';
                break;

            case 'Березовка' :
                $title = 'Byarozawka';
                break;

            case 'Ивье' :
                $title = 'Iwye';
                break;

            case 'Дятлово':
                $title = 'Dzyatlava';
                break;

            case 'Свислочь':
                $title = 'Svislach';
                break;

            case 'Гольшаны' :
                $title = 'Gol\'shany';
                break;

            default:
                $title = $code;
                break;
        }

        return $title;
    }

    public function getRegionCity($code){
        switch ($code){
            case 'Grodnenskaya' :
                $title = 'Гродно';
                break;
            case 'Vitebskaya' :
                $title = 'Витебск';
                break;
            case 'Brestskaya' :
                $title = 'Брест';
                break;
            case 'Gomel\'skaya':
                $title = 'Гомель';
                break;
        }

        return $title;
    }

    /**
     *
     * Отображение
     *
     */
    public function fetch()
    {
        return false;
    }

    /**
     *
     * Плагины для смарти
     *
     */
    public function get_posts_plugin($params, &$smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        if (!empty($params['var'])) {
            $smarty->assign($params['var'], $this->blog->get_posts($params));
        }
    }

    public function get_brands_plugin($params, $smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        if (!empty($params['var'])) {
            $smarty->assign($params['var'], $this->brands->get_brands($params));
        }
    }

    public function get_browsed_products($params, &$smarty)
    {
        if (!empty($_COOKIE['browsed_products'])) {
            $browsed_products_ids = explode(',', $_COOKIE['browsed_products']);
            $browsed_products_ids = array_reverse($browsed_products_ids);
            if (isset($params['limit'])) {
                $browsed_products_ids = array_slice($browsed_products_ids, 0, $params['limit']);
            }

            $products = $this->products->renders(array('id' => $browsed_products_ids, 'visible' => 1));


            $smarty->assign($params['var'], $products);
        }
    }


    public function get_featured_products_plugin($params, &$smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        $params['featured'] = 1;
        if (!empty($params['var'])) {
            if (isset($_SESSION['region_id'])){
                $params['region'] = $_SESSION['region_id'];
            }

            $products = $this->products->renders($params);

            $smarty->assign($params['var'], $products);
        }
    }


    public function get_new_products_plugin($params, &$smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        if (!isset($params['sort'])) {
            $params['sort'] = 'created';
        }
        if (!empty($params['var'])) {
            $products = $this->products->renders($params);

            $smarty->assign($params['var'], $products);
        }
    }


    public function get_discounted_products_plugin($params, &$smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        $params['discounted'] = 1;
        if (!empty($params['var'])) {

            if (isset($_SESSION['region_id'])){
                $params['region'] = $_SESSION['region_id'];
            }

            $products = $this->products->renders($params);

            $smarty->assign($params['var'], $products);
        }
    }
}
