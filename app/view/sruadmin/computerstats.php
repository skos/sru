<?
/**
 * statystyki komputera
 */
class UFview_SruAdmin_ComputerStats
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputer());
		$this->append('body', $box->computer());
		$this->append('body', $box->computerStats());
	}
}
