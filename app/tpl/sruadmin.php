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
		echo '<li><a href="'.UFURL_BASE.'/admin/">Szukaj</a></li>';	
		echo '<li><a href="'.UFURL_BASE.'/admin/computers/">Komputery</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/penalties/">Kary</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/dormitories/">Akademiki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/admins/">Administratorzy</a></li>';
		echo '</ul>';
	}

	public function adminBar(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/', array('class'=>'adminBar'));
		echo $form->_fieldset();
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
		$url = $this->url(0).'/computers/'.$d['computer']->id;
		echo '<div class="computer">';
		$d['computer']->write('details');
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
		echo $d['computer']->write('formEditAdmin', $d['dormitories'], $d['history']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
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
		echo '<div class="computerSearchResults"><ul>';
		echo $d['computers']->write('searchResults');
		echo '</ul></div>';
	}

	public function computerSearchResultsNotFound() {
		echo $this->ERR('Nie znaleziono');
	}

	public function titleUserSearch() {
		echo 'Znajdź użytkownika';
	}

	public function userSearch(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<div class="userSearch">';
		echo $form->_start($this->url(0).'/users/search');
		echo $form->_fieldset('Znajdź użytkownika');
		echo $d['user']->write('formSearch', $d['searched']);
		echo $form->_submit('Znajdź');
		echo ' <a href="'.$this->url(0).'/users/:add">Dodaj</a>';
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
		
		echo '<p class="nav"><a href="'.$url.':add">Dodaj komputer</a></p>';
		
	}
	public function userInactiveComputers(array $d) {
		$url = $this->url(2).'/computers/';
		
		echo '<h2>Wyrejestrowane komputery</h2><ul>';
			
		echo $d['computers']->write('listAdmin');
		echo '</ul>';
		
	}	

	public function userComputersNotFound(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id.'/computers/';
		echo '<h2>Komputery użytkownika</h2>';
		echo '<p class="nav"><a href="'.$url.':add">Dodaj komputer</a></p>';
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
		echo $d['user']->write('formEditAdmin', $d['dormitories'], $d['faculties']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userHistory(array $d) {
		echo '<div class="user">';
		echo '<h2>Historia zmian</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['user']);
		echo '</ol>';
		echo '</div>';
	}
	public function titleAdmins() {
		echo 'Administratorzy';
	}	
	public function admins(array $d)
	{
		$url = $this->url(0).'/admins/';
		$acl = $this->_srv->get('acl');
		
		if ($this->_srv->get('msg')->get('adminAdd/ok')) {
			echo $this->OK('Konto zostało założone');
		}		
		
		echo '<div class="admins">';
		echo '<h2>Administratorzy</h2>';

		$d['admins']->write('listAdmin');

		echo '</div>';
		
		
		if($acl->sruAdmin('admin', 'add'))
		{
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
				
	}
	public function inactiveAdmins(array $d)
	{
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins inactive">';
		echo '<h2>Nieaktywni Administratorzy</h2>';

		$d['admins']->write('listAdmin');

		echo '</div>';			
	}	
	public function bots(array $d)
	{
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins">';
		echo '<h2>Boty</h2>';

		$d['admins']->write('listBots');

		echo '</div>';			
	}	
	public function titleAdminNotFound() {
		echo 'Nie znaleziono administratora';
	}
			
	public function adminsNotFound() {
		echo '<h2>Administratorzy</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
		
		if($acl->sruAdmin('admin', 'add'))
		{
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
		
		if($acl->sruAdmin('admin', 'edit', $d['admin']->id))
		{
			echo '<p class="nav"><a href="'.$url.'/:edit">Edycja</a></p>';
		}
		echo '</div>';
	}

	public function penaltyTemplateChoose(array $d) {
		echo '<h2>Typ kary dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</h2>';
		echo '<ul class="penaltyTemplates">';
		$d['templates']->write('choose');
		echo '</ul>';
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
		

		echo $d['admin']->write('formEdit', $d['dormitories'], $d['advanced']);
		echo $form->_submit('Zapisz');
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
		

		
		if($d['rooms'])
		{
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
		
		if ($this->_srv->get('msg')->get('penaltyAdd/ok')) {
			echo $this->OK('Kara została założona');
		}		
		
		echo '<h2>Aktywne kary &bull; <a href="'.$url.'actions">Ostatnie akcje</a></h2>';

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
		$acl = $this->_srv->get('acl');
		
		echo '<h2>Kara dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</h2>';
		
		echo '<div class="penalty">';
	
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset();
		echo $d['penalty']->write('formAdd', $d['computers'], $d['templates'], $d['computerId']);
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);		

		echo '</div>';
					
	}

	public function titlePenalty(array $d) {
		echo 'Kara / Ostrzeżenie';
	}

	public function penalty(array $d) {
		echo '<div class="penalty">';	
		echo '<h2>';

		if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $d['penalty']->typeId) {
			echo 'Ostrzeżenie';
		} else {
			echo 'Kara';
		}
		echo '</h2>';
		if ($this->_srv->get('msg')->get('penaltyEdit/ok')) {
			echo $this->OK('Zmiany zostały wprowadzone');
		} elseif ($this->_srv->get('msg')->get('penaltyEdit/errors/endAt')) {
			echo $this->ERR('Nieprawidłowa data');
		}
		
		$d['penalty']->write('details', $d['computers']);
		
		echo '</div>';
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
		
		echo '<h2>Lista kar dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</h2><ul>';

		$d['penalties']->write('listUserPenalty');

		echo '</ul>';
	}	
	
	public function titleComputerPenalties(array $d) {
		echo 'Lista kar i ostrzeżeń dla hosta '.$d['computer']->host;
	}	

	public function computerPenalties(array $d)
	{
		$acl = $this->_srv->get('acl');		
		
		echo '<h2>Lista kar dla hosta '.$d['computer']->host.'</h2><ul>';

		$d['penalties']->write('listComputerPenalty');

		echo '</ul>';
	}

	public function penaltyActions(array $d)
	{
		$url = $this->url(0).'/penalties/';
		$acl = $this->_srv->get('acl');		
		
		echo '<h2><a href="'.$url.'">Aktywne kary</a> &bull; Ostatnie akcje</h2>';
		
		echo '<h3>Modyfikacje</h3><ul>';
		$d['modified']->write('penaltyLastModified');
		echo '</ul><h3>Nowe</h3><ul>';
		$d['added']->write('penaltyLastAdded');
		echo '</ul>';

	}

	public function titlePenaltyActions() {
		echo 'Ostatnie akcje na karach i ostrzeżeniach';
	}
	
	public function titleIps() {
		echo 'Zestawienie numerów IP';
	}
	
	public function ips(array $d) {
		if (!is_null($d['dorm'])) {
			echo '<h2><a href="'.$this->url(0).'/dormitories/'.$d['dorm']->alias.'">'.$d['dorm']->name.'</a></h2>';
		} else {
			echo '<h2>Zestawienie numerów IP</h2>';
		}
		echo '<div class="ips">';
		
		$d['ips']->write('ips');
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

	public function adminPenaltiesAdded(array $d) {
		echo '<h3>Kary i ostrzeżenia ostatnio dodane</h3>';
		$d['added']->write('penaltyLastAdded', false);
	}

	public function adminPenaltiesModified(array $d) {
		echo '<h3>Kary i ostrzeżenia ostatnio modyfikowane</h3>';
		$d['modified']->write('penaltyLastModified', false);
	}
}
