<?php
class UFview_SruAdmin_Ips
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleIps());
		$this->append('body', $box->ips());
	}
}
