<?php
class UFview_SruAdmin_Inventory
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleInventory());
		$this->append('body', $box->inventory());
	}
}
