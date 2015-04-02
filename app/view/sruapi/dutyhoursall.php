<?
/**
 * godziny dyżurów - wszystkie
 */
class UFview_SruApi_DutyHoursAll
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dutyHours());
	}
}
