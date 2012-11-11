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
		echo '<div class="rightColumn">';
		echo '<div class="rightColumnInfo"><h2>Informacja</h2><p>Na swoje konto możesz zalogować się dopiero po zameldowaniu Cię przez administrację DSu.</p></div>';
		echo '</div>';
		echo '<div class="leftColumn">';
		echo '<div id="login">';
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
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
		if ($this->_srv->get('msg')->get('userLogin/errors')) {
			UFlib_Script::focus('userLogin_password');
		}else{
			UFlib_Script::focus('userLogin_login');
		}
		echo '<span id="recoverPasswordSwitch"></span>';
		echo '</div>';
		// left column will continue...
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function penaltyInfo(array $d) {
		echo '<div class="leftColumn">';
		if (!is_null($d['penalties'])) {
			$form = UFra::factory('UFlib_Form');
			echo $form->_start($this->url(0).'/');
			echo $form->_fieldset('Aktywne kary i ostrzeżenia');
			echo $d['penalties']->write('listPenalty', $d['computers']);
			echo $form->_end();
			echo $form->_end(true);
		}
		// leftColumn will continue...
	}

	public function userInfo(array $d) {
		// leftColumn continues...
		if ($this->_srv->get('msg')->get('userEdit/ok')) {
			echo $this->OK('Dane zostały zmienione. Pamiętaj, aby zaktualizować dane, gdy ponownie ulegną zmianie.');
		}
		
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Twoje dane');
		echo $d['user']->write('detailsUser');
		echo $form->_end();
		echo $form->_end(true);
		// leftColumn will continue...
	}

	public function hostsInfo(array $d) {
		// leftColumn continues...
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Twoje komputery');
		if (is_null($d['computers'])) {
			if ($d['inactive'] != null) {
				echo $this->ERR('Nie posiadasz komputerów.');
				echo '<p>Przywróć komputer:</p>';
				$d['inactive']->write('listToActivate');
				echo '<p><a href="'.$this->url(0).'/computers/:add">lub dodaj nowy komputer</a>.</p>';
			} else {
				echo $this->ERR('Nie posiadasz komputerów. <a href="'.$this->url(0).'/computers/:add">Dodaj komputer</a>.');
			}
		} else {
			echo '<ul>';
			echo $d['computers']->write('listOwn');
			echo '</ul>';
		}
		echo $form->_end();
		echo $form->_end(true);
		// leftColumn will continue...
	}

	public function servicesInfo(array $d) {
		// leftColumn continues...
		$acl = $this->_srv->get('acl');
		if ($acl->sru('service', 'view')) {
			$form = UFra::factory('UFlib_Form');

			echo $form->_start($this->url(0).'/');
			echo $form->_fieldset('Twoje usługi');
			echo $d['allServices']->write('formEdit', $d['userServices'], $d['user'], false, false);
			echo '<p><a class="userAction" href="'.$this->url(0).'/services">Edytuj</a>';
			echo $form->_end();
			echo $form->_end(true);
		}
		echo '</div>';
	}

	public function contact(array $d) {
		echo '<div class="rightColumn">';
		$form = UFra::factory('UFlib_Form');

		echo $form->_fieldset('Kontakt');
		echo '<h3>Adres e-mail do wszystkich administratorów w DSie:<br/><a href="mailto:admin-'.$d['user']->dormitoryAlias.'@ds.pg.gda.pl">admin-'.$d['user']->dormitoryAlias.'@ds.pg.gda.pl</a>.</h3>';
		echo $form->_end();

		echo $form->_fieldset('Najbliższe dyżury');
		if (!is_null($d['dutyHours'])) {
			echo $d['dutyHours']->write('apiUpcomingDutyHours', 3, null);
		}
		echo '<p><a class="userAction" href="http://dyzury.ds.pg.gda.pl/">Pełna lista dyżurów</a>';
		echo $form->_end();
		echo $form->_end(true);
		// rightColumn will continue...
	}

	public function importantLinks(array $d) {
		// rightColumn continues...
		$conf = UFra::shared('UFconf_Sru');
		$links = $conf->userImportantLinks;
		if (!empty($links)) {
			$form = UFra::factory('UFlib_Form');
			echo $form->_fieldset('Ważne linki');
			echo '<ul>';
			foreach ($links as $url => $desc) {
				echo '<li><a href="'.$url.'">'.$desc.'</a>';
			}
			echo '</ul>';
			echo $form->_end();
			echo $form->_end(true);
		}
		// rightColumn will continue...
	}

	public function banners(array $d) {
		// rightColumn continues...
		if (!is_null($d['content'])) {
			echo $d['content'];
		}
		echo '</div>';
	}

	public function userPenalties(array $d) {
		$d['penalties']->write('listAllPenalty', $d['computers']);
	}


	public function titlePenalties() {
		echo 'Archiwum kar i ostrzeżeń';
	}

	public function userPenaltiesNotFound() {
		echo "<h3>Hurra! Brak kar i ostrzeżeń! ;)</h3>";
	}

	public function userServicesEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h1>Dostępne usługi</h1>';
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
		
		echo '<div id="nav"><ul>';
		echo '<li><a href="'.$this->url(0).'">Główna</a></li>';
		echo '<li><a href="'.$this->url(0).'/profile">Profil</a></li>';
		echo '<li><a href="'.$this->url(0).'/computers">Komputery</a></li>';
		echo '<li><a href="'.$this->url(0).'/penalties">Kary</a></li>';
		if ($acl->sru('service', 'view')) {
			echo '<li><a href="'.$this->url(0).'/services">Usługi</a></li>';
		}
		echo '</ul></div>';
	}

	public function titleError404() {
		echo 'Nie znaleziono strony';
	}

	public function error403() {
		echo $this->ERR('Nie masz uprawnień do oglądania tej strony. Wróć do <a href="'.$this->url(0).'/" title="System Rejestracji Użytkowników">SRU</a>.');
	}

	public function titleError403() {
		echo 'Brak uprawnień';
	}

	public function error404() {
		echo $this->ERR('Nie znaleziono strony. Wróć do <a href="'.$this->url(0).'/" title="System Rejestracji Użytkowników">SRU</a>.');
	}

	public function titleUserEdit() {
		echo 'Edycja Twoich danych';
	}

	public function userEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Twoje dane');
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
			echo $this->OK('Dane zostały zmienione.');
		} else if ($this->_srv->get('msg')->get('computerAdd/ok')) {
			echo $this->OK('Komputer został dodany. Poczekaj cierpliwie, aż sieć zacznie działać. Może to potrwać nawet godzinę.');
		} elseif ($this->_srv->get('msg')->get('computerDel/ok')) {
			echo $this->OK('Komputer został wyrejestrowany.');
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
		echo 'Nie znaleziono komputera';
	}

	public function userComputer(array $d) {
		$acl = $this->_srv->get('acl');

		echo '<div class="computer">';
		$d['computer']->write('detailsOwn');
		echo '<p><a class="userAction" href="'.$this->url(1).'">Powrót do listy</a>';
		if ($acl->sru('computer', 'edit')) {
			echo ' &bull; <a class="userAction" href="'.$this->url(2).'/:edit">Edytuj</a>';
		}
		echo '</p></div>';
	}

	public function userComputerNotFound() {
		echo $this->ERR('Nie znaleziono komputera');
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
		echo $d['user']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt'], $d['lastInvLoginIp'], $d['lastInvLoginAt']);
		echo $form->_submit('Wyloguj', array('name'=>'userLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function recoverPassword(array $d) {
		// left column continues...
		if ($this->_isOK('sendPassword')) {
			echo $this->OK('Kliknij link, który został wysłany na maila.');
		} elseif ($this->_isOK('userRecoverPassword')) {
			echo $this->OK('Nowe hasło zostało wysłane na maila.');
		} elseif ($this->_srv->get('msg')->get('sendPassword/errors/email/notUnique')) {
			echo $this->ERR('Podany email jest przypisany do kilku kont - proszę zgłosić się do administratora lokalnego w celu zmiany hasła.');
		} elseif ($this->_isErr('sendPassword')) {
			echo $this->ERR('Nie znaleziono aktywnego konta z podanym mailem. Czy aktywowałeś swoje konto w administracji DS?');
		}
		echo '<div id="recoverPassword">';
		$form = UFra::factory('UFlib_Form', 'sendPassword');

		echo $form->_start($this->url(0));
		echo $form->_fieldset('Przypomnij login i hasło');
		echo $form->email('E-mail');
		echo $form->_submit('Zmień');
		echo $form->_end();
		echo $form->_end(true);
		echo '</div></div>';

?><script type="text/javascript">
function changeVisibility() {
	var rpDiv = document.getElementById('recoverPassword');
	var lDiv = document.getElementById('login');
	if (rpDiv.sruHidden != true) {
		rpDiv.style.display = 'none';
		rpDiv.sruHidden = true;
		lDiv.style.display = 'block';
	} else {
		rpDiv.style.display = 'block';
		rpDiv.sruHidden = false;
		lDiv.style.display = 'none';
	}
}
var container = document.getElementById('recoverPasswordSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Zapomniałem loginu lub hasła!');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function titleUserUnregistered() {
		echo 'Komputer niezarejestrowany w SKOS PG';
	}

	public function userUnregistered() {
		echo '<h1>Twój komputer jest niezarejestrowany w SKOS PG</h1>
<p>Aby zarejestrować się, musisz zameldować się w administracji swojego akademika.</p>
<p>Jeżeli posiadasz konto w  SRU, a lista Twoich komputerów jest pusta, należy dodać komputer. Po zarejestrowaniu komputera należy poczekać nawet godzinę.</p>
<p>Zobacz także: <a href="http://skos.ds.pg.gda.pl">Strona SKOS</a></p>
<h1>Your computer is not registered in the SKOS PG</h1>
<p>You should visit your dorm administration.</p>
<p>If you have an account in SRU, but the list of your computers is empty, you should add your computer. After that you need to wait for 1 hour.</p
<p>See also: <a href="http://skos.ds.pg.gda.pl">SKOS web page</a></p>
<p>*SKOS PG - it is a polish acronym for the campus network</p>';
	}

	public function titleUserBanned() {
		echo 'Komputer ukarany odcięciem od Internetu';
	}

	public function userBanned() {
		echo '<h1>Twój komputer został ukarany odcięciem od Internetu</h1>
<p>Zaloguj się, aby sprawdzić powód kary. Możesz się także skontaktować z administratorami w <a href="http://dyzury.ds.pg.gda.pl/">godzianch dyżurów</a>.</p>
<p>Zobacz także: <a href="http://skos.ds.pg.gda.pl">Strona SKOS</a> &bull; <a href="http://kary.ds.pg.gda.pl">Polityka kar</a></p>
<h1>Your computer has been punished by cutting off Internet</h1>
<p>Log in to check the reason of your penalty. You can also contact us during <a href="http://dyzury.ds.pg.gda.pl/">our duty hours</a>.</p>
<p>See also: <a href="http://skos.ds.pg.gda.pl">SKOS web page</a> &bull; <a href="http://kary.ds.pg.gda.pl">Penalties politic</a></p>';
	}

	public function userAddByAdminMailTitle(array $d) {
            echo $d['user']->write('userAddByAdminMailTitle');	
	}
	
	public function userAddByAdminMailBody(array $d) {
            echo $d['user']->write('userAddByAdminMailBody', $d['password']);
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
			echo $d['penalty']->write('penaltyAddMailTitlePolish', $d['user']);
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
