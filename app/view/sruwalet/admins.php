<?php
class UFview_SruWalet_Admins
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleAdmins());
		$this->append('body', $box->admins());
		$this->append('body', $box->sruAdmins());
		
		if($acl->sruWalet('admin', 'advancedEdit')) {
			$this->append('body', $box->inactiveAdmins());
		}
	}
}
