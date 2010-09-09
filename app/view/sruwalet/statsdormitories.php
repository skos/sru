<?php
class UFview_SruWalet_StatsDormitories
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsDormitories());
		$this->append('body', $box->statsDormitories());
	}
}
