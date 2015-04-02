<?php
class UFview_SruAdmin_AdminsActive
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleAdmins());
		$this->append('body', $box->adminsActive());
	}
}
