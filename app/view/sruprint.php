<?
/**
 * widok wydruku
 */
class UFview_SruPrint
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexPrint', $this->_srv);
	}

	protected function fillDefaultData() {
		$acl = $this->_srv->get('acl');

		// logi
		if ($acl->core('log', 'ufraShow')) {
			$bLogs = UFra::shared('UFbox_Log');
			$this->data['logs'] = $bLogs->full();
		} else {
			$this->data['logs'] = '';
		}
	}

	public function fillData() {
		$box  = UFra::shared('UFbox_SruWalet');
		$this->append('title', $box->title());
	}
}
