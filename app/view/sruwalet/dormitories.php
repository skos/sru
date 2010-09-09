<?php
class UFview_SruWalet_Dormitories
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleDormitories());
		$this->append('body', $box->dormitories());
	}
}
