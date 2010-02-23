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

		if ($this->_srv->get('msg')->get('userConfirm/errors/token/invalid')) {
			echo $this->ERR('Token w linku jest nieprawidłowy.');
		} elseif ($this->_srv->get('msg')->get('userLogin/errors')) {
			echo $this->ERR('Nieprawidłowy login lub hasło. Czy aktywowałeś swoje konto u administratora lub linkiem z maila?');
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
		echo $d['user']->write('formAdd', $d['dormitories'], $d['faculties'], $d['admin']);
		echo '<br/><b>Założenie konta oznacza akceptację <a href="http://skos.ds.pg.gda.pl/skos/wiki/regulamin">Regulaminu SKOS PG</a>.</b><br/><br/>';
		echo $form->_submit('Załóż');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserAdded() {
		echo 'Założono konto';
	}

	public function userAdded(array $d) {

		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			echo $this->OK('Konto zostało założone. Hasło otrzymasz wkrótce na maila.<br /><br /><a href="'.$this->url(0).'">Kliknij tutaj, aby się zalogować</a>');
		}
	}

	public function userAddMailTitle(array $d) {
		echo 'Witamy w sieci SKOS / Welcome in SKOS network';
	}

	protected function userAddMailBody(array $d, $info='', $infoEn='') {
		echo 'Witamy w Sieci Komputerowej Osiedla Studenckiego Politechniki Gdańskiej!'."\n";
		echo "\n";
		echo 'Jeżeli otrzymałeś/aś tę wiadomość, a nie chciałeś/aś założyć konta w SKOS PG,'."\n";
		echo 'prosimy o zignorowanie tej wiadomości.'."\n";
		echo $info;
		echo "\n";
		echo 'W razie jakichkolwiek problemów zachęcamy do skorzystania z FAQ:'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Dane, na które zostało założone konto:'."\n";
		echo $d['user']->write('userAddMailBody', $d['password']);
		echo "\n";
		echo 'System Rejestracji Użytkowników: http://'.$d['host'].'/'."\n";
		echo 'PROSIMY O ZMIANĘ HASŁA ZARAZ PO PIERWSZYM ZALOGOWANIU SIĘ!'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Nasza sieć obejmuje swoim zasięgiem sieci LAN wszystkich Domów Studenckich'."\n";
		echo 'Politechniki Gdańskiej, jest częścią Uczelnianej Sieci Komputerowej (USK PG) i'."\n";
		echo 'dołączona jest bezpośrednio do sieci TASK.'."\n";
		echo "\n";
		echo 'Wszelkie informacje na temat funkcjonowania sieci, godzin dyżurów'."\n";
		echo 'administratorów SKOS PG oraz Regulamin SKOS PG znajdziesz na stronie'."\n";
		echo 'http://skos.ds.pg.gda.pl/ , zaś bieżące komunikaty na grupie dyskusyjnej ds.siec.komunikaty'."\n";
		echo "\n\n";
		echo '- - - ENGLISH VERSION - - -'."\n";
		echo 'Welcome in Gdańsk University of Technology Students’ District Computer Network (polish acronym - SKOS PG)!' . "\n";
		echo "\n";
		echo 'If you received this message but you didn’t want to create an account in SKOS PG, please ignore it.' . "\n";
		echo $infoEn;
		echo "\n";
		echo 'If you have problems using Internet in our network, please refer to FAQ:'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Account was created for:'."\n";
		echo $d['user']->write('userAddMailBodyEnglish', $d['password']);
		echo "\n";
		echo 'User Register System (System Rejestracji Użytkowników): http://'.$d['host'].'/'."\n";
		echo 'PLEASE CHANGE YOUR PASSWORD AFTER THE FIRST LOGON!'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Any information about our network you can find on our page'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}

	public function userAddMailBodyNoToken(array $d) {
		$info = "\n";
		$info .= 'Aby dokończyć proces aktywacji konta, zgłoś się do swojego administratora'."\n";
		$info .= 'lokalnego z wejściówką do DS-u. Godziny, w których możesz go zastać znajdziesz'."\n";
		$info .= 'tutaj: http://skos.ds.pg.gda.pl/'."\n";
		$infoEn = "\n";
		$infoEn .= 'To finish activation procedure you must go to your local administrator in his duty hours:'."\n";
		$infoEn .= 'http://skos.ds.pg.gda.pl/'."\n";
		$infoEn .= 'with your tenant card.'."\n";
		$this->userAddMailBody($d, $info, $infoEn);
	}

	public function userAddMailBodyNoInfo(array $d) {
		$this->userAddMailBody($d);
	}

	public function userAddMailBodyToken(array $d) {
		$info = "\n";
		$info .= 'Aby aktywować swoje konto, kliknij:'."\n";
		$info .= 'http://'.$d['host'].$this->url(0).'/'.$d['token']->token."\n";
		$infoEn = "\n";
		$infoEn .= 'To activate your account click here:'."\n";
		$infoEn .= 'http://'.$d['host'].$this->url(0).'/'.$d['token']->token."\n";
		$this->userAddMailBody($d, $info, $infoEn);
	}

	public function mailHeaders() {
		echo 'MIME-Version: 1.0'."\n";
		echo 'Content-Type: text/plain; charset=UTF-8'."\n";
		echo 'Content-Transfer-Encoding: 8bit'."\n";
		echo 'From: Administratorzy SKOS <adnet@ds.pg.gda.pl>'."\n";
	}

	public function userAddMailHeaders(array $d) {
		$this->mailHeaders();
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function userInfo(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Ważne informacje');
		echo $d['penalties']->write('listPenalty');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userPenalties(array $d) {
		$d['penalties']->write('listAllPenalty');
	}


	public function titlePenalties() {
		echo 'Archiwum kar i ostrzeżeń';
	}

	public function penaltiesNotFound() {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Ważne informacje');
		echo "<h3>Hurra! Brak aktywnych kar i ostrzeżeń! ;)</h3>";
		echo $form->_end();
		echo $form->_end(true);
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

		echo $d['allServices']->write('formEdit', $d['userServices']);
		echo $form->_end(true);
	}


	public function titleServices() {
		echo 'Panel Usług Użytkownika';
	}

	public function userServicesNotFound() {
		echo "<h3>Nie znaleziono usług</h3>";
	}

	public function userMainMenu() {
		echo '<div class="mainMenu"><h1>System Rejestracji Użytkowników</h1>';
		if ($this->_srv->get('msg')->get('userConfirm/ok')) {
			echo $this->OK('Konto zostało aktywowane - teraz <a href="'.$this->url(0).'/computers">dodaj komputer</a>');
		}
		echo '<ul>';
		echo '<li><a href="'.$this->url(0).'/profile">Profil</a></li>';
		echo '<li><a href="'.$this->url(0).'/computers">Komputery</a></li>';
		echo '<li><a href="'.$this->url(0).'/penalties">Kary</a></li>';
		//echo '<li><a href="'.$this->url(0).'/services">Usługi</a></li>';
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
			echo $this->OK('Komputer został dodany');
		} elseif ($this->_srv->get('msg')->get('computerDel/ok')) {
			echo $this->OK('Komputer został wyrejestrowany');
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
		echo $this->ERR('Komputera nie znaleziono');
	}

	public function userComputerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset('Zmień dane komputera');
		if ($this->_srv->get('msg')->get('computerEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
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

	public function userBar(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/', array('class'=>'userBar'));
		echo $form->_fieldset();
		echo $d['user']->write(__FUNCTION__);
		echo $form->_submit('Wyloguj', array('name'=>'userLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function recoverPassword(array $d) {
		$form = UFra::factory('UFlib_Form', 'sendPassword');

		echo $form->_start($this->url(0));
		echo $form->_fieldset('Nie pamiętam hasła');

		if ($this->_isOK('sendPassword')) {
			echo $this->OK('Kliknij link, który został wysłany na maila.');
		} elseif ($this->_isOK('userConfirmPassword')) {
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

	public function userRecoverPasswordMailTitle(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		echo $conf->emailPrefix.' Zmiana hasła';
	}

	public function userRecoverPasswordMailBodyToken(array $d) {
		echo 'Kliknij poniższy link, aby zmienić hasło do Twojego konta w SRU'."\n";
		echo '(Systemie Rejestracji Użytkowników):'."\n";
		echo 'http://'.$d['host'].$this->url(0).'/'.$d['token']->token."\n\n";
		echo '- - - ENGLISH VERSION - - -'."\n";
		echo 'Follow this link to change your password in User Register System:'."\n";
		echo 'http://'.$d['host'].$this->url(0).'/'.$d['token']->token."\n\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}

	public function userRecoverPasswordMailBodyPassword(array $d) {
		echo 'Twój login: '.$d['user']->login."\n";
		echo 'Twoje nowe hasło: '.$d['password']."\n\n";
		echo 'System Rejestracji Użytkowników: http://'.$d['host'].'/'."\n";
		echo 'PROSIMY O ZMIANĘ HASŁA ZARAZ PO PIERWSZYM ZALOGOWANIU!'."\n\n";
		echo '- - - ENGLISH VERSION - - -'."\n";
		echo 'Your login: '.$d['user']->login."\n";
		echo 'Your new password: '.$d['password']."\n\n";
		echo 'User Register System: http://'.$d['host'].'/'."\n";
		echo 'PLEASE CHANGE YOUR PASSWORD JUST AFTER THE FIRS LOG IN!'."\n\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}

	public function userRecoverPasswordMailHeaders(array $d) {
		$this->mailHeaders();
	}
	
	public function penaltyAddMailTitle(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo $conf->emailPrefix.' Otrzymał(a/e)ś ostrzeżenie / You got a new warning';
		} else {
			echo $conf->emailPrefix.' Otrzymał(a/e)ś karę / You got a new penalty';
		}
	}
	
	public function penaltyAddMailBody(array $d) {
		echo 'Informujemy, że otrzymał(a/e)ś ';
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'OSTRZEŻENIE';
		} else {
			echo 'KARĘ';
		}
		echo ' w SKOS PG.'."\n";
		echo 'Trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt)."\n";
		if ($d['penalty']->typeId != UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Kara nałożona na host(y): ';
			foreach ($d['computers'] as $computer) {
				if (is_array($computer)) {
					echo $computer['host'].' ';
				} else {
					echo $computer->host.' ';
				}
			}
		}
		echo 'Powód: '.$d['penalty']->reason."\n";
		echo "\n".'Szczegółowe informacje znajdziesz w Systemie Rejestracji Użytkownika: http://sru.ds.pg.gda.pl';
		echo ' (Twój login to: '.$d['user']->login.')';
		echo "\n\n";
		echo '- - - ENGLISH VERSION - - -'."\n";
		echo 'We inform, that you got ';
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'WARNING';
		} else {
			echo 'PENALTY';
		}
		echo ' in out network.'."\n";
		echo 'Valid till: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt)."\n";
		if ($d['penalty']->typeId != UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Banned host(s): ';
			foreach ($d['computers'] as $computer) {
				if (is_array($computer)) {
					echo $computer['host'].' ';
				} else {
					echo $computer->host.' ';
				}
			}
		}
		echo 'Reason: '.$d['penalty']->reason."\n";
		echo "\n".'You can find more information in User Register System: http://sru.ds.pg.gda.pl';
		echo ' (your login: '.$d['user']->login.')';
		echo "\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}
	
	public function penaltyAddMailHeaders(array $d) {
		$this->mailHeaders();
	}

	public function penaltyEditMailTitle(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo $conf->emailPrefix.' Zmodyfikowano Twoje ostrzeżenie / Your warning was modified';
		} else {
			echo $conf->emailPrefix.' Zmodyfikowano Twoją karę / Your penalty was modified';
		}
	}
	
	public function penaltyEditMailBody(array $d) {
		echo 'Informujemy, że zmodyfikowano ';
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Twoje OSTRZEŻENIE';
		} else {
			echo 'Twoją KARĘ';
		}
		echo ' w SKOS PG.'."\n";
		echo 'Teraz trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt)."\n";
		echo 'Powód: '.$d['penalty']->reason."\n";
		echo "\n".'Szczegółowe informacje znajdziesz w Systemie Rejestracji Użytkownika: http://sru.ds.pg.gda.pl';
		echo ' (Twój login to: '.$d['user']->login.')';
		echo "\n\n";
		echo '- - - ENGLISH VERSION - - -'."\n";
		echo 'We inform, that your ';
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'WARNING';
		} else {
			echo 'PENALTY';
		}
		echo ' was modified in out network.'."\n";
		echo 'Now valid till: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt)."\n";
		echo 'Reason: '.$d['penalty']->reason."\n";
		echo "\n".'You can find more information in User Register System: http://sru.ds.pg.gda.pl';
		echo ' (your login: '.$d['user']->login.')';
		echo "\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}
	
	public function penaltyEditMailHeaders(array $d) {
		$this->mailHeaders();
	}

	public function dataChangedMailTitle(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		echo $conf->emailPrefix.' Zmieniłeś swoje dane / You have changed your data';
	}
	
	public function dataChangedMailBody(array $d) {
		echo 'Potwierdzamy, że zmiana Twoich danych w SKOS PG została zapisana.'."\n\n";
		echo 'Imię: '.$d['user']->name."\n";
		echo 'Nazwisko: '.$d['user']->surname."\n";
		echo $d['user']->dormitoryName."\n";
		echo 'Pokój: '.$d['user']->locationAlias."\n";
		echo 'Login: '.$d['user']->login."\n";
		echo 'Numer GG: '.$d['user']->gg."\n";
		echo "\n".'- - - ENGLISH VERSION - - -'."\n";
		echo 'We comfirm, that change of your personal data in SKOS PG has been saved.'."\n\n";
		echo 'Name: '.$d['user']->name."\n";
		echo 'Surname: '.$d['user']->surname."\n";
		echo $d['user']->dormitoryName."\n";
		echo 'Room: '.$d['user']->locationAlias."\n";
		echo 'Login: '.$d['user']->login."\n";
		echo 'GG number: '.$d['user']->gg."\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}
	
	public function dataChangedMailHeaders(array $d) {
		$this->mailHeaders();
	}

	public function hostChangedMailTitle(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		echo $conf->emailPrefix.' Zmieniłeś dane Twojego hosta / You have changed your host data';
	}
	
	public function hostChangedMailBody(array $d) {
		if ($d['action'] == UFact_Sru_Computer_Add::PREFIX) {
			echo 'Potwierdzamy, że do Twojego konta w SKOS PG został dodany nowy host.'."\n\n";
		} else if ($d['action'] == UFact_Sru_Computer_Edit::PREFIX) {
			echo 'Potwierdzamy, że zmiana danych Twojego hosta w SKOS PG została zapisana.'."\n\n";
		} else {
			echo 'Potwierdzamy dezaktywację Twojego hosta w SKOS PG.'."\n\n";
		}
		echo 'Nazwa hosta: '.$d['host']->host."\n";
		echo 'Ważny do: '.date(self::TIME_YYMMDD,$d['host']->availableTo)."\n";
		echo 'IP: '.$d['host']->ip."\n";
		echo 'Adres MAC: '.$d['host']->mac."\n";
		echo "\n".'- - - ENGLISH VERSION - - -'."\n";
		if ($d['action'] == UFact_Sru_Computer_Add::PREFIX) {
			echo 'We confirm, that a new host has been added to your SKOS PG account.'."\n\n";
		} else if ($d['action'] == UFact_Sru_Computer_Edit::PREFIX) {
			echo 'We confirm, that change ofyour host data in SKOS PG has been saved.'."\n\n";
		} else {
			echo 'We confirm, that your host in SKOS PG has been deactivated.'."\n\n";
		}
		echo 'Host name: '.$d['host']->host."\n";
		echo 'Available to: '.date(self::TIME_YYMMDD,$d['host']->availableTo)."\n";
		echo 'IP: '.$d['host']->ip."\n";
		echo 'MAC address: '.$d['host']->mac."\n";
		echo '-- '."\n";
		echo 'Pozdrawiamy / Regards,'."\n";
		echo 'Administratorzy SKOS PG / SKOS PG Administrators'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo '[wiadomość została wygenerowana automatycznie / this message was generated automatically]'."\n";
	}
	
	public function hostChangedMailHeaders(array $d) {
		$this->mailHeaders();
	}
}
