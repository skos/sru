<?php
/**
 * pokazanie szablonów kar
 */
class UFview_SruAdmin_PenaltyTemplates
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyTemplates());
		$this->append('body',  $box->penaltyTemplates());
	}
}
