<?
/**
 * szablon modulu Waleta
 */
class UFtpl_SruWalet
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

	public function menuWalet() {
		echo '<ul id="nav">';
		echo '<li><a href="'.UFURL_BASE.'/walet/">Wyszukiwanie</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/walet/inhabitants/">Obsadzenie</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/walet/stats/">Statystyki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/walet/admins/">Administratorzy</a></li>';
		echo '</ul>';
	}

	public function waletBar(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/', array('class'=>'adminBar'));
		echo $form->_fieldset();
		echo $d['admin']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt']);
		echo $form->_submit('Wyloguj', array('name'=>'adminLogout'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleMain() {
		echo 'System Rejestracji Użytkowników - Walet';
	}


	/* Mieszkańcy */

	public function titleUserSearch() {
		echo 'Znajdź użytkownika';
	}

	public function userSearch(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<div class="userSearch">';
		echo $form->_start($this->url(0).'/users/search');
		echo $form->_fieldset('Znajdź mieszkańca');
		echo $d['user']->write('formSearchWalet', $d['searched']);
		echo $form->_submit('Znajdź');
		echo ' <a href="'.$this->url(0).'/users/:add">Dodaj</a>';
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function userSearchResults(array $d) {
		echo '<div class="userSearchResults"><ul>';
		echo $d['users']->write('searchResultsWalet');
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
		$d['user']->write('detailsWalet');
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
		echo '<h2>Historia profilu</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['user']);
		echo '</ol>';
		echo '</div>';
	}


	/* Obsadzenie */

	public function titleInhabitants() {
		echo 'Obsadzenie pokoi';
	}

	public function inhabitants(array $d) {
		echo '<h2>Obsadzenie | <a href="'.$this->url(0).'/dormitories">Akademiki</a></h2>';
		$d['dormitories']->write('inhabitants');
	}

	public function titleDormitories() {
		echo 'Lista Domów Studenckich';
	}

	public function dormitories(array $d) {
		echo '<h2><a href="'.$this->url(0).'/inhabitants">Obsadzenie</a> | Akademiki</h2>';
		$d['dormitories']->write('listDorms');
	}

	public function titleDorm(array $d) {
		echo $d['dorm']->write('titleDetails');
	}

	public function dorm(array $d) {
		echo '<h2><a href="'.$this->url(0).'/inhabitants">Obsadzenie</a> | Akademiki</h2>';
		//$d['rooms']->write('dormInhabitants');
	}

	public function titleDormNotFound() {
		echo 'Nie znaleziono domu studenckiego';
	}

	public function dormNotFound(array $d) {
		echo $this->ERR('Nie znaleziono domu studenckiego');
	}


	/* Statystyki */

	public function titleStatsUsers() {
		echo 'Statystyki użytkowników';
	}
	
	public function statsUsers(array $d) {
		echo '<h2>Użytkownicy | <a href="'.$this->url(1).'/dormitories">Akademiki</a></h2>';
		$d['users']->write('stats');
	}
	
	public function statsUsersNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function titleStatsDormitories() {
		echo 'Statystyki akademików';
	}
	
	public function statsDormitories(array $d) {
		echo '<h2><a href="'.$this->url(1).'">Użytkownicy</a> | Akademiki</h2>';
		$d['users']->write('statsDorms');
	}						
	
	public function statsDormitoriesNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}


	/* Admini */

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
		echo '<h2>Administratorzy OS</h2>';

		$d['admins']->write('listAdmin');

		echo '</div>';
		
		if($acl->sruWalet('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
	}

	public function inactiveAdmins(array $d) {
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins inactive">';
		echo '<h2>Nieaktywni Administratorzy OS</h2>';

		$d['admins']->write('listAdmin');

		echo '</div>';
	}


	public function titleAdminNotFound() {
		echo 'Nie znaleziono administratora';
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
		echo ' <a href="'.$this->url(1).'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleAdminAdd(array $d) {
		echo 'Dodawanie nowego administratora';
	}

	public function adminEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo '<h2>Edycja administratora</h2>';
		echo $form->_start();
		
		echo $d['admin']->write('formEdit', $d['dormitories'], $d['dormList'], $d['advanced']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$this->url(1).'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleAdminEdit(array $d) {
		echo $d['admin']->write('titleDetails');
	}

	public function adminsNotFound() {
		echo '<h2>Administratorzy OS</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
		
		if($acl->sruWalet('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
	}

	public function inactiveAdminsNotFound() {
		echo '<h2>Nieaktywni Administratorzy OS</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
	}

	public function admin(array $d) {
		$url = $this->url(0).'/admins/'.$d['admin']->id;
		$acl = $this->_srv->get('acl');
		
		if ($this->_srv->get('msg')->get('adminEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		
		echo '<div class="admin">';
		$d['admin']->write('details', $d['dormList']);
		
		echo '<p class="nav">';
		if($acl->sruWalet('admin', 'edit', $d['admin']->id)) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a></p></div>';
	}

	public function adminUsersModified(array $d) {
		echo '<h3>Osoby ostatnio modyfikowane/dodane</h3>';
		$d['modifiedUsers']->write('userLastModified');
	}
}
