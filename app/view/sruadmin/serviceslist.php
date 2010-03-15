<?php
class UFview_SruAdmin_ServicesList
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleServices());
		$this->append('body', $box->servicesList());
	}
}
