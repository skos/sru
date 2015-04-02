<?php
class UFview_SruAdmin_Penalties
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titlePenalties());
		$this->append('body', $box->penalties());
		

	}
}
