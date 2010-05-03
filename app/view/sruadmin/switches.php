<?php
class UFview_SruAdmin_Switches
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleSwitches());
		$this->append('body', $box->switches());
	}
}
