<?
/**
 * dodanie strony tekstowej
 */
class UFview_Text_TextAdd
extends UFview {

	public function fillData() {
		$text  = UFra::shared('UFbox_Text');

		$this->append('title', $text->addTitle());
		$this->append('body', $text->add());
	}
}
