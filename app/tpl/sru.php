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

		if ($this->_srv->get('msg')->get('userRecover/errors/token/invalid')) {
			echo $this->ERR('Token w linku jest nieprawidłowy.');
		} elseif ($this->_srv->get('msg')->get('userLogin/errors')) {
			echo $this->ERR('Nieprawidłowy login lub hasło. Czy aktywowałeś swoje konto w administracji DS?');
		}
		echo $d['user']->write('formLogin');
		echo $form->_submit('Zaloguj');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function userInfo(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Ważne informacje');
		if (!is_null($d['penalties'])) {
			echo $d['penalties']->write('listPenalty');
		} else {
			echo '<h3>Hurra! Brak aktywnych kar i ostrzeżeń! ;)</h3>';
		}
		echo $form->_end();
		echo $form->_fieldset('Najbliższe dyżury Twoich administratorów');
		if (!is_null($d['dutyHours'])) {
			echo $d['dutyHours']->write('upcomingDutyHours', $d['user'], 3);
		} else {
			echo $this->ERR('Żaden administrator nie jest przypisany do Twojego DSu. Skontaktuj się z nami mailowo: <a href="mailto:adnet@ds.pg.gda.pl">adnet@ds.pg.gda.pl</a>.');
		}
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userPenalties(array $d) {
		$d['penalties']->write('listAllPenalty');
	}


	public function titlePenalties() {
		echo 'Archiwum kar i ostrzeżeń';
	}

	public function userPenaltiesNotFound() {
		echo "<h3>Hurra! Brak kar i ostrzeżeń! ;)</h3>";
	}

	public function userServicesEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2>Dostępne usługi</h2>';
		echo $form->_start();

		if ($this->_srv->get('msg')->get('serviceEdit/ok')) {
			echo $this->OK('Zmiany zostały zapisane');
		}

		echo $d['allServices']->write('formEdit', $d['userServices'], $d['user']);
		echo $form->_end(true);
	}


	public function titleServices() {
		echo 'Panel Usług Użytkownika';
	}

	public function userServicesNotFound() {
		echo "<h3>Nie znaleziono usług</h3>";
	}

	public function userMainMenu() {
		$acl = $this->_srv->get('acl');
		
		echo '<div class="mainMenu"><h1>System Rejestracji Użytkowników</h1>';
		if ($this->_srv->get('msg')->get('userRecover/ok')) {
			echo $this->OK('Konto zostało aktywowane - teraz <a href="'.$this->url(0).'/computers">dodaj komputer</a>');
		}
		echo '<ul>';
		echo '<li><a href="'.$this->url(0).'/profile">Profil</a></li>';
		echo '<li><a href="'.$this->url(0).'/computers">Komputery</a></li>';
		echo '<li><a href="'.$this->url(0).'/penalties">Kary</a></li>';
		if ($acl->sru('service', 'view')) {
			echo '<li><a href="'.$this->url(0).'/services">Usługi</a></li>';
		}
		echo '</ul></div>';
	}

	public function titleError404() {
		echo 'Strony nie znaleziono';
	}

	public function error403() {
		echo $this->ERR('Nie masz uprawnień do oglądania tej strony. Wróć do <a href="'.$this->url(0).'/" title="System Rejestracji Użytkowników">SRU</a>.');
	}

	public function titleError403() {
		echo 'Brak uprawnień';
	}

	public function error404() {
		echo $this->ERR('Strony nie znaleziono. Wróć do <a href="'.$this->url(0).'/" title="System Rejestracji Użytkowników">SRU</a>.');
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
		echo $d['user']->write('formEdit', $d['faculties']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserComputers() {
		echo 'Twoje komputery';
	}

	public function userComputers(array $d) {
		echo '<h1>Twoje komputery</h1><ul>';
		if ($this->_srv->get('msg')->get('computerEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		} else if ($this->_srv->get('msg')->get('computerAdd/ok')) {
			echo $this->OK('Komputer został dodany');
		} elseif ($this->_srv->get('msg')->get('computerDel/ok')) {
			echo $this->OK('Komputer został wyrejestrowany');
		}
		$d['computers']->write('listOwn');
		echo '</ul>';
		echo '<p>Samodzielnie możesz dodać tylko jeden komputer. Jeżeli chcesz zarejestrować kolejny, zgłoś się do administratora lokalnego.</p>';
	}

	public function userComputersNotFound(array $d) {
		echo '<h1>Twoje komputery</h1>';
		if ($this->_srv->get('msg')->get('computerEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		if ($d != null) {
			echo $this->ERR('Nie posiadasz komputerów.');
			echo '<p>Przywróć komputer:</p>';
			$d['computers']->write('listToActivate');
			echo '<p><a href="'.$this->url(1).'/:add">lub dodaj nowy komputer</a>.</p>';
		} else {
			echo $this->ERR('Nie posiadasz komputerów. <a href="'.$this->url(1).'/:add">Dodaj komputer</a>.');
		}
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
		echo $this->ERR('Komputera nie znaleziono');
	}

	public function userComputerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset('Zmień dane komputera');
		echo $d['computer']->write('formEdit', $d['activate']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
		if ($d['activate']) {
			echo '<p class="nav"><a href="'.$this->url(1).'">Powrót</a></p>';
		} else {
			echo '<p class="nav"><a href="'.$this->url(2).'">Powrót</a></p>';
		}
	}

	public function titleUserComputerAdd() {
		echo 'Dodaj komputer';
	}

	public function userComputerAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/computers/:add');
		echo $form->_fieldset('Dodaj komputer');
		echo $d['computer']->write('formAdd', $d['user'], false, $d['macAddress'], null, null);
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

	public function computerStats(array $d) {
		echo '<div class="computer">';
		echo '<h2>Statystyki transferów</h2>';
		$d['computer']->write('transferStats', $d['file'], $d['statHour'], $d['statDate']);
		echo '</div>';
	}

	public function computerStatsNotFound() {
		echo '<h2>Statystyki transferów</h2>';
		echo $this->ERR('Brak danych');
	}

	public function userBar(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/', array('class'=>'userBar'));
		echo $form->_fieldset();
		echo $d['user']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt']);
		echo '<table><tr><td style="width: 100%;"><a href="'. $this->url(0) .'/">Strona główna</a></td><td>';
		echo $form->_submit('Wyloguj', array('name'=>'userLogout'));
		echo '</td></tr></table>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function recoverPassword(array $d) {
		$form = UFra::factory('UFlib_Form', 'sendPassword');

		echo $form->_start($this->url(0));
		echo $form->_fieldset('Nie pamiętam hasła');

		if ($this->_isOK('sendPassword')) {
			echo $this->OK('Kliknij link, który został wysłany na maila.');
		} elseif ($this->_isOK('userRecoverPassword')) {
			echo $this->OK('Nowe hasło zostało wysłane na maila.');
		} elseif ($this->_srv->get('msg')->get('sendPassword/errors/email/notUnique')) {
			echo $this->ERR('Podany email jest przypisany do kilku kont - proszę zgłosić się do administratora lokalnego w celu zmiany hasła.');
		} elseif ($this->_isErr('sendPassword')) {
			echo $this->ERR('Nie znaleziono aktywnego konta z podanym mailem.');
		}
		echo $form->email('E-mail');
		echo $form->_submit('Zmień');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userAddMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userAddMailTitleEnglish');
		} else {
			echo $d['user']->write('userAddMailTitlePolish');
		}
	}

	public function userAddMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userAddMailBodyEnglish', $d['dutyHours']);
		} else {
			echo $d['user']->write('userAddMailBodyPolish', $d['dutyHours']);
		}
	}

	public function userRecoverPasswordMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailTitleEnglish');
		} else {
			echo $d['user']->write('userRecoverPasswordMailTitlePolish');
		}
	}

	public function userRecoverPasswordMailBodyToken(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailBodyTokenEnglish', $d['token'], $d['host']);
		} else {
			echo $d['user']->write('userRecoverPasswordMailBodyTokenPolish', $d['token'], $d['host']);
		}
	}

	public function userRecoverPasswordMailBodyPassword(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailBodyPasswordEnglish', $d['password'], $d['host']);
		} else {
			echo $d['user']->write('userRecoverPasswordMailBodyPasswordPolish', $d['password'], $d['host']);
		}
	}
	
	public function penaltyAddMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyAddMailTitleEnglish');
		} else {
			echo $d['penalty']->write('penaltyAddMailTitlePolish');
		}
	}
	
	public function penaltyAddMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyAddMailBodyEnglish', $d['user'], $d['computers'], $d['dutyHours']);
		} else {
			echo $d['penalty']->write('penaltyAddMailBodyPolish', $d['user'], $d['computers'], $d['dutyHours']);
		}
	}

	public function penaltyEditMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyEditMailTitleEnglish');
		} else {
			echo $d['penalty']->write('penaltyEditMailTitlePolish');
		}
	}
	
	public function penaltyEditMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyEditMailBodyEnglish', $d['user'], $d['dutyHours']);
		} else {
			echo $d['penalty']->write('penaltyEditMailBodyPolish', $d['user'], $d['dutyHours']);
		}
	}
	
	public function dataChangedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('dataChangedMailTitleEnglish');
		} else {
			echo $d['user']->write('dataChangedMailTitlePolish');
		}
	}
	
	public function dataChangedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('dataChangedMailBodyEnglish');
		} else {
			echo $d['user']->write('dataChangedMailBodyPolish');
		}
	}

	public function hostChangedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostChangedMailTitleEnglish');
		} else {
			echo $d['host']->write('hostChangedMailTitlePolish');
		}
	}
	
	public function hostChangedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostChangedMailBodyEnglish', $d['action']);
		} else {
			echo $d['host']->write('hostChangedMailBodyPolish', $d['action']);
		}
	}
}
