<?php
class UFview_SruWalet_Inventory
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleInventory());
		$this->append('body', $box->inventory());
	}
}
