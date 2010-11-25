<?php
class UFview_SruAdmin_StatsTransfer
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsTransfer());
		$this->append('body', $box->statsTransfer());
	}
}
