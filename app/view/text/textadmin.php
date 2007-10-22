<?
/**
 * administracyjna lista stron
 */
class UFview_Text_TextAdmin
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->titleAdmin());
		$this->append('body', $text->adminList());
	}
}
