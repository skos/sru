<?
/**
 * szablon modulu tekstowego
 */
class UFtpl_Text
extends UFtpl_Common {

	public function addTitle(array $d) {
		echo 'Dodaj stronę';
	}

	public function preview(array $d) {
		$d['text']->write('preview');
	}

	public function add(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/:add');
		echo $form->_fieldset('Dodaj stronę');
		if ($this->_srv->get('msg')->get('textAdd/ok')) {
			UFtpl_Html::msgOk('Strona została dodana');
		}
		echo $d['text']->write('formAdd');
		echo $form->_submit('Podgląd', array('name'=>'textPreview'));
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);
		echo '<small class="admin"><a href="'.$this->url(0).'/:edit">Edytuj</a></small>';
	}

	public function titleList(array $d) {
		echo 'Lista stron';
	}

	public function listShort(array $d) {
		echo '<h1>Lista stron</h1><ul>';
		echo $d['texts']->write('listShort');
		echo '</ul>';
		echo '<small class="admin"><a href="'.$this->url(0).'/:edit">Edytuj</a></small>';
	}

	public function listShortNotFound() {
		UFtpl_Html::msgErr('Nie ma jeszcze stron');
		echo '<small><a href="'.$this->url(0).'/:add">Dodaj</a></small>';
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
		echo '<h1>Administracja stronami</h1>';
		if ($this->_srv->get('msg')->get('textDel/ok')) {
			UFtpl_Html::msgOk('Strona została usunięta');
		} elseif ($this->_srv->get('msg')->get('textEdit/ok')) {
			UFtpl_Html::msgOk('Strona została zapisana');
		}
		echo '<ul class="admin">';
		echo $d['texts']->write('adminList');
		echo '</ul>';
		echo '<small class="admin"><a href="'.$this->url(0).'/">Wróć do listy</a> <a href="'.$this->url(0).'/:add">Dodaj</a></small>';
	}

	public function edit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url().'/');
		echo $form->_fieldset('Zmień stronę');
		if ($this->_srv->get('msg')->get('textEdit/ok')) {
			UFtpl_Html::msgOk('Strona została zapisana');
		}
		echo $d['text']->write('formEdit');
		echo $form->_submit('Podgląd', array('name'=>'textPreview'));
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
		echo '<small class="admin"><a href="'.$this->url(0).'/:edit">Edytuj</a></small>';
	}

	public function titleNotFound() {
		echo 'Strony nie znaleziono';
	}

	public function textNotFound() {
		UFtpl_Html::msgErr('Strony nie znaleziono');
	}

	public function delete(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url().'/');
		echo $form->_fieldset('Usuń stronę');
		echo $d['text']->write('formDel');
		echo $form->_submit('Usuń', array('name'=>'textDel'));
		echo $form->_end();
		echo $form->_end(true);
		echo '<a href="'.$this->url(0).'/:edit">Edytuj</a>';
	}
}
