<?
/**
 * front controller aplikacji
 */
class UFctl_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();
		if (0 == $segCount) {
			$get->view = 'index';
		} else {
			switch ($req->segment(1)) {
				case 'admin':
					$ctl = UFra::factory('UFctl_SruAdmin_Front');
					$req->forward();
					$ctl->go();
					return false;
				case 'sru':
					$ctl = UFra::factory('UFctl_Sru_Front');
					$req->forward();
					$ctl->go();
					return false;
				case 'api':
					$ctl = UFra::factory('UFctl_SruApi_Front');
					$req->forward();
					$ctl->go();
					return false;
				case 'walet':
					$ctl = UFra::factory('UFctl_SruWalet_Front');
					$req->forward();
					$ctl->go();
					return false;
				default:
					$ctl = UFra::factory('UFctl_Text_Front');
					$ctl->go();
					return false;
			}
		}
	}

	protected function filterMiddle() {
		$this->_srv->get('req')->get->alias = 'index.html';
	}

	protected function chooseView($view = null) {
		return 'Text_TextShow';
	}
}
