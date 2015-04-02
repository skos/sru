<?php
/**
 * zalozenie kary
 */
class UFview_SruAdmin_PenaltyAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyAdd());
		$this->append('body',  $box->penaltyAdd());
	}
}
