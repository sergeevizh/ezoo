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

class Blog extends Simpla
{

    /**
     * Функция возвращает пост по его id или url
     * (в зависимости от типа аргумента, int - id, string - url)
     *
     * @param  int $id
     * @return object|bool
     */
    public function get_post($id)
    {
        if (is_int($id)) {
            $where = $this->db->placehold(' WHERE b.id=? ', intval($id));
        } else {
            $where = $this->db->placehold(' WHERE b.url=? ', $id);
        }

        $query = $this->db->placehold("SELECT b.id, b.url, b.name, b.annotation, b.text, b.meta_title, b.meta_keywords, b.meta_description, b.visible, b.date
										FROM __blog b
											$where
										LIMIT 1");
        if ($this->db->query($query)) {
            return $this->db->result();
        } else {
            return false;
        }
    }

    /**
     * Функция возвращает массив постов, удовлетворяющих фильтру
     *
     * @param array $filter
     * @return array|bool
     */
    public function get_posts($filter = array())
    {
        // По умолчанию
        $limit = 1000;
        $page = 1;
        $post_id_filter = '';
        $visible_filter = '';
        $keyword_filter = '';
        $year_filter = '';
        $month_filter = '';

        if (isset($filter['limit'])) {
            $limit = max(1, intval($filter['limit']));
        }

        if (isset($filter['page'])) {
            $page = max(1, intval($filter['page']));
        }

        if (!empty($filter['id'])) {
            $post_id_filter = $this->db->placehold('AND b.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND b.visible = ?', intval($filter['visible']));
        }

        if (!empty($filter['year'])) {
            $year_filter = $this->db->placehold('AND YEAR (b.date) = ?', intval($filter['year']));
        }

        if (!empty($filter['month'])) {
            $month_filter = $this->db->placehold('AND MONTH (b.date) = ?', intval($filter['month']));
        }

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (b.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR b.meta_keywords LIKE "%' . $this->db->escape(trim($keyword)) . '%") ');
            }
        }

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        $query = $this->db->placehold("SELECT b.id, b.url, b.name, b.annotation, b.text, b.meta_title, b.meta_keywords, b.meta_description, b.visible, b.date
										FROM __blog b
										WHERE 1
											$post_id_filter
											$visible_filter
											$year_filter
											$month_filter
											$keyword_filter
										ORDER BY date DESC, id DESC
										$sql_limit");

        $this->db->query($query);
        return $this->db->results();
    }

    /**
     * Функция вычисляет количество постов, удовлетворяющих фильтру
     *
     * @param  array $filter
     * @return int|bool
     */
    public function count_posts($filter = array())
    {
        $post_id_filter = '';
        $visible_filter = '';
        $keyword_filter = '';
        $year_filter = '';
        $month_filter = '';

        if (!empty($filter['id'])) {
            $post_id_filter = $this->db->placehold('AND b.id in(?@)', (array)$filter['id']);
        }

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND b.visible = ?', intval($filter['visible']));
        }

        if (!empty($filter['year'])) {
            $year_filter = $this->db->placehold('AND YEAR (b.date) = ?', intval($filter['year']));
        }

        if (!empty($filter['month'])) {
            $month_filter = $this->db->placehold('AND MONTH (b.date) = ?', intval($filter['month']));
        }

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $keyword_filter .= $this->db->placehold('AND (b.name LIKE "%' . $this->db->escape(trim($keyword)) . '%" OR b.meta_keywords LIKE "%' . $this->db->escape(trim($keyword)) . '%") ');
            }
        }

        $query = $this->db->placehold("SELECT COUNT(distinct b.id) as count
										FROM __blog b
										WHERE 1
											$post_id_filter
											$visible_filter
											$year_filter
											$month_filter
											$keyword_filter");

        if ($this->db->query($query)) {
            return $this->db->result('count');
        } else {
            return false;
        }
    }

    /**
     * @param  object $post
     * @return bool|int
     */
    public function add_post($post)
    {
        if (!isset($post->date)) {
            $date_query = ', date=NOW()';
        } else {
            $date_query = '';
        }

        $query = $this->db->placehold("INSERT INTO __blog SET ?% $date_query", $post);

        if (!$this->db->query($query)) {
            return false;
        } else {
            return $this->db->insert_id();
        }
    }

    /**
     * Обновить пост(ы)
     *
     * @param  int $id
     * @param  array|object $post
     * @return int
     */
    public function update_post($id, $post)
    {
        $query = $this->db->placehold("UPDATE __blog SET ?% WHERE id in(?@) LIMIT ?", $post, (array)$id, count((array)$id));
        $this->db->query($query);
        return $id;
    }

    /**
     * Удалить пост
     *
     * @param  int $id
     * @return bool
     */
    public function delete_post($id)
    {
        if (!empty($id)) {

            $this->delete_image($id);

            $query = $this->db->placehold("DELETE FROM __blog WHERE id=? LIMIT 1", intval($id));
            if ($this->db->query($query)) {
                $query = $this->db->placehold("DELETE FROM __comments WHERE type='blog' AND object_id=?", intval($id));
                if ($this->db->query($query)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function delete_image($id)
    {
        $query = $this->db->placehold("SELECT image FROM __blog WHERE id=?", intval($id));
        $this->db->query($query);
        $filename = $this->db->result('image');
        if (!empty($filename)) {
            $query = $this->db->placehold("UPDATE __blog SET image=NULL WHERE id=?", $id);
            $this->db->query($query);

            $query = $this->db->placehold("SELECT count(*) as count FROM __blog WHERE image=? LIMIT 1", $filename);
            $this->db->query($query);

            $count = $this->db->result('count');
            if ($count == 0) {
                @unlink($this->config->root_dir . $this->config->blog_images_dir . $filename);
            }
        }
    }


    public function get_years($filter = array())
    {

        $visible_filter = '';
        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND b.visible = ?', intval($filter['visible']));
        }

        $this->db->query("SELECT DISTINCT YEAR (b.date) as year
						FROM __blog b
						WHERE 1
						$visible_filter
						ORDER BY year
						DESC");

        return $this->db->results('year');
    }

    public function get_months($filter = array())
    {

        $visible_filter = '';
        $year_filter = '';

        if (isset($filter['visible'])) {
            $visible_filter = $this->db->placehold('AND b.visible = ?', intval($filter['visible']));
        }

        if (!empty($filter['year'])) {
            $year_filter = $this->db->placehold('AND YEAR (b.date) = ?', intval($filter['year']));
        }

        $this->db->query("SELECT DISTINCT MONTH (b.date) as month
						FROM __blog b
						WHERE 1
						$visible_filter
						$year_filter
						ORDER BY month
						DESC");

        return $this->db->results('month');
    }


    /**
     * Следующий пост
     *
     * @param  int $id
     * @return object|bool
     */
    public function get_next_post($id)
    {
        $this->db->query("SELECT date FROM __blog WHERE id=? LIMIT 1", $id);
        $date = $this->db->result('date');

        $this->db->query("(SELECT id FROM __blog WHERE date=? AND id>? AND visible  ORDER BY id limit 1)
								UNION
							(SELECT id FROM __blog WHERE date>? AND visible ORDER BY date, id limit 1)",
            $date, $id, $date);
        $next_id = $this->db->result('id');
        if ($next_id) {
            return $this->get_post(intval($next_id));
        } else {
            return false;
        }
    }

    /**
     * Предыдущий пост
     *
     * @param  int $id
     * @return object|bool
     */
    public function get_prev_post($id)
    {
        $this->db->query("SELECT date FROM __blog WHERE id=? LIMIT 1", $id);
        $date = $this->db->result('date');

        $this->db->query("(SELECT id FROM __blog WHERE date=? AND id<? AND visible ORDER BY id DESC limit 1)
								UNION
							(SELECT id FROM __blog WHERE date<? AND visible ORDER BY date DESC, id DESC limit 1)",
            $date, $id, $date);
        $prev_id = $this->db->result('id');
        if ($prev_id) {
            return $this->get_post(intval($prev_id));
        } else {
            return false;
        }
    }
}
