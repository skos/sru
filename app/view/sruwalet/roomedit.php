<?php
/**
 * edytowanie pokoju
 */
class UFview_SruWalet_RoomEdit
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleRoom());
		$this->append('body',  $box->roomEdit());
	}
}
