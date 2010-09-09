<?php
class UFview_SruWalet_Inhabitants
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleInhabitants());
		$this->append('body', $box->inhabitants());
	}
}
