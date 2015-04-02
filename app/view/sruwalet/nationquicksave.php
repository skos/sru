<?
/**
 * szybki zapis
 */
class UFview_SruWalet_NationQuickSave
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruWalet');

		$this->append('body', $box->quickNationSaveResults());
	}
}

