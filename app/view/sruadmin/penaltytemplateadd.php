<?php
/**
 * dodanie szablonu dla kary
 */
class UFview_SruAdmin_PenaltyTemplateAdd
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyTemplateAdd());
		$this->append('body',  $box->penaltyTemplateAdd());
	}
}
