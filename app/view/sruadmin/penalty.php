<?php
/**
 * dane kary
 */
class UFview_SruAdmin_Penalty
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenalty());
		$this->append('body', $box->penalty());

	}
}
