<?
/**
 * dane komputera
 */
class UFview_SruAdmin_Computer
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputer());
		$this->append('body', $box->computer());
	}
}
