<?php
class UFview_SruAdmin_StatsUsers
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsUsers());
		$this->append('body', $box->statsUsers());
	}
}
