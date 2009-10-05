<?php
class UFview_SruAdmin_Migration
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleMigration());
		$this->append('body', $box->migration());
	}
}
