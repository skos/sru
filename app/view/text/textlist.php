<?
/**
 * lista stron tekstowych
 */
class UFview_Text_TextList
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->titleList());
		$this->append('body', $text->listShort());
	}
}
