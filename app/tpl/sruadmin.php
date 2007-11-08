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

	public function logout(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url(0).'/');
		$form->_fieldset('Wyloguj się');
		echo $d['admin']->write('formLogout');
		$form->_submit('Wyloguj', array('name'=>'adminLogout'));
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

	public function titleComputerNotFound() {
		echo 'Komputera nie znaleziono';
	}

	public function computerNotFound() {
		UFtpl_Html::msgErr('Komputera nie znaleziono');
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

	public function computersNotFound() {
		UFtpl_Html::msgErr('Komputerów nie znaleziono');
	}

	public function computerHistory(array $d) {
		echo '<div class="computer">';
		echo '<h2>Historia zmian</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['computer']);
		echo '</ol>';
		echo '</div>';
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

	public function computerSearch(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<div class="computerSearch">';
		$form->_start($this->url(0).'/computers');
		$form->_fieldset('Znajdź komputer');
		echo $d['computer']->write('formSearch', $d['searched']);
		$form->_submit('Znajdź');
		$form->_end();
		$form->_end(true);
		echo '</div>';
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function titleComputerSearch() {
		echo 'Znajdź komputer';
	}

	public function computerSearchResults(array $d) {
		echo '<div class="computerSearchResults"><ul>';
		echo $d['computers']->write('searchResults');
		echo '</ul></div>';
	}

	public function computerSearchResultsNotFound() {
		UFtpl_Html::msgErr('Nie znaleziono');
	}

	public function titleUserSearch() {
		echo 'Znajdź użytkownika';
	}

	public function userSearch(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<div class="userSearch">';
		$form->_start($this->url(0).'/users');
		$form->_fieldset('Znajdź użytkownika');
		echo $d['user']->write('formSearch', $d['searched']);
		$form->_submit('Znajdź');
		$form->_end();
		$form->_end(true);
		echo '</div>';
	}

	public function userSearchResults(array $d) {
		echo '<div class="userSearchResults"><ul>';
		echo $d['users']->write('searchResults');
		echo '</ul></div>';
	}

	public function userSearchResultsNotFound() {
		UFtpl_Html::msgErr('Nie znaleziono');
	}

	public function user(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id;
		echo '<div class="user">';
		$d['user']->write('details');
		echo '<p class="nav"><a href="'.$url.'">Dane</a> <a href="'.$url.'/history">Historia zmian</a> <a href="'.$url.'/:edit">Edycja</a></p>';
		echo '</div>';
	}

	public function userNotFound() {
		UFtpl_Html::msgErr('Użytkownika nie znaleziono');
	}

	public function titleUser(array $d) {
		echo $d['user']->write('titleDetails');
	}

	public function titleUserNotFound() {
		echo 'Użytkownika nie znaleziono';
	}

	public function userComputers(array $d) {
		echo '<h2>Komputery użytkownika</h2><ul>';
		echo $d['computers']->write('listAdmin');
		echo '</ul>';
	}

	public function userComputersNotFound() {
	}

	public function titleUserEdit(array $d) {
		echo $d['user']->write('titleEdit');
	}

	public function userEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja</h2>';
		$form->_start($this->url());
		$form->_fieldset('Edycja użytkownika');
		if (!$this->_srv->get('msg')->get('userEdit') && $this->_srv->get('req')->get->is('userHistoryId')) {
			UFtpl_Html::msgErr('Formularz wypełniony danymi z '.date(self::TIME_YYMMDD_HHMM, $d['user']->modifiedAt));
		} elseif ($this->_srv->get('msg')->get('userEdit/ok')) {
			UFtpl_Html::msgOk('Dane zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('userEdit/loginChanged')) {
			UFtpl_Html::msgOk('W związku ze zmianą loginu, użytkownik będzie musiał przejść procedurę zmiany hasła.');
		}
		echo $d['user']->write('formEditAdmin', $d['dormitories'], $d['faculties']);
		$form->_submit('Zapisz');
		$form->_end();
		$form->_end(true);
	}

	public function userHistory(array $d) {
		echo '<div class="user">';
		echo '<h2>Historia zmian</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['user']);
		echo '</ol>';
		echo '</div>';
	}
}