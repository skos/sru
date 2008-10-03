<?
/**
 * szablon modulu sru
 */
class UFtpl_Sru
extends UFtpl_Common {

	public function titleLogin() {
		echo 'Zaloguj się';
	}

	public function login(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Zaloguj się');

		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			echo $this->OK('Konto zostało założone. Hasło otrzymasz wkrótce na maila.');
		} elseif ($this->_srv->get('msg')->get('userLogin/errors')) {
			echo $this->ERR('Nieprawidłowy login lub hasło');
		}
		echo $d['user']->write('formLogin');
		echo $form->_submit('Zaloguj');
		echo ' <a href="'.$this->url(0).'/create">Załóż konto</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserAdd() {
		echo 'Załóż konto';
	}

	public function userAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Załóż konto');
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			UFtpl_Html::msgOk('Konto zostało założone');
		}
		echo $d['user']->write('formAdd', $d['dormitories'], $d['faculties'], $d['admin']);
		echo '<br/><b>Założenie konta oznacza akceptację <a href="../regulamin.html">Regulaminu SKOS PG</a>.</b><br/><br/>';
		echo $form->_submit('Załóż');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userAddMailTitle(array $d) {
		echo 'Witamy w sieci SKOS';
	}

	public function userAddMailBody(array $d) {
		echo 'Imię: '.$d['user']->name."\n";
		echo 'Nazwisko: '.$d['user']->surname."\n";
		echo "\n";
		echo 'Login: '.$d['user']->login."\n";
		echo 'Hasło: '.$d['password'];
		echo "\n";
		echo 'Kliknij: http://'.$d['host'].$this->url(0).'/'.$d['token']->token;
	}

	public function userAddMailHeaders(array $d) {
		echo 'MIME-Version: 1.0'."\n";
		echo 'Content-Type: text/plain; charset=UTF-8'."\n";
		echo 'Content-Transfer-Encoding: 8bit'."\n";
		echo 'From: Test SRU <testowy-email-no-reply@pg.edu.pl>'."\n";
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function logout(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Wyloguj się');
		echo $d['user']->write('formLogout');
		echo $form->_submit('Wyloguj', array('name'=>'userLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userMainMenu() {
		echo '<div class="mainMenu"><h1>System Rejestracji Użytkowników</h1>';
		if ($this->_srv->get('msg')->get('userConfirm/ok')) {
			echo $this->OK('Konto zostało aktywowane.');
		}
		echo '<ul>';
		echo '<li><a href="'.$this->url(0).'/profile">Profil</a></li>';
		echo '<li><a href="'.$this->url(0).'/computers">Komputery</a></li>';
		/*
		echo '<li><a href="'.$this->url(0).'/bans">Kary</a></li>';
		echo '<li><a href="'.$this->url(0).'/services">Usługi</a></li>';
		*/
		echo '</ul></div>';
	}

	public function titleError404() {
		echo 'Strony nie znaleziono';
	}

	public function error404() {
		UFtpl_Html::msgErr('Strony nie znaleziono. Wróć do <a href="'.$this->url(0).'/" title="System Rejestracji Użytkowników">SRU</a>.');
	}

	public function titleUserEdit() {
		echo 'Edycja Twoich danych';
	}

	public function userEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Twoje dane');
		if ($this->_srv->get('msg')->get('userEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		echo $d['user']->write('formEdit', $d['dormitories'], $d['faculties']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserComputers() {
		echo 'Twoje komputery';
	}

	public function userComputers(array $d) {
		echo '<h1>Twoje komputery</h1><ul>';
		if ($this->_srv->get('msg')->get('computerAdd/ok')) {
			UFtpl_Html::msgOk('Komputer został dodany');
		} elseif ($this->_srv->get('msg')->get('computerDel/ok')) {
			UFtpl_Html::msgOk('Komputer został wyrejestrowany');
		}
		$d['computers']->write('listOwn');
		echo '</ul>';
		echo '<p>Samodzielnie możesz dodać tylko jeden komputer. Jeżeli chcesz zarejestrować kolejny, zgłoś się do administratora lokalnego.</p>';
	}

	public function userComputersNotFound() {
		echo '<h1>Twoje komputery</h1>';
		echo $this->ERR('Nie posiadasz komputerów. <a href="'.$this->url(1).'/:add">Dodaj komputer</a>.');
	}

	public function titleUserComputer(array $d) {
		echo $d['computer']->write('titleDetails');
	}

	public function titleUserComputerNotFound(array $d) {
		echo 'Komputera nie znaleziono';
	}

	public function userComputer(array $d) {
		echo '<div class="computer">';
		$d['computer']->write('detailsOwn');
		echo '<p class="nav"><a href="'.$this->url(1).'">Powrót do listy</a> <small><a href="'.$this->url(2).'/:edit">Edytuj</a></small></p>';
		echo '</div>';
	}

	public function userComputerNotFound() {
		UFtpl_Html::msgErr('Komputera nie znaleziono');
	}

	public function userComputerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset('Zmień dane komputera');
		if ($this->_srv->get('msg')->get('computerEdit/ok')) {
			UFtpl_Html::msgOk('Dane zostały zmienione');
		}
		echo $d['computer']->write('formEdit');
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
		echo '<p class="nav"><a href="'.$this->url(2).'">Powrót</a></p>';
	}

	public function titleUserComputerAdd() {
		echo 'Dodaj komputer';
	}

	public function userComputerAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/computers/:add');
		echo $form->_fieldset('Dodaj komputer');
		echo $d['computer']->write('formAdd');
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userComputerDel(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset('Wyrejestruj komputer');
		echo $d['computer']->write('formDel');
		echo $form->_submit('Wyrejestruj');
		echo $form->_end();
		echo $form->_end(true);
	}
}
