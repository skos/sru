<?
/**
 * szablon modulu administracji sru
 */
class UFtpl_SruAdmin
extends UFtpl {

	public function titleLogin() {
		echo 'Zaloguj się';
	}

	public function login(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start();
		$form->_fieldset('Zaloguj się');
		if ($this->_srv->get('msg')->get('adminLogin/errors')) {
			UFtpl_Html::msgErr('Nieprawidłowy login lub hasło');
		}
		echo $d['admin']->write('formLogin');
		$form->_submit('Zaloguj');
		$form->_end();
		$form->_end(true);
	}

	public function title() {
		echo 'Administracja SKOS';
	}

	public function menuAdmin() {
		echo '<ul id="nav">';
		echo '<li><a href="'.UFURL_BASE.'/admin/">Start</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/users/">Użytkownicy</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/computers/">Komputery</a></li>';
		echo '</ul>';
	}

	public function titleComputer(array $d) {
		echo $d['computer']->write('titleDetails');
	}

	public function computer(array $d) {
		$url = $this->url(0).'/computers/'.$d['computer']->id;
		echo '<div class="computer">';
		$d['computer']->write('details');
		echo '<p class="nav"><a href="'.$url.'">Dane</a> <a href="'.$url.'/history">Historia zmian</a> <a href="'.$url.'/:edit">Edycja</a></p>';
		echo '</div>';
	}

	public function titleComputers() {
		echo 'Wszystkie komputery';
	}

	public function computers(array $d) {
		echo '<div class="computers">';
		echo '<h1>Komputery</h1>';
		if ($this->_srv->get('msg')->get('computerDel/ok')) {
			UFtpl_Html::msgOk('Komputer został usunięty');
		}
		echo '<ul>';
		$d['computers']->write('listAdmin');
		echo '</ul>';
		echo '</div>';
	}

	public function computerHistory(array $d) {
		echo '<div class="computer">';
		echo '<h2>Historia zmian</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['computer']);
		echo '</ol>';

	}

	public function titleComputerEdit(array $d) {
		echo $d['computer']->write('titleEdit');
	}

	public function computerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja</h2>';
		$form->_start($this->url());
		$form->_fieldset('Edycja komputera');
		if (!$this->_srv->get('msg')->get('computerEdit') && $this->_srv->get('req')->get->is('computerHistoryId')) {
			UFtpl_Html::msgErr('Formularz wypełniony danymi z '.date(self::TIME_YYMMDD_HHMM, $d['computer']->modifiedAt));
		} elseif ($this->_srv->get('msg')->get('computerEdit/ok')) {
			UFtpl_Html::msgOk('Dane zostały zmienione');
		}
		echo $d['computer']->write('formEditAdmin', $d['dormitories']);
		$form->_submit('Zapisz');
		$form->_end();
		$form->_end(true);
	}

	public function computerDel(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url(3).'/');
		$form->_fieldset('Wyrejestruj komputer');
		echo $d['computer']->write('formDelAdmin');
		$form->_submit('Wyrejestruj');
		$form->_end();
		$form->_end(true);
	}
}
