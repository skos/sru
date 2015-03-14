<?
/**
 * widok uzytkownika
 */
class UFview_SruUser
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexSru', $this->_srv);
	}

	protected function fillDefaultData() {
		$acl = $this->_srv->get('acl');
		
		if (!isset($this->data['userMainManu'])) {
			$box = UFra::shared('UFbox_Sru');
			$this->append('userMainMenu', $box->userMainMenu());
		}
		if (!isset($this->data['userBar'])) { 
			$box = UFra::shared('UFbox_Sru');
			$this->append('userBar', $box->userBar());
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
		$box  = UFra::shared('UFbox_Sru');
	}
}
