<?php
/**
 * edycja szablonu dla kary
 */
class UFview_SruAdmin_PenaltyTemplateEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyTemplateEdit());
		$this->append('body',  $box->penaltyTemplateEdit());
	}
}
