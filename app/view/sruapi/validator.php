<?
/**
 * validator na potrzeby Ajax
 */
class UFview_SruApi_Validator
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->validatorResults());
	}
}

