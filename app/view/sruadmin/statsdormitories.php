<?php
class UFview_SruAdmin_StatsDormitories
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsDormitories());
		$this->append('body', $box->statsDormitories());
	}
}
