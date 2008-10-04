<?
/**
 * brak ukprawnien
 */
class UFview_Sru_Error403
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleError403());
		$this->append('body', $box->error403());
	}
}
