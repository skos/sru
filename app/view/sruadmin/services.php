<?php
class UFview_SruAdmin_Services
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleServices());
		$this->append('body', $box->servicesEdit());
		

	}
}
