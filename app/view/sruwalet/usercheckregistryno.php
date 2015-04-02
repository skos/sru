<?
/**
 * sprawdzenie unikalnosci nr indeksu
 */
class UFview_SruWalet_UserCheckRegistryNo
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('body', $box->checkRegistryNoResults());
	}
}

