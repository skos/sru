<?php
/**
 * dane akademika
 */
class UFview_SruAdmin_Dorm
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleDorm());
		$this->append('body', $box->dorm());

	}
}
