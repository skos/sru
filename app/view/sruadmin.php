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
		$acl = $this->_srv->get('acl');

		if (!isset($this->data['menuAdmin'])) {
			$box = UFra::shared('UFbox_SruAdmin');
			$this->append('menuAdmin', $box->menuAdmin());
		}
		if (!isset($this->data['adminBar'])) { 
			$box = UFra::shared('UFbox_SruAdmin');
			$this->append('adminBar', $box->adminBar());
		}		

		// logi
		if ($acl->core('log', 'ufraShow')) {
			$bLogs = UFra::shared('UFbox_Log');
			$this->data['logs'] = $bLogs->full();
		} else {
			$this->data['logs'] = '';
		}
	}

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->title());
		//$this->append('body', $box->login());
	}
}
