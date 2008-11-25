<?php
/**
 * dane kary
 */
class UFview_SruAdmin_PenaltyActions
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyActions());
		$this->append('body', $box->penaltyActions());

	}
}
