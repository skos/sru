<?php
/**
 * dane administratora
 */
class UFview_SruAdmin_Admin
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdmin());
		$this->append('body', $box->admin());
		$this->append('body', $box->adminDutyHours());
		$this->append('body', $box->adminDorms());
		$this->append('body', $box->adminHosts());
		$this->append('body', $box->adminPenaltiesAdded());
		$this->append('body', $box->adminWarningsAdded());
		$this->append('body', $box->adminPenaltiesModified());
		$this->append('body', $box->adminUsersModified());
		$this->append('body', $box->adminComputersModified());
	}
}
