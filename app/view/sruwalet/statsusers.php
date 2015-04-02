<?php
class UFview_SruWalet_StatsUsers
extends UFview_SruWalet {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$acl = $this->_srv->get('acl');

		$this->append('title', $box->titleStatsUsers());
		$this->append('body', $box->statsUsers());
	}
}
