<?php
class UFview_SruAdmin_Admins
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleAdmins());
		$this->append('body', $box->admins());
		
		if($acl->sruAdmin('admin', 'advancedEdit'))
		{
			$this->append('body', $box->bots());									
			$this->append('body', $box->inactiveAdmins());
		}
	}
}
