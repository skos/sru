<?
/**
 * godziny dyżurów - zbliżające się
 */
class UFview_SruApi_DutyHoursUpcoming
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->dutyHoursUpcoming());
	}
}
