<?
/**
 * widok zdministracyjny
 */
class UFview_SruAdmin
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexAdmin', $this->_srv);
	}

	protected function fillDefaultData() {
		if (!isset($this->data['menuAdmin'])) {
			$box = UFra::shared('UFbox_SruAdmin');
			$this->append('menuAdmin', $box->menuAdmin());
		}
	}

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->title());
		//$this->append('body', $box->login());
	}
}
