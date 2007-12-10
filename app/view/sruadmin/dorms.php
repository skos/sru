<?php
class UFview_SruAdmin_Dorms
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleDormitories());
		
		$this->append('body', $box->dorms());
	}
}
