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

	}
}