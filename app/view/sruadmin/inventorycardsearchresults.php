<?
/**
 * wyszukiwanie urzadzen
 */
class UFview_SruAdmin_InventoryCardSearchResults
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleInventoryCardSearch());
		$this->append('body', $box->inventoryCardSearch());
		$this->append('body', $box->inventoryCardSearchResults());
		$this->append('body', $box->inventoryCardSearchHistoryResults());
	}
}
