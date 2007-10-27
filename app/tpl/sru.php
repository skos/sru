<?
/**
 * szablon modulu sru
 */
class UFtpl_Sru
extends UFtpl {

	public function titleLogin() {
		echo 'Zaloguj się';
	}

	public function login(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start();
		$form->_fieldset('Zaloguj się');
		if ($this->_srv->get('msg')->get('userLogin/errors')) {
			UFtpl_Html::msgOk('Nieprawidłowy login lub hasło');
		}
		echo $d['user']->write('formLogin');
		$form->_submit('Zaloguj');
		echo ' <a href="'.$this->url(0).'/create">Załóż konto</a>';
		$form->_end();
		$form->_end(true);
	}

	public function titleUserAdd() {
		echo 'Załóż konto';
	}

	public function userAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		//print_r($this->_srv->get('msg'));

		$form->_start();
		$form->_fieldset('Załóż konto');
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			UFtpl_Html::msgOk('Konto zostało założone');
		}
		echo $d['user']->write('formAdd', $d['dormitories'], $d['faculties']);
		$form->_submit('Załóż');
		$form->_end();
		$form->_end(true);
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function logout(array $d) {
		$form = UFra::factory('UFlib_Form');

		$form->_start($this->url(0).'/');
		$form->_fieldset('Wyloguj się');
		echo $d['user']->write('formLogout');
		$form->_submit('Wyloguj', array('name'=>'userLogout'));
		$form->_end();
		$form->_end(true);
	}

	public function userMainMenu() {
		echo '<div class="mainMenu"><h1>System Rejestracji Użytkowników</h1><ul>';
		echo '<li><a href="'.$this->url(0).'/profile">Profil</a></li>';
		echo '<li><a href="'.$this->url(0).'/computers">Komputery</a></li>';
		echo '<li><a href="'.$this->url(0).'/bans">Kary</a></li>';
		echo '<li><a href="'.$this->url(0).'/services">Usługi</a></li>';
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
		//print_r($this->_srv->get('msg'));

		$form->_start();
		$form->_fieldset('Twoje dane');
		if ($this->_srv->get('msg')->get('userEdit/ok')) {
			UFtpl_Html::msgOk('Dane zostały zmienione');
		}
		echo $d['user']->write('formEdit', $d['dormitories'], $d['faculties']);
		$form->_submit('Zapisz');
		$form->_end();
		$form->_end(true);
	}
}
