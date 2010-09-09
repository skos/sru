<?php
/**
 * obsadzenie akademika
 */
class UFview_SruWalet_Dorm
extends UFview_SruWalet {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->titleDorm());
		$this->append('body', $box->dorm());

	}
}
