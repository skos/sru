<?
/**
 * edycja kary
 */
class UFview_SruAdmin_PenaltyEdit
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenaltyEdit());
		$this->append('body', $box->penaltyEdit());
	}
}
