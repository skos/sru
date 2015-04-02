<?php
/**
 * zmiana portów switcha
 */
class UFview_SruAdmin_SwitchLockoutsEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitchEdit());
		$this->append('body', $box->switchDetails());
		$this->append('body',  $box->switchLockoutsEdit());
	}
}
