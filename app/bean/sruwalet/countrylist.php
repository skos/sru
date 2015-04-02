<?
/**
 * panstwa
 */
class UFbean_SruWalet_CountryList
extends UFbeanList {

	public function quickSearch(array $params) {
		$this->data = $this->dao->quickSearch($params);
	}
}
