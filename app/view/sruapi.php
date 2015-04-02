<?
/**
 * widok api
 */
class UFview_SruApi
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexApi', $this->_srv);
	}

	public function fillData() {
	}
}
