<?
/**
 * zapelnienie pul w DSach
 */
class UFview_SruApi_DormitoryFreeIps
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dormitoryFreeIps());
	}
}
