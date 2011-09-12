<?
/**
 * sprawdzanie PESELu
 */
class UFview_SruWalet_UserValidatePesel
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('body', $box->validatePeselResults());
	}
}

