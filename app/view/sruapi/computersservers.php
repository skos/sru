<?
/**
 * serwery i urzadzenia
 */
class UFview_SruApi_ComputersServers
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->computersServers());
	}
}
