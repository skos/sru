<?php
class UFview_SruAdmin_SwitchPortMacs
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleSwitch());
		$this->append('body', $box->switchDetails());
		$this->append('body', $box->switchPorts());
		$this->append('body', $box->switchPortDetails());
		$this->append('body', $box->switchPortMacs());
	}
}
