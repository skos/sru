<?php
class UFview_SruAdmin_Admins
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdmins());
		$this->append('body', $box->admins());
	}
}

?>