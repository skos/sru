<?php
/**
 * zmiana admina
 */
class UFview_SruAdmin_AdminEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdminEdit());
		$this->append('body',  $box->adminEdit());
	}
}
