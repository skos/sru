<?
/**
 * edycja strony tekstowej
 */
class UFview_Text_TextEdit
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->title());
		$this->append('body', $text->edit());
	}
}
