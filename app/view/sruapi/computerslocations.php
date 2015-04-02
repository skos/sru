<?
/**
 * polozenie komputerow w pokojach
 */
class UFview_SruApi_ComputersLocations
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->computersLocations());
	}
}
