<?
/**
 * lista turystów indywidualnych
 */
class UFview_SruApi_Tourists
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->tourists());
	}
}
