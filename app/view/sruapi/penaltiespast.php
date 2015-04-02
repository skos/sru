<?
/**
 * przedawnione kary
 */
class UFview_SruApi_PenaltiesPast
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->penaltiesPast());
	}
}
