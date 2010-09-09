<?php
/**
 * zmiana admina Waleta
 */
class UFview_SruWalet_AdminEdit
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleAdminEdit());
		$this->append('body',  $box->adminEdit());
	}
}
