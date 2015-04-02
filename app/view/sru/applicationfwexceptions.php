<?
/**
 * lista wnioskow o uslugi serwerowe
 */
class UFview_Sru_ApplicationFwExceptions
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleApplicationFwExceptions());
		$this->append('body', $box->applicationFwExceptions());
	}
}
