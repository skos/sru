<?php
class UFview_SruAdmin_StatsComputers
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsComputers());
		$this->append('body', $box->statsComputers());
	}
}
