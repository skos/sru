<?
/**
 * aliasy komputerow, ktorym skonczyla sie rdata waznosci
 */
class UFview_SruApi_ComputersOutdatedAliases
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->computersOutdatedAliases());
	}
}
