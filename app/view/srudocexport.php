<?
/**
 * widok exportu do doca
 */
class UFview_SruDocExport
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexDocExport', $this->_srv);
	}

	public function fillData() {
	}
}
