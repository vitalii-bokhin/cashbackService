<?php
class Coupons extends Core
{
	private $_page;
	protected $_itemsquantity;
	protected $_category_id;
	protected $_type;

	protected function getContent($query)
	{
		$cat = array();

		// params
		if (!empty($this->_alias)) {
			$category = $this->db->prepare('SELECT * FROM categories WHERE alias=? AND relation=?');
			$category->execute(array($this->_alias, 'coupons'));
			$category_arr = $category->fetch(PDO::FETCH_ASSOC);

			$this->_category_id = $category_arr['id'];

			if (empty($this->_category_id)) {
				$this->_page_not_found = true;
			}

			if (!empty($this->_region)) {
				$cat[] = '%"' . $category_arr['id'] . '"%';
				$cat[] = 1;
				$cat[] = 1;

				$par = 'category_ids LIKE ? AND ' . $this->_region . '_reg=? AND available=?';
			} else {
				$cat[] = '%"' . $category_arr['id'] . '"%';
				$cat[] = 1;
				$cat[] = 1;
				$cat[] = 1;
				$cat[] = 1;

				$par = 'category_ids LIKE ? AND by_reg=? AND ru_reg=? AND ua_reg=? AND available=?';
			}
		} else {
			if (!empty($this->_region)) {
				$cat[] = 1;
				$cat[] = 1;

				$par = $this->_region . '_reg=? AND available=?';
			} else {
				$cat[] = 1;
				$cat[] = 1;
				$cat[] = 1;
				$cat[] = 1;

				$par = 'by_reg=? AND ru_reg=? AND ua_reg=? AND available=?';
			}
		}

		// type
		$this->_type = $query['type'];

		switch ($query['type']) {
			case 'free-shipping':
				$cat[] = '["1"]';

				$par .= ' AND type_ids=?';
				break;

			case 'discounts':
				$cat[] = '%"2"%';

				$par .= ' AND type_ids LIKE ?';
				break;

			case 'gifts':
				$cat[] = '%"3"%';

				$par .= ' AND type_ids LIKE ?';
				break;
		}

		// page num
		$this->_page = $query['page'];

		if (empty($this->_page)) {
			$page = 1;
		} else {
			$page = $this->_page;
		}

		$page = ($page - 1) * 24;

		// sort params
		$sorting_result = $this->getSorting();
		$sorting = $sorting_result['sql'];
		$result['sorting'] = $sorting_result['param'];

		// quantity
		$sql_count = $this->db->prepare('SELECT COUNT(*) FROM coupons WHERE ' . $par . $sorting);
		$sql_count->execute($cat);
		$this->_itemsquantity = $sql_count->fetchColumn();

		// get coupons
		$coupons_sql = $this->db->prepare('SELECT * FROM coupons WHERE ' . $par . $sorting . ' LIMIT ' . $page . ', 24');
		$coupons_sql->execute($cat);
		$result['coupons'] = $coupons_sql->fetchAll(PDO::FETCH_ASSOC);

		// get shops
		$shops_sql = $this->db->prepare('SELECT * FROM shops');
		$shops_sql->execute();
		$shops = $shops_sql->fetchAll(PDO::FETCH_ASSOC);

		foreach ($shops as $shop) {
			$result['shops'][$shop['id']] = $shop;
		}

		return $result;
	}

	protected function getPagenav()
	{
		$base = $this->_type;

		if (!empty($this->_alias)) {
			$alias = '/' . $this->_alias;
		} else {
			$alias = '';
		}

		if (empty($this->_page)) {
			$page = 1;
		} else {
			$page = $this->_page;
		}

		$pages = ceil($this->_itemsquantity / 24);

		if ($this->_itemsquantity > 24) {
			// prev link
			if ($page > 1) {
				if ($page == 2) {
					$pagination = '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '" class="paginate__prev"></a></li>';
				} else {
					$pagination = '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '/page/' . ($page - 1) . '" class="paginate__prev"></a></li>';
				}
			} else {
				$pagination = '<li class="paginate__item"><span class="paginate__prev paginate__prev_disabled"></span></li>';
			}

			// page links
			if ($pages <= 5) {
				$pag = $pages;
				$i = 1;
			} else {
				$pag = 5;
				$i = 1;

				if ($page > 3) {
					$i = $page - 2;
					$pag = $page + 2;
				}

				if ($page > ($pages - 2)) {
					$i = $pages - 4;
					$pag = $pages;
				}
			}

			if ($pages > 5 && $page > 3) {
				$dots = '';

				if ($page > 4 && $pages != 6) {
					$dots = '<li class="paginate__item">...</li>';
				}

				$pagination .= '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '" title="1-я страница" class="paginate__a">1</a></li>' . $dots;
			}

			for ($i; $i <= $pag; $i++) {
				if ($page == $i) {
					$pagination .= '<li class="paginate__item"><span class="paginate__curr">' . $i . '</span></li>';
				} else {
					if ($i == 1) {
						$pagination .= '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '" title="1-я страница" class="paginate__a">1</a></li>';
					} else {
						$pagination .= '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '/page/' . $i . '" title="' . $i . '-я страница" class="paginate__a">' . $i . '</a></li>';
					}
				}
			}

			if ($pages > 5 && $page < ($pages - 2)) {
				$dots = '';

				if ($page != ($pages - 3) && $pages != 6) {
					$dots = '<li class="paginate__item">...</li>';
				}

				$pagination .= $dots . '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '/page/' . $pages . '" title="' . $pages . '-я страница" class="paginate__a">' . $pages . '</a></li>';
			}

			// next link
			if ($page < $pages) {
				$pagination .= '<li class="paginate__item"><a rel="nofollow" href="/' . $base . $alias . '/page/' . ($page + 1) . '" class="paginate__next"></a></li>';
			} else {
				$pagination .= '<li class="paginate__item"><span class="paginate__next paginate__next_disabled"></span></li>';
			}
		}

		return $pagination;
	}

	private function getSorting()
	{
		$result = array();

		if ($this->_type == 'discounts') {
			if (isset($_POST['sorting'])) {
				$result['param'] = $_SESSION['sorting'] = $_POST['sorting'];

				$this->_page = 1;
			} else {
				if (isset($_SESSION['sorting'])) {
					$result['param'] = $_SESSION['sorting'];
				} else {
					$result['param'] = 'rating';
				}
			}

			if (!$this->_page) {
				unset($_SESSION['sorting']);
				$result['param'] = 'rating';
			}

			switch ($result['param']) {
				case 'rating':
					$result['sql'] = ' ORDER BY rating DESC';
					break;

				case 'biggest_discounts':
					$result['sql'] = ' ORDER BY discount_abs DESC';
					break;

				case 'newest':
					$result['sql'] = ' ORDER BY date_start DESC';
					break;

				case 'expire_soon':
					$result['sql'] = ' AND date_end > 0 AND date_end > NOW() ORDER BY date_end ASC';
					break;

				default:
					$result['sql'] = ' ORDER BY discount_abs DESC';
					break;
			}
		} else {
			$result['sql'] = ' ORDER BY rating DESC';
		}

		return $result;
	}

	protected function delDiscount($id)
	{
		$del = $this->db->prepare('DELETE FROM coupons WHERE id=?');
		$del->execute(array($id));
	}
}
