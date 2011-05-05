<?
/**
 * szablon modulu administracji sru
 */
class UFtpl_SruAdmin
extends UFtpl_Common {

	public function titleLogin() {
		echo 'Zaloguj się';
	}

	public function login(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Zaloguj się');
		if ($this->_srv->get('msg')->get('adminLogin/errors')) {
			echo $this->ERR('Nieprawidłowy login lub hasło');
		}
		echo $d['admin']->write('formLogin');
		echo $form->_submit('Zaloguj');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function logout(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
		echo $form->_fieldset('Wyloguj się');
		echo $d['admin']->write('formLogout');
		echo $form->_submit('Wyloguj', array('name'=>'adminLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function title() {
		echo 'Administracja SKOS';
	}

	public function menuAdmin() {
		echo '<ul id="nav">';
		echo '<li><a href="'.UFURL_BASE.'/admin/">Użytkownicy</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/penalties/">Kary</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/dormitories/">Akademiki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/stats/">Statystyki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/admins/">Administratorzy</a></li>';
		echo '</ul>';
	}

	public function adminBar(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/', array('class'=>'adminBar'));
		echo $form->_fieldset();
		if($d['admin']->active == true && $d['admin']->activeTo - time() <= UFra::shared('UFconf_Sru')->adminDeactivateAfter && $d['admin']->activeTo - time() >= 0)
			echo '<span title="Zbliża się czas dezaktywacji konta" style="color: red;">(!) </span>';
		echo $d['admin']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt']);
		echo $form->_submit('Wyloguj', array('name'=>'adminLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}
	
	public function titleComputer(array $d) {
		echo $d['computer']->write('titleDetails');
	}

	public function titleComputerNotFound() {
		echo 'Komputera nie znaleziono';
	}

	public function computerNotFound() {
		echo $this->ERR('Komputera nie znaleziono');
	}

	public function computer(array $d) {
		
		if ($this->_srv->get('msg')->get('computerDel/ok')) {
			echo $this->OK('Komputer wyrejestrowano');
		}
		if ($this->_srv->get('msg')->get('computerAliasesEdit/ok')) {
			echo $this->OK('Zmodyfikowano aliasy komputera');
		}
		$url = $this->url(0).'/computers/'.$d['computer']->id;
		echo '<div class="computer">';
		$d['computer']->write('details', $d['switchPort'], $d['aliases']);
		echo '</div>';
	}

	public function titleComputers() {
		echo 'Wszystkie komputery';
	}
	

	public function serverComputers(array $d) {
		echo '<div class="computers">';
		echo '<h2>Serwery</h2>';

		echo '<ul>';
		$d['computers']->write('listAdmin');
		echo '</ul>';
		echo '</div>';
	}
	public function serverAliases(array $d) {
		echo '<div class="computers">';
		echo '<h2>Aliasy serwerów</h2>';

		echo '<ul>';
		$d['aliases']->write('listAliases');
		echo '</ul>';
		echo '</div>';
	}
	public function administrationComputers(array $d) {
		echo '<div class="computers">';
		echo '<h2>Komputery administracji</h2>';

		echo '<ul>';
		$d['computers']->write('listAdmin');
		echo '</ul>';
		echo '</div>';
	}
	public function organizationsComputers(array $d) {
		echo '<div class="computers">';
		echo '<h2>Komputery organizacji</h2>';

		echo '<ul>';
		$d['computers']->write('listAdmin');
		echo '</ul>';
		echo '</div>';
	}
	public function organizationsComputersNotFound() {
		echo '<h2>Komputery organizacji</h2>';
		echo $this->ERR('Nie znaleziono komputerów');
	}
	public function administrationComputersNotFound() {
		echo '<h2>Komputery administracji</h2>';
		echo $this->ERR('Nie znaleziono komputerów');
	}	
	public function serverComputersNotFound() {
		echo '<h2>Serwery</h2>';
		echo $this->ERR('Nie znaleziono komputerów');
	}
	public function serverAliasesNotFound() {
		echo '<h2>Aliasy serwerów</h2>';
		echo $this->ERR('Nie znaleziono komputerów');
	}

	public function computersNotFound() {
		echo $this->ERR('Komputerów nie znaleziono');
	}

	public function computerHistory(array $d) {
		echo '<div class="computer">';
		echo '<h2>Historia zmian</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['computer']);
		echo '</ol>';
		echo '</div>';
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

	public function titleComputerEdit(array $d) {
		echo $d['computer']->write('titleEdit');
	}

	public function computerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja</h2>';
		echo $form->_start($this->url());
		echo $form->_fieldset('Edycja komputera');
		if (!$this->_srv->get('msg')->get('computerEdit') && $this->_srv->get('req')->get->is('computerHistoryId')) {
			echo $this->ERR('Formularz wypełniony danymi z '.date(self::TIME_YYMMDD_HHMM, $d['computer']->modifiedAt));
		} elseif ($this->_srv->get('msg')->get('computerEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		echo $d['computer']->write('formEditAdmin', $d['dormitories'], $d['user'], $d['history']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleComputerAliasesEdit(array $d) {
		echo $d['computer']->write('titleAliasesEdit');
	}

	public function computerAliasesEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja aliasów</h2>';
		echo $form->_start($this->url());
		echo $d['computer']->write('formAliasesEdit', $d['aliases']);
		echo $form->_submit('Zapisz');
		echo $form->_end(true);
	}

	public function computerAliasesNotFound() {
		echo $this->ERR('Błąd wyświetlania aliasów');
	}

	public function computerDel(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset('Wyrejestruj komputer');
		echo $d['computer']->write('formDelAdmin');
		echo $form->_submit('Wyrejestruj');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function computerSearch(array $d) {
		$form = UFra::factory('UFlib_Form');	
		echo '<div class="computerSearch">';
		echo $form->_start($this->url(0).'/computers/search');
		echo $form->_fieldset('Znajdź komputer');
		echo $d['computer']->write('formSearch', $d['searched']);
		echo $form->_submit('Znajdź');
		echo ' <a href="'.UFURL_BASE.'/admin/computers/">Serwery, administracja, organizacje</a>';
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników';
	}

	public function titleComputerSearch() {
		echo 'Znajdź komputer';
	}

	public function computerSearchResults(array $d) {
		echo '<h2>Znalezione komputery:</h2>';
		echo '<div class="computerSearchResults"><ul>';
		echo $d['computers']->write('searchResults');
		echo '</ul></div>';
	}

	public function computerSearchByAliasResults(array $d) {
		echo '<h2>Znalezione aliasy:</h2>';
		echo '<ul>';
		echo $d['aliases']->write('listAliases');
		echo '</ul></div>';
	}

	public function computerSearchHistoryResults(array $d) {
		echo '<h2>Znalezione komputery używające wcześniej szukanego IP:</h2>';
		echo '<ul>';
		echo $d['computers']->write('searchResults');
		echo '</ul></div>';
	}

	public function computerSearchResultsUnregistered(array $d) {
		echo '<div class="computer">';
		echo $d['computers']->write('searchResultsUnregistered', $d['switchPort']);
		echo '</div>';
	}

	public function computerSearchResultsNotFound() {
		echo '<h2>Znalezione komputery:</h2>';
		echo $this->ERR('Nie znaleziono');
	}

	public function computerSearchByAliasResultsNotFound() {
	}

	public function computerSearchHistoryResultsNotFound() {
	}

	public function titleUserSearch() {
		echo 'Znajdź użytkownika';
	}

	public function userSearch(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Szukaj | <a href="'.$this->url(0).'/services">Usługi</a></h2>';

		echo '<div class="userSearch">';
		echo $form->_start($this->url(0).'/users/search');
		echo $form->_fieldset('Znajdź użytkownika');
		echo $d['user']->write('formSearch', $d['searched']);
		echo $form->_submit('Znajdź');
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function userSearchResults(array $d) {
		echo '<div class="userSearchResults"><ul>';
		echo $d['users']->write('searchResults');
		echo '</ul></div>';
	}

	public function userSearchResultsNotFound() {
		echo $this->ERR('Nie znaleziono');
	}

	public function user(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id;
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			echo $this->OK('Konto zostało założone.');
		}		
		echo '<div class="user">';
		$d['user']->write('details');
		echo '</div>';
	}

	public function userNotFound() {
		echo $this->ERR('Użytkownika nie znaleziono');
	}

	public function titleUser(array $d) {
		echo $d['user']->write('titleDetails');
	}

	public function titleUserNotFound() {
		echo 'Użytkownika nie znaleziono';
	}

	public function userComputers(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id.'/computers/';
		
		echo '<h2>Komputery użytkownika</h2><ul>';
		
		if ($this->_srv->get('msg')->get('computerAdd/ok')) {
			echo $this->OK('Komputer został dodany');
		}
		echo $d['computers']->write('listAdmin');
		echo '</ul>';
		$acl = $this->_srv->get('acl');
		if($acl->sruAdmin('computer', 'addForUser', $d['user']->id)) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj komputer</a></p>';
		}
	}
	public function userInactiveComputers(array $d) {
		$url = $this->url(2).'/computers/';
		
		echo '<h2>Wyrejestrowane komputery</h2><ul>';
			
		echo $d['computers']->write('listAdmin');
		echo '</ul>';
		
	}

	public function userServicesEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2>Usługi użytkownika</h2>';
		echo $form->_start();

		if ($this->_srv->get('msg')->get('serviceEdit/ok')) {
			echo $this->OK('Zmiany zostały zapisane');
		}

		echo $d['allServices']->write('formEdit', $d['userServices'], $d['user'], true);
		echo $form->_end(true);
	}

	public function userServicesNotFound() {
		echo $this->ERR('Usług nie znaleziono');
	}

	public function userComputersNotFound(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id.'/computers/';
		echo '<h2>Komputery użytkownika</h2>';
		$acl = $this->_srv->get('acl');
		if($acl->sruAdmin('computer', 'addForUser', $d['user']->id)) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj komputer</a></p>';
		}
	}
	public function userInactiveComputersNotFound() {
	}
	public function titleUserEdit(array $d) {
		echo $d['user']->write('titleEdit');
	}

	public function titleUserEditNotFound(array $d) {
		$this->titleUserNotFound();
	}

	public function userEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja</h2>';
		echo $form->_start($this->url());
		echo $form->_fieldset('Edycja użytkownika');
		if (!$this->_srv->get('msg')->get('userEdit') && $this->_srv->get('req')->get->is('userHistoryId')) {
			echo $this->ERR('Formularz wypełniony danymi z '.date(self::TIME_YYMMDD_HHMM, $d['user']->modifiedAt));
		} elseif ($this->_srv->get('msg')->get('userEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('userEdit/loginChanged')) {
			echo $this->OK('W związku ze zmianą loginu, użytkownik będzie musiał przejść procedurę zmiany hasła.');
		}
		echo $d['user']->write('formEditAdmin', $d['faculties']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userHistory(array $d) {
		echo '<div class="user">';
		echo '<h2>Historia profilu</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['user']);
		echo '</ol>';
		echo '</div>';
	}

	public function serviceHistory(array $d) {
		echo '<div class="user">';
		echo '<h2>Historia usług</h2>';
		echo '<ol class="history">';
		$d['servicehistory']->write('table', $d['user']);
		echo '</ol>';
		echo '</div>';
	}

	public function titleAdmins() {
		echo 'Administratorzy';
	}

	public function admins(array $d) {
		$url = $this->url(0).'/admins/';
		$acl = $this->_srv->get('acl');
		
		if ($this->_srv->get('msg')->get('adminAdd/ok')) {
			echo $this->OK('Konto zostało założone');
		}
		
		echo '<div class="admins">';
		echo '<h2>Administratorzy</h2>';
		$d['admins']->write('listAdmin');
		echo '</div>';
		
		if($acl->sruAdmin('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
	}

	public function inactiveAdmins(array $d) {
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins inactive">';
		echo '<h2>Nieaktywni Administratorzy</h2>';

		$d['admins']->write('listAdmin');

		echo '</div>';
	}

	public function bots(array $d) {
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins">';
		echo '<h2>Boty</h2>';

		$d['admins']->write('listBots');

		echo '</div>';
	}

	public function waletAdmins(array $d) {
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins">';
		echo '<h2>Pracownicy Osiedla</h2>';

		$d['admins']->write('listAdminSimple');

		echo '</div>';
	}

	public function titleAdminNotFound() {
		echo 'Nie znaleziono administratora';
	}

	public function adminsNotFound() {
		echo '<h2>Administratorzy</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
		
		if($acl->sruAdmin('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
		
	}
	public function botsNotFound() {
		echo '<h2>Boty</h2>';
		echo $this->ERR('Nie znaleziono botów');
	}
	public function inactiveAdminsNotFound() {
		echo '<h2>Nieaktywni Administratorzy</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
	}		
	public function admin(array $d) {
		$url = $this->url(0).'/admins/'.$d['admin']->id;
		$acl = $this->_srv->get('acl');
		
		if ($this->_srv->get('msg')->get('adminEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		
		echo '<div class="admin">';
		$d['admin']->write('details');
		
		echo '<p class="nav">';
		if($acl->sruAdmin('admin', 'edit', $d['admin']->id)) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a></p></div>';
	}

	public function adminDutyHours(array $d) {
		$url = $this->url(0).'/admins/'.$d['admin']->id;
		$acl = $this->_srv->get('acl');

		echo '<h3>Godziny dyżurów</h3>';
		$d['hours']->write('listDutyHours');
		if($acl->sruAdmin('admin', 'edit', $d['admin']->id)) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a>';
	}

	public function adminDutyHoursNotFound() {
		echo '<h3>Godziny dyżurów</h3>';
		echo $this->ERR('Brak godzin dyżurów');
	}

	public function adminDorms(array $d) {
		if (is_null($d['dormList'])) {
			$this->adminDormsNotFound();
			return;
		}

		$url = $this->url(0).'/admins/'.$d['admin']->id;
		$acl = $this->_srv->get('acl');

		echo '<h3>Domy studenckie</h3>';
		$d['admin']->write('listDorms', $d['dormList']);
		if($acl->sruAdmin('admin', 'changeAdminDorms')) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a>';
	}

	public function adminDormsNotFound() {
		echo '<h3>Domy studenckie</h3>';
		echo $this->ERR('Brak przypisanych DSów');
	}

	public function penaltyTemplateChoose(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id;

		echo '<h2>Typ kary dla <a href="'.$url.'">'.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</a></h2>';
		echo '<ul class="penaltyTemplates">';
		$d['templates']->write('choose');
		echo '</ul>';
	}

	public function penaltyTemplateChange(array $d) {
		echo '<h2>Edycja typu kary dla '.$d['penalty']->userName.' '.$d['penalty']->userSurname.' ('.$d['penalty']->userLogin.')</h2>';
		echo '<ul class="penaltyTemplates">';
		$d['templates']->write('choose');
		echo '</ul>';
	}

	public function titlePenaltyTemplateAdd(array $d) {
		echo 'Dodawanie nowego szablonu';
	}

	public function penaltyTemplateAdd(array $d) {
		$url = $this->url(2);

		$form = UFra::factory('UFlib_Form');
		echo '<h2>Nowy szablon kary</h2>';
		echo $form->_start();
		$d['template']->write('formAdd');
		echo $form->_submit('Dodaj');
		echo ' <a href="'.$url.'/">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titlePenaltyTemplateEdit(array $d) {
		echo 'Edycja szablonu '.$d['template']->title;
	}

	public function penaltyTemplateEdit(array $d) {
		$url = $this->url(2);

		$form = UFra::factory('UFlib_Form');
		echo '<h2>Edycja szablonu '.$d['template']->title.'</h2>';
		echo $form->_start();
		$d['template']->write('formEdit');
		echo $form->_submit('Edytuj');
		echo ' <a href="'.$url.'/">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function penaltyTemplatesNotFound() {
		echo $this->ERR('Nie znaleziono szablonów kar');
	}

	public function adminNotFound() {
		echo $this->ERR('Nie znaleziono administratora');
	}	

	public function titleAdmin(array $d) {
		echo $d['admin']->write('titleDetails');
	}	
	public function adminAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2>Dodawanie nowego administratora</h2>';
		echo $form->_start();
		
		echo $d['admin']->write('formAdd', $d['dormitories']);
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);
	}
	public function titleAdminAdd(array $d) {
		echo 'Dodawanie nowego administratora';
	}	
	public function titleAdminEdit(array $d) {
		echo $d['admin']->write('titleDetails');
	}
	public function adminEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2>Edycja administratora</h2>';
		echo $form->_start();
		

		echo $d['admin']->write('formEdit', $d['dormitories'], $d['dutyHours'], $d['dormList'], $d['advanced']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$this->url(0).'/admins/'.$d['admin']->id.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}
	public function titleDormitories() {
		echo 'Akademiki';
	}	
	public function dorms(array $d)
	{
		$url = $this->url(0).'/admins/';
			
		echo '<div class="dormitories">';
		echo '<h2>Akademiki</h2>';

		$d['dorms']->write('listDorms');
		echo '</div>';
	}
	public function titleDorm(array $d) {
		echo $d['dorm']->write('titleDetails');
	}
	public function dorm(array $d) {
		echo '<div class="dorm">';		
		$d['dorm']->write('details');
		
		if($d['rooms']) {
			echo '<div class="rooms">';
			echo '<h3>Pokoje</h3>';			

			$d['rooms']->write('listRooms');
			echo '</div>';
		}

		echo '</div>';
	}
	public function dormNotFound() {
		echo $this->ERR('Nie znaleziono akademika');
	}	
	public function titleDormNotFound() {
		echo 'Nie znaleziono akademika';
	}		
	public function dormsNotFound() {
		echo $this->ERR('Nie znaleziono akademików');
	}

	public function titleSwitches() {
		echo 'Switche';
	}	
	public function switches(array $d) {
		$url = $this->url(0).'/dormitories/';
		$urlIp = $this->url(0).'/ips/';

		if (!is_null($d['dorm'])) {
			echo '<h2>'.$d['dorm']->name;
			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; <a href="'.$urlIp.$d['dorm']->alias.'">komputery</a> &bull; liczba switchy: '.count($d['switches']).')</small></h2>';
		} else {
			echo '<h2>Switche</h2>';
		}

		if ($this->_srv->get('msg')->get('switchAdd/ok')) {
			echo $this->OK('Switch został dodany');
		}

		$d['switches']->write('listSwitches', $d['dorm']);
		echo '</div>';
	}
	public function switchesNotFound() {
		$url = $this->url(0).'/switches/';
		echo $this->ERR('Nie znaleziono switchy<br/><a href="'.$url.':add">Dodaj nowego switcha</a>');
	}

	public function titleSwitch(array $d) {
		echo $d['switch']->write('titleDetails');
	}

	public function switchDetails(array $d) {
		if ($this->_srv->get('msg')->get('switchEdit/ok')) {
			echo $this->OK('Dane switcha zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('switchPortsEdit/ok')) {
			echo $this->OK('Dane portów switcha zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('switchLockoutsEdit/ok')) {
			echo $this->OK('Zablokowane adresy MAC na switchu zostały zmienione');
		}
		$d['switch']->write('headerDetails');
		$d['switch']->write('details', $d['info'], $d['lockouts']);
	}

	public function switchData(array $d) {
		echo json_encode($d['info']);
	}

	public function switchTech(array $d) {
		$d['switch']->write('techDetails', $d['info'], $d['gbics']);
	}

	public function switchPorts(array $d) {
		$d['ports']->write('listPorts', $d['switch'], $d['portStatuses'], $d['trunks'], $d['port']);
	}

	public function roomSwitchPorts(array $d) {
		echo '<h3>Przypisane porty</h3>';
		$d['ports']->write('listRoomPorts', $d['room'], $d['portStatuses']);
	}

	public function switchPortDetails(array $d) {
		if ($this->_srv->get('msg')->get('switchPortEdit/ok')) {
			echo $this->OK('Dane portu switcha zostały zmienione');
		}
		$d['port']->write('details', $d['switch'], $d['alias']);
	}

	public function switchPortMacs(array $d) {
		$d['port']->write('portMacs', $d['switch'],$d['macs']);
	}

	public function switchNotFound() {
		echo $this->ERR('Nie znaleziono switcha');
	}

	public function titleSwitchNotFound() {
		echo 'Nie znaleziono switcha';
	}

	public function switchPortsNotFound(array $d) {
		echo $this->ERR('Nie znaleziono portów switcha');
	}

	public function switchAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);

		echo '<h2>Dodawanie nowego switcha</h2>';
		echo $form->_start();
		echo $d['switch']->write('formAdd', $d['dormitories'], $d['swModels']);
		echo $form->_submit('Dodaj');
		echo ' <a href="'.$url.'/switches/">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleSwitchAdd(array $d) {
		echo 'Dodawanie nowego switcha';
	}

	public function switchEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);

		echo '<h2>Edycja switcha <a href="'.$this->url(0).'/switches/'.$d['switch']->serialNo.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d['switch']->dormitoryAlias, $d['switch']->hierarchyNo).'</a></h2>';
		echo $form->_start();
		echo $d['switch']->write('formEdit', $d['dormitories'], $d['swModels']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$url.'/switches/'.$d['switch']->serialNo.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleSwitchEdit(array $d) {
		echo $d['switch']->write('titleEditDetails');
	}

	public function switchPortsEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);

		echo '<h2>Edycja portów switcha <a href="'.$this->url(0).'/switches/'.$d['switch']->serialNo.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d['switch']->dormitoryAlias, $d['switch']->hierarchyNo).'</a></h2>';
		echo $form->_start();
		echo $d['ports']->write('formEdit', $d['switch'], $d['enabledSwitches'], $d['portAliases']);
		echo $form->_end();
		echo $form->_end(true);
	}

	public function switchPortEdit(array $d) {
		$url = $this->url(0);
		$form = UFra::factory('UFlib_Form');
		echo $form->_start();
		echo $d['port']->write('formEditOne', $d['switch'], $d['enabledSwitches'], $d['status']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$url.'/switches/'.$d['switch']->serialNo.'/port/'.$d['port']->ordinalNo.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleSwitchPortsEdit(array $d) {
		echo $d['switch']->write('titlePortsEditDetails');
	}

	public function switchLockoutsEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);

		echo '<h3>Edycja zablokowanych adresów MAC na switchu</h3>';
		echo $form->_start();
		echo $d['switch']->write('formEditLockouts', $d['lockouts']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$url.'/switches/'.$d['switch']->serialNo.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleRoom(array $d) {
		echo $d['room']->write('titleDetails');
	}

	public function room(array $d) {
		if ($this->_srv->get('msg')->get('roomEdit/ok')) {
			echo $this->OK('Komentarz został zmieniony');
		}
		
		$d['room']->write('details');
	}

	public function roomNotFound() {
		echo $this->ERR('Nie znaleziono pokoju');
	}	

	public function roomUsers(array $d) {
		echo '<h3>Użytkownicy</h3><ul>';
		$d['users']->write('shortList');
		echo '</ul>';
	}

	public function roomUsersNotFound() {
		echo '<h3>Użytkownicy</h3>';
		echo $this->ERR('Brak użytkowników');
	}

	public function roomComputers(array $d) {
		echo '<h3>Komputery</h3><ul>';
		$d['computers']->write('shortList');
		echo '</ul>';
	}

	public function roomComputersNotFound() {
		echo '<h3>Komputery</h3>';
		echo $this->ERR('Brak komputerów');
	}

	public function titleRoomNotFound() {
		echo 'Nie znaleziono pokoju';
	}		
	public function roomsNotFound() {
		echo $this->ERR('Nie znaleziono pokoi');
	}
	public function roomEdit(array $d) {
		echo $d['room']->write('formEdit');
	}
	public function titleComputerAdd() {
		echo 'Dodaj komputer';
	}	
	public function computerAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Dodaj komputer');
		echo $d['computer']->write('formAdd', true);
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);
		
	}

	public function titlePenalties() {
		echo 'Aktywne kary';
	}	

	public function penalties(array $d)
	{
		$url = $this->url(0).'/penalties/';
		$acl = $this->_srv->get('acl');
		
		echo '<h2><a href="'.$url.'">Ostatnie akcje</a>| Aktywne kary | <a href="'.$url.'templates">Szablony</a></h2>';

		$d['penalties']->write('listPenalty');
	}	

	public function penaltiesNotFound() {
		echo $this->ERR('Nie znaleziono aktywnych kar');
	}

	public function titlePenaltyAdd(array $d) {
		echo 'Kara dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')';
	}	

	public function penaltyAdd(array $d) {
		$url = $this->url(0).'/penalties/';
		$urlUser = $this->url(0).'/users/'.$d['user']->id;
		$acl = $this->_srv->get('acl');
		
		echo '<h2>Kara dla <a href="'.$urlUser.'">'.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</a></h2>';
		
		echo '<div class="penalty">';
	
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset();
		isset($d['computerId']) ? $computerId = $d['computerId'] : $computerId = null;
		echo $d['penalty']->write('formAdd', $d['computers'], $d['templates'], $d['user'], $computerId);
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);		

		echo '</div>';
					
	}

	public function titlePenalty(array $d) {
		echo 'Kara / Ostrzeżenie';
	}

	public function penalty(array $d) {
		if ($this->_srv->get('msg')->get('penaltyAdd/ok')) {
			echo $this->OK('Kara została założona');
		}
		if ($this->_srv->get('msg')->get('penaltyEdit/ok')) {
			echo $this->OK('Zmiany zostały wprowadzone');
		}
		echo '<div class="penalty">';	
		echo '<h2>';

		if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $d['penalty']->typeId) {
			echo 'Ostrzeżenie';
		} else {
			echo 'Kara';
		}
		echo '</h2>';
		
		$d['penalty']->write('details', $d['computers']);
		
		echo '</div>';
	}

	public function titlePenaltyEdit(array $d) {
		echo 'Edycja kary';
	}

	public function penaltyEdit(array $d) {
		if ($this->_srv->get('msg')->get('penaltyEdit/errors/endAt')) {
			echo $this->ERR('Nieprawidłowa data');
		}

		echo '<div class="penalty">';	
		echo '<h2>Edycja ';

		if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $d['penalty']->typeId) {
			echo 'Ostrzeżenia';
		} else {
			echo 'Kary';
		}
		echo '</h2>';
		$templateTitle = isset($d['templateTitle']) ? $d['templateTitle'] : null;
		$d['penalty']->write('formEdit', $d['computers'], $templateTitle);
		
		echo '</div>';
	}

	public function penaltyHistory(array $d) {
		$d['history']->write('history', $d['penalty']);
	}

	public function penaltyNotFound() {
		echo $this->ERR('Nie znaleziono kary');
	}

	public function titleUserPenalties(array $d) {
		echo 'Lista kar i ostrzeżeń dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')';
	}	

	public function userPenalties(array $d)
	{
		$acl = $this->_srv->get('acl');		
		
		echo '<h2>Lista kar dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</h2>';

		$d['penalties']->write('listUserPenalty');
	}	
	
	public function titleComputerPenalties(array $d) {
		echo 'Lista kar i ostrzeżeń dla hosta '.$d['computer']->host;
	}	

	public function computerPenalties(array $d)
	{
		$acl = $this->_srv->get('acl');		
		
		echo '<h2>Lista kar dla hosta '.$d['computer']->host.'</h2>';

		$d['penalties']->write('listComputerPenalty');
	}

	public function penaltyActions(array $d)
	{
		$url = $this->url(0).'/penalties/';
		$acl = $this->_srv->get('acl');		
		
		echo '<h2>Ostatnie akcje | <a href="'.$url.'active">Aktywne kary</a> | <a href="'.$url.'templates">Szablony</a></h2>';
		
		echo '<h3>Modyfikacje kar</h3>';
		$d['modifiedPenalties']->write('penaltyLastModified');
		echo '<h3>Nowe kary</h3>';
		$d['addedPenalties']->write('penaltyLastAdded');
		echo '<h3>Nowe ostrzeżenia</h3>';
		$d['addedWarnings']->write('penaltyLastAdded');

	}

	public function titlePenaltyActions() {
		echo 'Ostatnie akcje na karach i ostrzeżeniach';
	}

	public function titlePenaltyTemplates() {
		echo 'Szablony kar';
	}

	public function penaltyTemplates(array $d) {
		$url = $this->url(0).'/penalties/';

		if ($this->_srv->get('msg')->get('penaltyTemplateAdd/ok')) {
			echo $this->OK('Szablon został dodany');
		}
		if ($this->_srv->get('msg')->get('penaltyTemplateEdit/ok')) {
			echo $this->OK('Zmiany w szablonie zostały wprowadzone');
		}
		
		echo '<h2><a href="'.$url.'">Ostatnie akcje</a> | <a href="'.$url.'active">Aktywne kary</a> | Szablony</h2>';
		
		if (!is_null($d['templates'])) {
			echo '<h3>Aktywne szablony</h3>';
			echo '<ul class="penaltyTemplates">';
			$d['templates']->write('listEdit');
			echo '</ul>';
		}
		$acl = $this->_srv->get('acl');
		if($acl->sruAdmin('penaltyTemplate', 'add')) {
			echo '<p class="nav"><a href="'.$url.'templates/:add">Dodaj nowy szablon</a></p>';
		}
		if (!is_null($d['inactive'])) {
			echo '<h3>Nieaktywne szablony</h3>';
			echo '<ul class="penaltyTemplates">';
			$d['inactive']->write('listEdit');
			echo '</ul>';
		}
	}
	
	public function titleIps() {
		echo 'Zestawienie numerów IP';
	}
	
	public function ips(array $d) {
		$url = $this->url(0).'/dormitories/';
		$urlSw = $this->url(0).'/switches/dorm/';

		if (!is_null($d['dorm'])) {
			echo '<h2>'.$d['dorm']->name;
			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; zajętość puli: '.$d['used']->getIpCount().'/'.$d['sum']->getIpCount().' ~> '.($d['sum']->getIpCount() > 0 ? round($d['used']->getIpCount()/$d['sum']->getIpCount()*100) : 0).'% &bull; <a href="'.$urlSw.$d['dorm']->alias.'">switche</a>)</small></h2>';
		} else {
			echo '<h2>Zestawienie numerów IP</h2>';
		}
		echo '<div class="ips">';
		
		$d['ips']->write('ips', $d['dorm']);
		echo '</div>';
	}						
	
	public function ipsNotFound(array $d) {
		if (!is_null($d['dorm'])) {
			echo '<h2><a href="'.$this->url(0).'/dormitories/'.$d['dorm']->alias.'">'.$d['dorm']->name.'</a></h2>';
		} else {
			echo '<h2>Zestawienie numerów IP</h2>';
		}
		echo $this->ERR('Brak adresów IP dla tego DS-u');
	}

	public function titleStatsTransfer() {
		echo 'Statystyki transferu';
	}
	
	public function statsTransfer(array $d) {
		echo '<h2>Transfer | <a href="'.$this->url(1).'/users">Użytkownicy</a> | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		echo '<h3>Ranking uploaderów</h3>';
		$d['transfer']->write('transferStats');
	}

	public function statsTransferNotFound() {
		echo '<h2>Transfer | <a href="'.$this->url(1).'/users">Użytkownicy</a> | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}
	
	public function titleStatsUsers() {
		echo 'Statystyki użytkowników';
	}
	
	public function statsUsers(array $d) {
		echo '<h2><a href="'.$this->url(1).'">Transfer</a> | Użytkownicy | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		$d['users']->write('stats');
	}
	
	public function statsUsersNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function titleStatsPenalties() {
		echo 'Statystyki kar';
	}
	
	public function statsPenalties(array $d) {
		echo '<h2><a href="'.$this->url(1).'">Transfer</a> | <a href="'.$this->url(1).'/users">Użytkownicy</a> | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | Kary</h2>';
		$d['penalties']->write('stats');
	}
	
	public function statsPenaltiesNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function titleStatsDormitories() {
		echo 'Statystyki akademików';
	}
	
	public function statsDormitories(array $d) {
		echo '<h2><a href="'.$this->url(1).'">Transfer</a> | <a href="'.$this->url(1).'/users">Użytkownicy</a> | Akademiki | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		$d['users']->write('statsDorms');
	}
	
	public function statsDormitoriesNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function adminPenaltiesAdded(array $d) {
		echo '<h3>Kary ostatnio dodane</h3>';
		$d['addedPenalties']->write('penaltyLastAdded', false);
	}

	public function adminWarningsAdded(array $d) {
		echo '<h3>Ostrzeżenia ostatnio dodane</h3>';
		$d['addedWarnings']->write('penaltyLastAdded', false);
	}

	public function adminPenaltiesModified(array $d) {
		echo '<h3>Kary ostatnio modyfikowane</h3>';
		$d['modifiedPenalties']->write('penaltyLastModified');
	}
	
	/**
	 * Tytuł do sekcji ostatnich akcji na użytkownikach
	 *
	 */
	public function adminUsersModified(array $d) {
		echo '<h3>Osoby ostatnio modyfikowane/dodane</h3>';
		$d['modifiedUsers']->write('userLastModified');
	}
	
	/**
	 * Tytuł do sekcji ostatnich akcji na komputerach
	 *
	 */
	public function adminComputersModified(array $d) {
		echo '<h3>Komputery ostatnio modyfikowane/dodane</h3>';
		$d['modifiedComputers']->write('computerLastModified');
	}
	
	/**
	 * Tytuł do sekcji ostatnio modyfikowanych usług
	 *
	 */
	public function adminUserServicesModified(array $d) {
		echo '<h3>Usługi aktywowane/dezaktywowane (poziom admina)</h3>';
		$d['modifiedUserServices']->write('userServiceLastModified');
	}
	
	/**
	 * Tytuł do sekcji ostatnich próśb o aktywację/dezaktywację usług
	 *
	 */
	public function adminUserServicesRequested(array $d) {
		echo '<h3>Usługi aktywowane/dezaktywowane (poziom usera)</h3>';
		$d['requestedUserServices']->write('userServiceLastRequested');
	}

	public function servicesEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2><a href="'.$this->url(0).'/">Szukaj</a> | Usługi</h2>';
		echo '<h3>Zadania &bull; <a href="'.$this->url(0).'/services/list">Aktywne</a></h3>';
		
		$form = UFra::factory('UFlib_Form', 'serviceSelect', $d);
		echo $form->_start();
		echo $form->_fieldset();
		$tmp = array();
		$tmp['0'] = 'Wszystkie';
		foreach ($d['allServices'] as $srv) {
			$tmp[$srv['id']] = $srv['name'];
		}
		echo $form->serviceId('Wyświetl usługi: ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->_submit('Wyświetl');
		echo $form->_end();

		if ($this->_srv->get('msg')->get('serviceEdit/ok')) {
			echo $this->OK('Zmiany zostały zapisane');
		}

		echo $form->_start();
		if (isset($d['toActivate']))
		{
			echo '<h3>Do aktywacji:</h3>';
			echo $d['toActivate']->write('formToActivate');
		}
		echo $form->_end(true);
		
		echo $form->_start();
		if (isset($d['toDeactivate']))
		{
			echo '<h3>Do dezaktywacji:</h3>';
			echo $d['toDeactivate']->write('formToDeactivate');
		}
		echo $form->_end(true);
	}

	public function servicesList(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2><a href="'.$this->url(0).'/">Szukaj</a> | Usługi</h2>';
		echo '<h3><a href="'.$this->url(0).'/services">Zadania</a> &bull; Aktywne</h3>';
		
		$form = UFra::factory('UFlib_Form', 'serviceSelect', $d);
		echo $form->_start();
		echo $form->_fieldset();
		$tmp = array();
		$tmp['0'] = 'Wszystkie';
		foreach ($d['allServices'] as $srv) {
			$tmp[$srv['id']] = $srv['name'];
		}
		echo $form->serviceId('Wyświetl usługi: ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->_submit('Wyświetl');
		echo $form->_end();

		if ($this->_srv->get('msg')->get('serviceEdit/ok')) {
			echo $this->OK('Zmiany zostały zapisane');
		}

		echo $form->_start();
		if (isset($d['active']))
		{
			echo '<h3>Istniejące:</h3>';
			echo $d['active']->write('formToDeactivate');
		}
		echo $form->_end(true);
	}


	public function titleServices() {
		echo 'Panel Usług Użytkowników';
	}
	
	public function penaltyAddMailTitle(array $d) {
		if ($d['penalty']->typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Nałożono nowe ostrzeżenie w DS'.substr($d['user']->dormitoryAlias, 2);
		} else {
			echo 'Nałożono nową karę w DS'.substr($d['user']->dormitoryAlias, 2);
		}
	}
	
	public function penaltyAddMailBody(array $d) {
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Zostało nałożone nowe OSTRZEŻENIE';
		} else {
			echo 'Została nałożona nowa KARA';
		}
		echo ' w DS'.substr($d['user']->dormitoryAlias, 2)."\n";
		echo 'Trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt)."\n";
		if ($d['penalty']-> typeId != UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Kara nałożona na host(y): ';
			foreach ($d['computers'] as $computer) {
				if (is_array($computer)) {
					echo $computer['host'].' ';
				} else {
					echo $computer->host.' ';
				}
			}
		}
		echo "\n";
		echo 'Użytkownik: '.$d['user']->name.' "'.$d['user']->login.'" '.$d['user']->surname."\n";
		echo 'Admin: '.$d['admin']->name."\n";
		echo 'Szablon: '.$d['penalty']->templateTitle."\n";
		echo 'Min. długość (dni): '.(($d['penalty']->amnestyAfter - $d['penalty']->startAt) / 24 / 3600)."\n";
		echo 'Powód: '.$d['penalty']->reason."\n";
		echo 'Komentarz: '.$d['penalty']->comment."\n";
		echo 'Link: https://'.$d['host'].'/admin/penalties/'.$d['penalty']->id."\n";
	}

	public function penaltyEditMailTitle(array $d) {
		if ($d['penalty']->typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Zmodyfikowano ostrzeżenie w DS'.substr($d['user']->dormitoryAlias, 2);
		} else {
			echo 'Zmodyfikowano karę w DS'.substr($d['user']->dormitoryAlias, 2);
		}
	}
	
	public function penaltyEditMailBody(array $d) {
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Zmodyfikowano OSTRZEŻENIE';
		} else {
			echo 'Zmodyfikowano KARĘ';
		}
		echo ' w DS'.substr($d['user']->dormitoryAlias, 2)."\n";
		echo 'Trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['penalty']->endAt);
		echo ($d['penalty']->endAt != $d['oldPenalty']->endAt) ? ' (było: '.date(self::TIME_YYMMDD_HHMM, $d['oldPenalty']->endAt).')' : '';
		echo "\n";
		echo 'Użytkownik: '.$d['user']->name.' "'.$d['user']->login.'" '.$d['user']->surname."\n";
		echo 'Admin modyfikujący: '.$d['admin']->name."\n";
		echo 'Szablon: '.$d['newTpl'];
		echo ($d['penalty']->templateTitle != $d['newTpl']) ? ' (było: '.$d['oldPenalty']->templateTitle.')' : '';
		echo "\n";
		echo 'Min. długość (dni): '.(($d['penalty']->amnestyAfter - $d['penalty']->startAt) / 24 / 3600);
		echo ($d['penalty']->amnestyAfter != $d['oldPenalty']->amnestyAfter) ? ' (było: '.(($d['oldPenalty']->amnestyAfter - $d['oldPenalty']->startAt) / 24 / 3600).')' : '';
		echo "\n";
		echo 'Powód: '.$d['penalty']->reason;
		echo ($d['penalty']->reason != $d['oldPenalty']->reason) ? ' (było: '.$d['oldPenalty']->reason.')' : '';
		echo "\n";
		echo 'Komentarz: '.$d['penalty']->comment."\n";
		echo 'Link: https://'.$d['host'].'/admin/penalties/'.$d['penalty']->id."\n";
	}

	public function switchPortModifiedMailTitle(array $d) {
		echo 'Zmodyfikowano port '.$d['port']->ordinalNo.' na switchu '.UFtpl_SruAdmin_Switch::displaySwitchName($d['port']->dormitoryAlias, $d['port']->switchNo);
	}
	
	public function switchPortModifiedMailBody(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$host = $conf->sruUrl;

		echo 'Zmodyfikowano port '.$d['port']->ordinalNo.' na switchu '.UFtpl_SruAdmin_Switch::displaySwitchName($d['port']->dormitoryAlias, $d['port']->switchNo)."\n";
		echo 'Status portu: ';
		if ($d['enabled']) {
			echo 'włączony'."\n";
		} else {
			echo 'wyłączony'."\n";
		}
		echo 'Admin modyfikujący: '.$d['admin']->name."\n";
		echo 'Link: '.$host.'/admin/switches/'.$d['port']->switchSn.'/port/'.$d['port']->ordinalNo."\n";
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
			echo $d['user']->write('dataAdminChangedMailBodyEnglish', $d['history']);
		} else {
			echo $d['user']->write('dataAdminChangedMailBodyPolish', $d['history']);
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
			echo $d['host']->write('hostAdminChangedMailBodyEnglish', $d['action'], $d['history'], $d['admin']);
		} else {
			echo $d['host']->write('hostAdminChangedMailBodyPolish', $d['action'], $d['history'], $d['admin']);
		}
	}

	public function hostAliasesChangedMailTitle(array $d) {
		echo 'Zmodyfikowano aliasy hosta '.$d['host']->host;
	}
	
	public function hostAliasesChangedMailBody(array $d) {
		echo $d['host']->write('hostAliasesChangedMailBody', $d['deleted'], $d['added'], $d['admin']);
	}
}
