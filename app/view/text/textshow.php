<?
/**
 * strona tekstowa
 */
class UFview_Text_TextShow
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->title());
		$this->append('body', $text->show());
	}
}
