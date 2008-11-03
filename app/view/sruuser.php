<?
/**
 * widok uzytkownika
 */
class UFview_SruUser
extends UFview {

	protected function fillDefaultData() {
		if (!isset($this->data['userBar'])) { 
			$box = UFra::shared('UFbox_Sru');
			$this->append('userBar', $box->userBar());
		}	
	}			

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');
	}
}
