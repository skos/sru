<?
/**
 * historia kary
 */
class UFview_SruAdmin_PenaltyHistory
extends UFview_SruAdmin {

	public function fillData() {
		$box = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titlePenalty());
		$this->append('body', $box->penalty());
		$this->append('body', $box->penaltyHistory());
	}
}
