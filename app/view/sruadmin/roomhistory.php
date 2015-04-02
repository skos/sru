<?php
/**
 * dane pokoju
 */
class UFview_SruAdmin_RoomHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleRoom());
		$this->append('body',  $box->room());
		$this->append('body',  $box->roomHistory());
	}
}
