<?
/**
 * widok Walet
 */
class UFview_SruWalet
extends UFview {

	protected function chooseTemplate() {
		return UFra::factory('UFtpl_IndexWalet', $this->_srv);
	}

	protected function fillDefaultData() {
		$acl = $this->_srv->get('acl');

		if (!isset($this->data['menuWalet'])) {
			$box = UFra::shared('UFbox_SruWalet');
			$this->append('menuWalet', $box->menuWalet());
		}
		if (!isset($this->data['waletBar'])) { 
			$box = UFra::shared('UFbox_SruWalet');
			$this->append('waletBar', $box->waletBar());
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
		$box  = UFra::shared('UFbox_SruWalet');

		$this->append('title', $box->title());
	}
}
