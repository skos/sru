<?php
class UFview_SruWalet_StatsUsersDocExport
extends UFview_SruDocExport {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleStatsUsersExport());
		$this->append('body', $box->statsUsersExport());
	}
}
