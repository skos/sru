<?
/**
 * widok exportu do xlsa
 */
class UFview_SruXlsExport
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexXlsExport', $this->_srv);
	}

	public function fillData() {
	}
}
