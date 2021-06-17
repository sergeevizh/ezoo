<?php

/**
 * Simpla CMS
 *
 * @copyright	2016 Denis Pikusov
 * @link		http://simplacms.ru
 * @author		Denis Pikusov
 *
 */

require_once('../../api/Simpla.php');

class ExportPhonesAjax extends Simpla
{
    private $columns_names = array(
        'name'            =>    'Имя',
        'email'            =>    'Email',
        'phone'    =>    'Телефон',
        'date'        =>    'Дата',
        'id'        =>    'Заказ №',
        'product'        =>    'Товарные позиции',
        'total_price'        =>    'Сумма заказа',
        'status'         =>     'Статус заказа',
        'labels'         => 'Метки',
    );

    private $column_delimiter = ';';
    private $phones_count = 10;
    private $export_files_dir = '../files/export_phones/';
    private $filename = 'phones.csv';

    public function fetch()
    {

        if (!$this->managers->access('phones')) {
            return array('error' => 'Permission denied');
        }

        // Эксель кушает только 1251
        $this->db->query('SET NAMES cp1251');

        // Страница, которую экспортируем
        $page = $this->request->get('page', 'integer');
        if (empty($page) || $page == 1) {
            $page = 1;
            // Если начали сначала - удалим старый файл экспорта
            if (is_writable($this->export_files_dir . $this->filename)) {
                unlink($this->export_files_dir . $this->filename);
            }
        }

        // Открываем файл экспорта на добавление
        $f = fopen($this->export_files_dir . $this->filename, 'ab');

        foreach ($this->columns_names as $key => $value) {
            $this->columns_names[$key] = $this->convert_str_encoding($value, 'windows-1251', 'UTF-8', $key);
        }

        // Если начали сначала - добавим в первую строку названия колонок
        if ($page == 1) {
            fputcsv($f, $this->columns_names, $this->column_delimiter);
        }

        $date_end = $this->request->get('date_end');
        $date_start = $this->request->get('date_start');
        $registration = $this->request->get('registration');
        $contr_name = $this->request->get('contr_name');
        $contr_phone = $this->request->get('contr_phone');

        $filter = array();
        $filter['page'] = $page;
        $filter['limit'] = 20;

        if ($sort = $this->request->get('sort', 'string')) {
            $_SESSION['users_admin_sort'] = $sort;
        }
        if (!empty($_SESSION['users_admin_sort'])) {
            $filter['sort'] = $_SESSION['users_admin_sort'];
        } else {
            $filter['sort'] = 'name';
        }

        if (!empty($date_start)) {
            $filter['date_start'] = $date_start;
        }

        if (!empty($date_end)) {
            $filter['date_end'] = $date_end;
        }

        if (!empty($registration)) {
            $filter['registration'] = $registration;
        }

        if (mb_convert_encoding($this->request->get('contr_name'), "windows-1251", "auto") != ''){
            $filter['contr_name'] = mb_convert_encoding($this->request->get('contr_name'), "windows-1251", "auto");
        }

        if ($this->request->get('contr_phone') != ''){
            $filter['contr_phone'] = $this->request->get('contr_phone');
        }

        // Выбираем пользователей
        foreach ($this->phones->get_phones($filter) as $p) {

            $str = array();
            switch ($p->status){
                case '0':{
                    $p->status = "В обработке";
                }
                    break;
                case '1':{
                    $p->status = "Принят";
                }
                    break;
                case '2':{
                    $p->status = "Выполнен";
                }
                    break;
                case '3':{
                    $p->status = "Удален";
                }
                    break;
            }
            $p->status = $this->convert_str_encoding($p->status, 'windows-1251', 'UTF-8');
            $p->total_price = str_replace('.', ',', $p->total_price);
            foreach ($this->columns_names as $n => $c) {
                $str[] = $p->$n;
            }

            fputcsv($f, $str, $this->column_delimiter);
        }



        if ($filter['contr_name'] || $filter['contr_phone']){
            $total_price = $this->phones->get_sum_phones($filter);
            $total_price = str_replace('.', ',', $total_price);
            $total_column = array('', '', '', '', '', 'Итого', $total_price[0]);
            foreach ($total_column as $key => $value) {
                $total_column[$key] = $this->convert_str_encoding($value, 'windows-1251', 'UTF-8', $key);
            }
            fputcsv($f, $total_column, $this->column_delimiter);
        }


        $total_phones = $this->phones->count_phones($filter);

        fclose($f);

        if ($this->phones_count * $page < $total_phones) {
            return array('end' => false, 'page' => $page, 'totalpages' => $total_phones / $this->phones_count);
        } else {
            return array('end' => true, 'page' => $page, 'totalpages' => $total_phones / $this->phones_count);
        }


    }
}

$export_ajax = new ExportPhonesAjax();
$json = json_encode($export_ajax->fetch());
header("Content-type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");
print $json;
