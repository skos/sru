<?
/**
 * edytowanie wniosku o uslugi serwerowe
 */
class UFview_Sru_ApplicationFwExceptionEdit
extends UFview_SruUser {

	public function fillData() {
		$box  = UFra::shared('UFbox_Sru');

		$this->append('title', $box->titleApplicationFwException());
		$this->append('body', $box->applicationFwException());
	}
}
