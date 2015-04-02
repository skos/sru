<?
/**
 * widok wydruku
 */
class UFview_SruPrint
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexPrint', $this->_srv);
	}

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$this->append('title', $box->title());
	}
}
