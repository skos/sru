<?
/**
 * nie znaleziono strony
 */
class UFview_Sru_Error404
extends UFview {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleError404());
		$this->append('body', $box->error404());
	}
}
