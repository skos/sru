<?
/**
 * panstwo
 */
class UFdao_SruWalet_Country
extends UFdao {

	public function quickSearch($params) {
		$key = $this->cachePrefix.'/'.__FUNCTION__.'/'.print_r($params, true);
		try {
			return $this->cacheGet($key);
		} catch (UFex_Core_DataNotFound $e) {
			$mapping = $this->mapping('search');

			$query = $this->prepareSelect($mapping);
			$query->order($mapping->nationalitySearch, $query->ASC);
			foreach ($params as $var=>$val) {
				switch ($var) {
					case 'nationality':
						$val = str_replace('%', '', $val);
						$val = str_replace('*', '%', $val);
						$query->where($var.'Search', $val, UFlib_Db_Query::LIKE);
						break;
					default:
						$query->where($var, $val);
				}
			}

			$return = $this->doSelect($query);
			$this->cacheSet($key, $return);
			return $return;
		}
	}

	public function getByName($name) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->nationalitySearch, $name);

		return $this->doSelectFirst($query);
	}

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->nationality, $query->ASC);

		return $this->doSelect($query);
	}
}
