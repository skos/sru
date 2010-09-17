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
		echo '<h3>System Ewidencji Mieszkańców<br/>Osiedla Studenckiego PG</h3>';
		if ($this->_srv->get('msg')->get('adminLogin/errors')) {
			echo $this->ERR('Nieprawidłowy login lub hasło');
		}
		echo $d['admin']->write('formLogin');
		echo $form->_submit('Zaloguj');
		echo $form->_end();
		echo $form->_end(true);

		echo '<div style="position: relative; left: 420px; top: -130px;"><img src="'.UFURL_BASE.'/i/walet.png" alt="Walet"/></div>';
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
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function mainPageInfo() {
		echo '<div><br/>Wszlekie znalezione błędy prosimy zgłaszać na adres <a href="mailto:adnet@ds.pg.gda.pl">adnet@ds.pg.gda.pl</a>.</div>';
	}

	private function generateNewUserLink(array $searched) {
		$search = '';
		isset($searched['surname']) ? $search = '/surname:'.$searched['surname'] : '';
		if (isset($searched['registryNo'])) {
			if ($search == '') {
				$search = '/registryNo:'.$searched['registryNo'];
			} else {
				$search = $search.'/registryNo:'.$searched['registryNo'];
			}
		}
		return ' <a href="'.$this->url(0).'/users/:add'.$search.'">Dodaj nowego mieszkańca</a>.';
	}

	public function addUserLink(array $d) {
		echo $this->generateNewUserLink($d['searched']);
	}

	public function userSearchResults(array $d) {
		echo '<div class="userSearchResults">';
		echo $d['users']->write('searchResultsWalet');
		echo '</div>';
	}

	public function userSearchResultsNotFound(array $d) {
		echo $this->ERR('Nie znaleziono.');
	}

	public function user(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id;
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
?>
<script type="text/javascript">
window.open("<? echo $url; ?>/:print/<? echo $d['user']->login; ?>/<? echo $this->_srv->get('req')->get->password; ?>", "Wydruk potwierdzenia zameldowania",'width=800,height=600');
</script>
<?
			echo $this->OK('Konto zostało założone.<br/><a href="'.$url.'/:print/'.$d['user']->login.'/'.$this->_srv->get('req')->get->password.'" target="_blank">Wydrukuj potwierdzenie założenia konta</a>.');
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
		$url = $this->url(0).'/users/'.$d['user']->id;
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja</h2>';
		echo $form->_start($this->url());
		echo $form->_fieldset('Edycja użytkownika');
		if ($this->_srv->get('msg')->get('userEdit/ok')) {
			$msg = '';
			try {
				if ($this->_srv->get('req')->get->activated) {
?>
<script type="text/javascript">
window.open("<? echo $url; ?>/:print/<? echo $d['user']->login; ?>", "Wydruk potwierdzenia zameldowania",'width=800,height=600');
</script>
<?
					$msg = '<br/><a href="'.$url.'/:print/'.$d['user']->login.'" target="_blank">Wydrukuj potwierdzenie zameldowania</a>.';
				}
			} catch (UFex_Core_DataNotFound $e) {
			}
			echo $this->OK('Dane zostały zmienione.'.$msg);
		}
		echo $d['user']->write('formEditWalet', $d['dormitories']);
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userHistory(array $d) {
		echo '<div class="user">';
		echo '<h2>Historia profilu</h2>';
		echo '<ol class="history">';
		$d['history']->write('table', $d['user'], true);
		echo '</ol>';
		echo '</div>';
	}

	public function titleUserAdd() {
		echo 'Załóż konto';
	}

	public function userAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Załóż konto');
		echo $d['user']->write('formAdd', $d['dormitories'], $d['faculties'], $d['surname'], $d['registryNo']);
		echo $form->_submit('Załóż');
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserPrint() {
		echo 'Wydruk potwierdzenia zameldowania';
	}

	public function userPrint(array $d) {
		echo '<b>Witamy w Osiedlu Studenckim Politechniki Gdańskiej!</b>';
		echo '<p>Aby zalogować się na swoje konto w Systemie Rejestracji Użytkownika (http://sru.ds.pg.gda.pl) skorzystaj z następujących danych:<br/>
			<i>login:</i> '.$d['login'].'<br/>';
		if (is_null($d['password'])) {
			echo 'Użyj tego samego hasła, jakiego używał(a/e)ś poprzednio. Jeśli nie pamiętasz go, skorzystaj z przypomnienia hasła na SRU lub odwiedź administratora w godzinach dyżuru. Nie zapomnij wejściówki! Aby mieć Internet, po zalogowaniu się przywróć swoje komputery.';
		} else {
			echo '<i>hasło:</i> '.$d['password'].'<br/>
				Zaraz po zalogowaniu zostaniesz poproszon(a/y) o zmianę hasła.';
		}
	}

	public function userPrintError() {
		echo 'Błąd generowania wydruku';
	}


	/* Obsadzenie */

	public function titleInhabitants() {
		echo 'Obsadzenie pokoi';
	}

	public function inhabitants(array $d) {
		echo '<h2>Obsadzenie</h2>';
		$d['dormitories']->write('inhabitants', $d['rooms']);
	}

	public function titleDorm(array $d) {
		echo $d['dorm']->name.' - obsadzenie';
	}

	public function dorm(array $d) {
		echo '<h2><a href="'.$this->url(0).'/inhabitants">Obsadzenie</a></h2>';
		echo '<h3>'.$d['dorm']->name.'</h3>';
		$d['rooms']->write('dormInhabitants', $d['dorm'], $d['users']);
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
