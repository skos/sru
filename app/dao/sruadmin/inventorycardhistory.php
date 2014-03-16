<?
/**
 * historia karty wyposazenia
 */
class UFdao_SruAdmin_InventoryCardHistory
extends UFdao {

	public function listByInventoryCardId($id, $count=null) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->inventoryCardId, $id);
		$query->order($mapping->pkName(), $query->DESC);
		if (is_int($count)) {
			$query->limit($count);
		}

		return $this->doSelect($query);
	}
}
