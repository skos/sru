<?
/**
 * szablon modulu tekstowego
 */
class UFtpl_Text
extends UFtpl {

	public function addTitle(array $d) {
		echo 'Dodaj stronę';
	}

	public function preview(array $d) {
		$d['text']->write('preview');
	}

	public function add(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url(0).'/:add');
		$form->_fieldset('Dodaj stronę');
		if ($this->_srv->get('msg')->get('textAdd/ok')) {
			UFtpl_Html::msgOk('Strona została dodana');
		}
		echo $d['text']->write('formAdd');
		$form->_submit('Podgląd', array('name'=>'textPreview'));
		$form->_submit('Dodaj');
		$form->_end();
		$form->_end(true);
		echo '<a href="'.$this->url(0).'/:edit">Administruj</a>';
	}

	public function titleList(array $d) {
		echo 'Lista stron';
	}

	public function listShort(array $d) {
		echo '<h2>Lista stron</h2><ul>';
		echo $d['texts']->write('listShort');
		echo '</ul>';
		echo '<a href="'.$this->url(0).'/:edit">Administruj</a>';
	}

	public function title(array $d) {
		echo $d['text']->write('title');
	}

	public function show(array $d) {
		echo $d['text']->write('show');
	}

	public function titleAdmin() {
		echo 'Administracja stronami';
	}

	public function adminList(array $d) {
		echo '<h2>Administracja stronami</h2>';
		if ($this->_srv->get('msg')->get('textDel/ok')) {
			UFtpl_Html::msgOk('Strona została usunięta');
		} elseif ($this->_srv->get('msg')->get('textEdit/ok')) {
			UFtpl_Html::msgOk('Strona została zapisana');
		}
		echo '<ul>';
		echo $d['texts']->write('adminList');
		echo '</ul>';
		echo '<a href="'.$this->url(0).'/">Wróć do prezentacji</a> <a href="'.$this->url(0).'/:add">Dodaj</a>';
	}

	public function edit(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url().'/');
		$form->_fieldset('Zmień stronę');
		if ($this->_srv->get('msg')->get('textEdit/ok')) {
			UFtpl_Html::msgOk('Strona została zapisana');
		}
		echo $d['text']->write('formEdit');
		$form->_submit('Podgląd', array('name'=>'textPreview'));
		$form->_submit('Zapisz');
		$form->_end();
		$form->_end(true);
		echo '<a href="'.$this->url(0).'/:edit">Administruj</a>';
	}

	public function titleNotFound() {
		echo 'Strony nie znaleziono';
	}

	public function textNotFound() {
		UFtpl_Html::msgErr('Strony nie znaleziono');
	}

	public function delete(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url().'/');
		$form->_fieldset('Usuń stronę');
		echo $d['text']->write('formDel');
		$form->_submit('Usuń', array('name'=>'textDel'));
		$form->_end();
		$form->_end(true);
		echo '<a href="'.$this->url(0).'/:edit">Administruj</a>';
	}
}
