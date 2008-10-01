<?
/**
 * szablon beana tekstowego
 */
class UFtpl_Text_Text
extends UFtpl_Common {
	
	protected $errors = array(
		'alias' => 'Adres nie jest prawidłowy.',
		'alias/textMin' => 'Podaj adres',
		'alias/textMax' => 'Zbyt długi adres',
		'alias/regexp' => 'Dozwolone są litery, cyfry i myślniki.',
		'alias/duplicated' => 'Adres jest już używany',
		'title' => 'Tytuł nie jest prawidłowy.',
		'title/textMin' => 'Podaj tytuł',
		'title/textMax' => 'Zbyt długi tytuł',
	);

	public function addTitle(array $d) {
		echo 'Dodaj stronę';
	}

	public function formAdd(array $d) {
		$form = UFra::factory('UFlib_Form', 'textAdd', $d, $this->errors);

		echo $form->title('Tytuł');
		echo $form->alias('Adres');
		echo $form->content('Treść', array('type'=>$form->TEXTAREA, 'rows'=>20));
	}

	public function preview(array $d) {
		$wiki = UFra::shared('UFlib_Wiki_Xhtml');
		echo $wiki->render($d['content']);
	}

	public function listShort(array $d) {
		$url = $this->url(0).'/';
		foreach ($d as $t) {
			echo '<li><a href="'.$url.$t['alias'].'">'.$t['title'].'</a></li>';
		}
	}

	public function title(array $d) {
		echo $d['title'];
	}

	public function show(array $d) {
		$wiki = UFra::shared('UFlib_Wiki_Xhtml');
		echo $wiki->render($d['content']);
	}

	public function adminList(array $d) {
		$url = $this->url(0).'/:edit/';
		$urlDel = $this->url(0).'/:del/';
		foreach ($d as $t) {
			echo '<li><a href="'.$url.$t['alias'].'">'.$t['title'].' &mdash; '.$t['alias'].'</a><small> <a href="'.$urlDel.$t['alias'].'">Usuń</a></small></li>';
		}
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'textEdit', $d, $this->errors);

		echo $form->title('Tytuł');
		echo $form->alias('Adres');
		echo $form->content('Treść', array('type'=>$form->TEXTAREA, 'rows'=>20));
	}

	public function formDel(array $d) {
		$this->show($d);
	}
}
