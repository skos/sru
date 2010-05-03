<?php
/**
 * zmiana switcha
 */
class UFview_SruAdmin_SwitchEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleSwitchEdit());
		$this->append('body',  $box->switchEdit());
	}
}
