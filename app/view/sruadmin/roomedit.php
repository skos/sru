<?php
/**
 * edytowanie komentarzy do pokoju
 */
class UFview_SruAdmin_RoomEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleRoom());
		$this->append('body',  $box->room());
		$this->append('body',  $box->roomEdit());
	}
}
