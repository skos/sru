<?
/**
 * wyposazenie
 */
class UFbean_SruAdmin_InventoryCardList
extends UFbeanList {
	public function search(array $params) {
		$this->data = $this->dao->search($params);
	}
}
