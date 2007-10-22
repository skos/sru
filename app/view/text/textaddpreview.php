<?
/**
 * dodanie strony tekstowej
 */
class UFview_Text_TextAddPreview
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->addTitle());
		$this->append('body', $text->addPreview());
		$this->append('body', $text->add());
	}
}
