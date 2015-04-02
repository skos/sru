<?
/**
 * komputery, ktore nie byly widoczne w sieci
 */
class UFview_SruApi_ComputersNotSeen
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->computersNotSeen());
	}
}
