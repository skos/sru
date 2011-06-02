<?php
/**
 * dane administratora Waleta
 */
class UFview_SruWalet_Admin
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleAdmin());
		$this->append('body', $box->admin());
		$this->append('body', $box->adminDorms());
		$this->append('body', $box->adminHosts());
		$this->append('body', $box->adminUsersModified());
	}
}
