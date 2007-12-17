<?
/**
 * dane komputry
 */
class UFview_SruAdmin_Computers
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleComputers());
		
		$this->append('body', $box->serverComputers());
		$this->append('body', $box->administrationComputers());
		$this->append('body', $box->organizationsComputers());
	}
}
