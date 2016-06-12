<?
/**
 * szablon modulu administracji sru
 */
class UFtpl_SruAdmin
extends UFtpl_Common {

	public function titleLogin() {
		echo 'Zaloguj się';
	}
	
	public function leftColumnStart() {
		echo '<div class="leftColumn">';
	}
	
	public function rightColumnStart() {
		echo '<div class="rightColumn">';
	}
	
	public function columnEnd() {
		echo '</div>';
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
		
		UFlib_Script::focusIfLoginNotEmpty('adminLogin_password', 'adminLogin_login');
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
		echo '<li><a href="'.UFURL_BASE.'/admin/">Wyszukiwanie</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/admins/tasks">Zadania</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/penalties/">Kary</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/dormitories/">Akademiki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/stats/">Statystyki</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/admins/">Administratorzy</a></li>';
		echo '</ul>';
	}

	public function adminBar(array $d) {
		echo $d['admin']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt'], $d['lastInvLoginIp'], $d['lastInvLoginAt']);
	}
	
	public function titleComputer(array $d) {
		echo $d['computer']->write('titleDetails');
	}

	public function titleComputerNotFound() {
		echo 'Nie znaleziono komputera';
	}

	public function computerNotFound() {
		echo $this->ERR('Nie znaleziono komputera');
	}

	public function computer(array $d) {
		
		if ($this->_srv->get('msg')->get('computerDel/ok')) {
			echo $this->OK('Komputer wyrejestrowano');
		}
		if ($this->_srv->get('msg')->get('computerAliasesEdit/ok')) {
			echo $this->OK('Zmodyfikowano aliasy komputera');
		}
		if ($this->_srv->get('msg')->get('computerFwExceptionsEdit/ok')) {
			echo $this->OK('Zmodyfikowano wyjątki FW komputera');
		}
		if ($this->_srv->get('msg')->get('inventoryCardAdd/ok')) {
			echo $this->OK('Karta wyposażenia została dodana');
		}
		if ($this->_srv->get('msg')->get('inventoryCardEdit/ok')) {
			echo $this->OK('Karta wyposażenia została zmieniona');
		}
		$url = $this->url(0).'/computers/'.$d['computer']->id;
		echo '<div class="computer">';
		$d['computer']->write('details', $d['switchPort'], $d['aliases'], $d['virtuals'], $d['interfaces'], $d['masterHost'], $d['fwExceptions']);
		echo '</div>';
	}

	public function titleComputers() {
		echo 'Wszystkie komputery';
	}
	
	public function computersFilter() {
		echo '<h2>Serwery, administracja, organizacje</h2>';
		echo '<label for="filter">Filtruj:</label> <input type="text" name="filter" value="" id="filter" />';
		
		?>
<script type="text/javascript">
$(document).ready(function() {
	//default each row to visible
	$('tbody tr').addClass('visible');
	
	$('#filter').keyup(function(event) {
		//if esc is pressed or nothing is entered
		if (event.keyCode == 27 || $(this).val() == '') {
			//if esc is pressed we want to clear the value of search box
			$(this).val('');
			
			//we want each row to be visible because if nothing
			//is entered then all rows are matched.
			$('tbody tr').removeClass('visible').show().addClass('visible');
		} else { //if there is text, lets filter
			filter('tbody tr', $(this).val());
		}
	});
});

//filter results based on query
function filter(selector, query) {
	query = $.trim(query); //trim white space
	query = query.replace(/ /gi, '|'); //add OR for regex
  
	$(selector).each(function() {
		($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');
	});
}
</script>
<?
	}

	public function serverComputers(array $d) {
		echo '<h3>Serwery</h3>';

		echo '<div class="computers">';
		$d['computers']->write('listAdmin', 'servers');
		echo '</div>';
	}
	public function serverInterfaces(array $d) {
		echo '<h3>Interfejsy</h3>';

		echo '<div class="computers">';
		$d['interfaces']->write('listInterfaces');
		echo '</div>';
	}
	public function serverAliases(array $d) {
		echo '<h3>Aliasy serwerów</h3>';
		
		echo '<div class="computers">';
		$d['aliases']->write('listAliases');
		echo '</div>';
	}
	public function administrationComputers(array $d) {
		echo '<h3>Komputery administracji</h3>';

		echo '<div class="computers">';
		$d['computers']->write('listAdmin', 'administration');
		echo '</div>';
	}
	public function organizationsComputers(array $d) {
		echo '<h3>Komputery organizacji</h3>';

		echo '<div class="computers">';
		$d['computers']->write('listAdmin', 'org');
		echo '</div>';
	}
	public function organizationsComputersNotFound() {
		echo '<h3>Komputery organizacji</h3>';
		echo $this->ERR('Nie znaleziono komputerów');
	}
	public function administrationComputersNotFound() {
		echo '<h3>Komputery administracji</h3>';
		echo $this->ERR('Nie znaleziono komputerów');
	}	
	public function serverComputersNotFound() {
		echo '<h3>Serwery</h3>';
		echo $this->ERR('Nie znaleziono komputerów');
	}
	public function serverAliasesNotFound() {
		echo '<h3>Aliasy serwerów</h3>';
		echo $this->ERR('Nie znaleziono komputerów');
	}

	public function computersNotFound() {
		echo $this->ERR('Nie znaleziono komputerów');
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
		echo $d['computer']->write('formEditAdmin', $d['dormitories'], $d['user'], $d['history'], $d['servers'], $d['skosAdmins'], $d['waletAdmins'], $d['virtuals'], $d['deviceModels'], $d['interfaces'], $d['devicesInterfacable']);
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
	
	public function titleComputerFwExceptionsEdit(array $d) {
		echo $d['computer']->write('titleFwExceptionsEdit');
	}

	public function computerFwExceptionsEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>Edycja wyjątków FW</h2>';
		echo $form->_start($this->url());
		echo $d['computer']->write('formFwExceptionsEdit', $d['fwExceptions']);
		echo $form->_submit('Zapisz');
		echo $form->_end(true);
	}

	public function computerFwExceptionsNotFound() {
		echo $this->ERR('Błąd wyświetlania wyjątków FW');
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
		echo '<h2>Znalezione komputery ('.count($d['computers']).'):</h2>';
		echo '<div class="computerSearchResults">';
		echo $d['computers']->write('searchResults');
		echo '</div>';
	}

	public function computerSearchByAliasResults(array $d) {
		echo '<h2>Znalezione aliasy ('.count($d['aliases']).'):</h2>';
		echo '<div class="computerSearchResults">';
		echo $d['aliases']->write('listAliases');
		echo '</div>';
	}

	public function computerSearchHistoryResults(array $d) {
		echo '<h2>Znalezione komputery używające wcześniej szukanego IP ('.count($d['computers']).'):</h2>';
		echo '<div class="computerSearchResults">';
		echo $d['computers']->write('searchResults');
		echo '</div>';
	}

	public function computerSearchResultsUnregistered(array $d) {
		echo '<div class="computer">';
		echo $d['computers']->write('searchResultsUnregistered', $d['switchPort'], $d['searchedMac']);
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
		$acl = $this->_srv->get('acl');

		echo '<h2>Szukaj</h2>';

		echo '<div class="userSearch">';
		echo $form->_start($this->url(0).'/users/search');
		echo $form->_fieldset('Znajdź użytkownika');
		echo $d['user']->write('formSearch', $d['searched']);
		echo $form->_submit('Znajdź');
		if ($acl->sruAdmin('user', 'add')) {
			echo ' <a href="'.$this->url(0).'/users/:add">Dodaj</a>';
		}
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function userSearchResults(array $d) {
		echo '<h2>Znalezieni użytkownicy ('.count($d['users']).'):</h2>';
		echo '<div class="userSearchResults">';
		echo $d['users']->write('searchResults');
		echo '</div>';
	}

	public function userSearchResultsNotFound() {
		echo '<h2>Znalezieni użytkownicy:</h2>';
		echo $this->ERR('Nie znaleziono');
	}
	
	public function titleInventoryCardSearch() {
		echo 'Znajdź urządzenie';
	}

	public function inventoryCardSearch(array $d) {
		$form = UFra::factory('UFlib_Form');	
		echo '<div class="inventoryCardSearch">';
		echo $form->_start($this->url(0).'/inventory/search');
		echo $form->_fieldset('Znajdź urządzenie');
		echo $d['inventoryCard']->write('formSearch', $d['searched']);
		echo $form->_submit('Znajdź');
		
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
	}

	public function inventoryCardSearchResults(array $d) {
		echo '<h2>Znalezione urządzenia ('.count($d['inventoryCards']).'):</h2>';
		echo '<div class="inventoryCardSearchResults">';
		echo $d['inventoryCards']->write('searchResults');
		echo '</div>';
	}

	public function inventoryCardSearchResultsNotFound() {
		echo '<h2>Znalezione urządzenia:</h2>';
		echo $this->ERR('Nie znaleziono');
	}
	
	public function inventoryCardSearchHistoryResults(array $d) {
		echo '<h2>Znalezione urządzenia z szukanym S/N w historii:</h2>';
		echo '<div class="inventoryCardSearchResults">';
		echo $d['inventoryCards']->write('searchResults');
		echo '</div>';
	}
	
	public function inventoryCardSearchHistoryResultsNotFound(array $d) {
	}
	
	public function tasksSummary(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo $form->_fieldset('Podsumowanie zadań');
		echo ' <div id="tasksSummary"><img class="loadingImg" src="'.UFURL_BASE.'/i/img/ladowanie.gif" alt="Trwa ładowanie podsumowania..." /></div>';
		echo $form->_end();
?>
<script>
function getSummary() {
	$("#tasksSummary").load('<?=UFURL_BASE?>/admin/apis/gettaskssummary');
}
getSummary();
setInterval(getSummary, 10*1000);
</script>
<?
	}
	
	public function adminLists(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo $form->_fieldset('Zestawienia');
		echo '<ul>';
		echo '<li><a href="'.UFURL_BASE.'/admin/computers/">Serwery, administracja, organizacje</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/ips/">IP i VLANy</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/switches/">Switche</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/devices/">Urządzenia</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/inventory/">Inwentaryzacja</a></li>';
		echo '<li><a href="'.UFURL_BASE.'/admin/fwexceptions/">Wyjątki w firewallu</a></li>';
		echo '</ul>';
		echo $form->_end();
	}
	
	public function titleTasks() {
		echo 'Lista zadań';
	}

	public function toDoList(array $d) {
		echo '<h2>Lista zadań</h2>';
		echo $d['admin']->write('toDoList', $d['computers'], $d['devices']);
	}

	public function user(array $d) {
		if ($this->_srv->get('msg')->get('userAdd/ok')) {
			echo $this->OK('Konto zostało założone.');
		}		
		echo '<div class="user">';
		$d['user']->write('details', $d['functions']);
		echo '</div>';
	}

	public function userNotFound() {
		echo $this->ERR('Nie znaleziono użytkownika');
	}

	public function titleUser(array $d) {
		echo $d['user']->write('titleDetails');
	}

	public function titleUserNotFound() {
		echo 'Nie znaleziono użytkownika';
	}

	public function userComputers(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id.'/computers/';
		
		echo '<h2>Komputery użytkownika</h2>';
		
		if ($this->_srv->get('msg')->get('computerAdd/ok')) {
			echo $this->OK('Komputer został dodany');
		}
		echo $d['computers']->write('listAdmin', 'active');
		$acl = $this->_srv->get('acl');
		if($acl->sruAdmin('computer', 'addForUser', $d['user']->id)) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj komputer</a></p>';
		}
	}
	public function userInactiveComputers(array $d) {		
		echo '<h2>Wyrejestrowane komputery</h2>';
			
		echo $d['computers']->write('listAdmin', 'unregistered');
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

	public function titleUserAdd() {
		echo 'Załóż konto';
	}

	public function userAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Załóż konto');
		echo $d['user']->write('formAddAdmin', $d['dormitories']);
		echo $form->_submit('Załóż');
		echo $form->_end();
		echo $form->_end(true);
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
		echo $d['user']->write('formEditAdmin', $d['faculties'], $d['dormitories']);
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
		echo '<h2>Administratorzy ('.count($d['admins']).') | <a href="http://dyzury.ds.pg.gda.pl">Dyżury</a></h2>';
		$d['admins']->write('listAdmins', 'active');
		echo '</div>';
		
		if($acl->sruAdmin('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
		echo '<h2>Pokaż aktywnych na dany dzień</h2>';
		$this->activeOnDateAdminsForm(null);
	}

	public function adminsActive(array $d) {
		$url = $this->url(0).'/admins/';
		
		echo '<div class="admins">';
		$this->activeOnDateAdminsForm($d['activeOnDate']);
		$d['admins']->write('activeOnDateAdmins', $d['activeOnDate']);
		echo '</div>';

		echo '<p class="nav"><a href="'.$url.'">Powrót</a></p>';
	}

	public function adminsActiveDateError(array $d) {
		$url = $this->url(0).'/admins/';

		echo '<div class="admins">';
		echo $this->ERR('Błędna data');
		$this->activeOnDateAdminsForm(null);
		echo '</div>';

		echo '<p class="nav"><a href="'.$url.'">Powrót</a></p>';
	}

	public function adminsActiveNotFound(array $d) {
		$url = $this->url(0).'/admins/';

		echo '<div class="admins">';
		echo $this->ERR('Nie znaleziono administratorów');
		$this->activeOnDateAdminsForm(null);
		echo '</div>';

		echo '<p class="nav"><a href="'.$url.'">Powrót</a></p>';
	}

	private function activeOnDateAdminsForm($date) {
		$form = UFra::factory('UFlib_Form', 'activeOnForm');
		echo $form->_start($this->url(0).'/admins/active');
		echo $form->_fieldset();
		echo $form->activeOn('Data (YYYY-MM-DD)', array('value'=>$date));
		echo $form->_submit('Zobacz', array('after'=>UFlib_Helper::displayHint("Pokazuje adminów aktywnych w danym dniu oraz adminów, którym nie udało się ustalić statusu na dany dzień.")));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function inactiveAdmins(array $d) {	
		echo '<div class="admins inactive">';
		echo '<h2>Nieaktywni Administratorzy ('.count($d['admins']).')</h2>';

		$d['admins']->write('listAdmins', 'inactive');

		echo '</div>';
	}

	public function bots(array $d) {
		echo '<div class="admins">';
		echo '<h2>Boty ('.count($d['admins']).')</h2>';

		$d['admins']->write('listAdmins', 'bots', false, true);

		echo '</div>';
	}

	public function waletAdmins(array $d) {		
		echo '<div class="admins">';
		echo '<h2>Pracownicy Osiedla ('.count($d['admins']).')</h2>';

		$d['admins']->write('listAdmins', $d['dormitories']);

		echo '</div>';
	}

	public function titleAdminNotFound() {
		echo 'Nie znaleziono administratora';
	}

	public function adminsNotFound() {
		$acl = $this->_srv->get('acl');
		$url = $this->url(0).'/admins/';
		
		echo '<h2>Administratorzy</h2>';
		echo $this->ERR('Nie znaleziono administratorów');
		
		if($acl->sruAdmin('admin', 'add')) {
			echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego administratora</a></p>';
		}
		
	}
	
	public function waletAdminsNotFound() {
		echo '<h2>Pracownicy Osiedla</h2>';
		echo $this->ERR('Nie znaleziono pracowników Osiedla');		
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
		$session = $this->_srv->get('session');
		$sruConf = UFra::shared('UFconf_Sru');
		
		if ($this->_srv->get('msg')->get('adminEdit/ok')) {
			echo $this->OK('Dane zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('adminOwnPswEdit/ok')) {
			echo $this->OK('Hasło zostało zmienione');
		}
		$timeToInvalidatePassword = $d['admin']->lastPswChange + $sruConf->passwordValidTime - time();
		if(($d['admin']->id == $session->authAdmin || ($session->is('typeId') && ($session->typeId == UFacl_SruAdmin_Admin::CENTRAL
			|| $session->typeId == UFacl_SruAdmin_Admin::CAMPUS))) && $d['admin']->active == true 
			&& ($timeToInvalidatePassword < $sruConf->passwordOutdatedWarning)) {
			if ($timeToInvalidatePassword <= 0) {
				echo $this->ERR('Hasło straciło ważność');
			} else {
				echo $this->ERR('Hasło niedługo (za '.UFlib_Helper::secondsToTime($timeToInvalidatePassword).') straci ważność, należy je zmienić!');
			}
		}
		if(($d['admin']->id == $session->authAdmin || ($session->is('typeId') && ($session->typeId == UFacl_SruAdmin_Admin::CENTRAL 
			|| $session->typeId == UFacl_SruAdmin_Admin::CAMPUS)))
			&& $d['admin']->active == true && $d['admin']->activeTo - time() <= $sruConf->adminDeactivateAfter && $d['admin']->activeTo - time() >= 0){
			echo $this->ERR('Konto niedługo ulegnie dezaktywacji, należy przedłużyć jego ważność w CUI!');
		}
		
		echo '<div class="admin">';
		$d['admin']->write('details');
		
		echo '<p class="nav">';
		echo '<a href="'.$url.'/history">Historia profilu</a> &bull; ';
		if($acl->sruAdmin('admin', 'edit', $d['admin']->id)) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a></p></div>';
	}

	public function adminHistory(array $d) {
		$d['history']->write('history', $d['admin']);
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
		if($acl->sruAdmin('admin', 'changeAdminDorms', $d['admin']->id)) {
			echo '<a href="'.$url.'/:edit">Edycja</a> &bull; ';
		}
		echo '<a href="'.$this->url(0).'/admins/">Powrót</a>';
	}

	public function adminDormsNotFound() {
		echo '<h3>Domy studenckie</h3>';
		echo $this->ERR('Brak przypisanych DSów');
	}

	public function adminHosts(array $d) {
		echo '<div class="computers">';
		echo '<h3>Komputery pod opieką</h3>';
		$d['hosts']->write('listAdmin');
		echo '</div>';
	}

	public function adminHostsNotFound() {
		echo '<h3>Komputery pod opieką</h3>';
		echo $this->ERR('Brak komputerów pod opieką');
	}

	public function penaltyTemplateChoose(array $d) {
		$url = $this->url(0).'/users/'.$d['user']->id;

		echo '<h2>Typ kary dla <a href="'.$url.'">'.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</a></h2>';
		echo '<ul class="penaltyTemplates">';
		$d['templates']->write('choose');
		echo '</ul>';
	}
	
	public function penaltyTemplateChooseNotFound(array $d) {
		echo $this->ERR('Błąd pobierania szablonów kar - nie można nałożyć kary.');
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
	
	public function titlePenaltyTemplateEditNotFound(array $d) {
		echo 'Błąd edycji szablonu kary';
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
	
	public function penaltyTemplateEditNotFound() {
		echo $this->ERR('Nie znaleziono szablonu kary');
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
		echo $form->_end(true);
	}
	public function titleOwnPswEdit(array $d){
	    echo 'Konieczna zmiana hasła';
	}
	public function ownPswEdit(array $d){
	    $form = UFra::factory('UFlib_Form');
	    echo '<h2>Konieczna zmiana hasła</h2>';
	    echo $form->_start();
	    echo $d['admin']->write('ownPswEdit');
	    echo $form->_end(true);
	}
	public function titleDormitories() {
		echo 'Akademiki';
	}	
	public function dorms(array $d)	{		
		echo '<div class="dormitories">';
		echo '<h2>Akademiki</h2>';
		$d['dorms']->write('functionsPanel', $d['functions'], false, false);
		$d['dorms']->write('listDorms');
		echo '</div>';
	}
	public function titleDorm(array $d) {
		echo $d['dorm']->write('titleDetails');
	}
	public function dorm(array $d) {
		echo '<div class="dorm">';
		$d['dorm']->write('details', true, $d['leftRight'][0], $d['leftRight'][2]);
		if($d['rooms']) {
			$d['dorm']->write('functionsPanel', $d['functions'], true, false);
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
		$urlIp = $this->url(0).'/ips/ds/';
		$urlSw = $this->url(0).'/switches/dorm/';
		$urlDev = $this->url(0).'/devices/dorm/';		
		if (!is_null($d['dorm'])) {
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlSw.$d['leftRight'][0]['alias'].'" ><</a> ';
			}
			echo $d['dorm']->name;
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlSw.$d['leftRight'][2]['alias'].'" >></a>';
			}

			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; '
				. '<a href="'.$urlIp.$d['dorm']->alias.'">komputery</a> &bull; '
				. 'liczba switchy: '.count($d['switches']).' &bull; '
				. '<a href="'.$urlDev.$d['dorm']->alias.'">urządzenia</a>)</small></h2>';
		} else {
			echo '<h2>Switche</h2>';
		}

		if ($this->_srv->get('msg')->get('switchAdd/ok')) {
			echo $this->OK('Switch został dodany');
		}

		$d['switches']->write('listSwitches', $d['dorm']);
		echo '</div>';
	}
	public function switchesNotFound($d) {
		if (!is_null($d)) {
			$url = $this->url(0).'/dormitories/';
			$urlAdd = $this->url(0).'/switches/';
			$urlIp = $this->url(0).'/ips/ds/';
			$urlSw = $this->url(0).'/switches/dorm/';
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlSw.$d['leftRight'][0]['alias'].'" ><</a> ';
			}
			echo $d['dorm']->name;
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlSw.$d['leftRight'][2]['alias'].'" >></a>';
			}
			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; <a href="'.$urlIp.$d['dorm']->alias.'">komputery</a> &bull; liczba switchy: 0)</small></h2>';
		}
		
		echo $this->ERR('Nie znaleziono switchy<br/><a href="'.$urlAdd.':add">Dodaj nowego switcha</a>');
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
		if ($this->_srv->get('msg')->get('inventoryCardEdit/ok')) {
			echo $this->OK('Karta wyposażenia została zmieniona');
		}
		$d['switch']->write('headerDetails', $d['leftRight'][0], $d['leftRight'][2]);
		$d['switch']->write('details', $d['info'], $d['lockouts']);
	}
	
	public function switchHistory(array $d) {
		$d['history']->write('history', $d['switch']);
	}

	public function switchData(array $d) {
		echo json_encode($d['info']);
	}

	public function switchTech(array $d) {
		$d['switch']->write('techDetails', $d['info'], $d['gbics']);
	}

	public function switchPorts(array $d) {
		$d['ports']->write('listPorts', $d['switch'], $d['portStatuses'], $d['trunks'], $d['flags'], $d['port']);
	}

	public function roomSwitchPorts(array $d) {
		echo '<h3>Przypisane porty</h3>';
		$d['ports']->write('listRoomPorts', $d['room'], $d['portStatuses'], $d['portFlags']);
	}

	public function switchPortDetails(array $d) {
		if ($this->_srv->get('msg')->get('switchPortEdit/ok')) {
			echo $this->OK('Dane portu switcha zostały zmienione');
		}
		$d['port']->write('details', $d['switch'], $d['alias'], $d['speed'], $d['vlan'], $d['flag'], $d['learnMode'], $d['addrLimit'], $d['alarmState'], $d['loopProtect'], $d['trunk']);
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
		echo '<h3>Przypisane porty</h3>';
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
		echo '<h2>Edycja switcha <a href="'.$this->url(0).'/switches/'.$d['switch']->serialNo.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d['switch']->dormitoryAlias, $d['switch']->hierarchyNo, $d['switch']->lab).'</a></h2>'; 
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

		echo '<h2>Edycja portów switcha <a href="'.$this->url(0).'/switches/'.$d['switch']->serialNo.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d['switch']->dormitoryAlias, $d['switch']->hierarchyNo, $d['switch']->lab).'</a></h2>';
		echo $form->_start();
		echo $d['ports']->write('formEdit', $d['switch'], $d['enabledSwitches'], $d['portAliases']);
		echo $form->_end();
		echo $form->_end(true);
	}

	public function switchPortEdit(array $d) {
		$url = $this->url(0);
		$form = UFra::factory('UFlib_Form');
		echo $form->_start();
		echo $d['port']->write('formEditOne', $d['switch'], $d['enabledSwitches'], $d['status'], $d['penalties']);
		echo $form->_submit('Zapisz', array('after'=>UFlib_Helper::displayHint("Zapisanie portu spowoduje opuszczenie flagi wtargnięcia (o ile jest podniesiona).")));
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
	
	public function titleDevices() {
		echo 'Urządzenia';
	}	
	public function devices(array $d) {
		$url = $this->url(0).'/dormitories/';
		$urlIp = $this->url(0).'/ips/ds/';
		$urlSw = $this->url(0).'/switches/dorm/';
		$urlDev = $this->url(0).'/devices/dorm/';
		if (!is_null($d['dorm'])) {
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlDev.$d['leftRight'][0]['alias'].'" ><</a> ';
			}
			echo $d['dorm']->name;
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlDev.$d['leftRight'][2]['alias'].'" >></a>';
			}

			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; '
				. '<a href="'.$urlIp.$d['dorm']->alias.'">komputery</a> &bull; '
				. '<a href="'.$urlSw.$d['dorm']->alias.'">switche</a> &bull; '
				. 'liczba urządzeń: '.count($d['devices']).')</small></h2>';
		} else {
			echo '<h2>Urządzenia</h2>';
		}

		if ($this->_srv->get('msg')->get('deviceAdd/ok')) {
			echo $this->OK('Urządzenie zostało dodane');
		}

		$d['devices']->write('listDevices', $d['dorm']);
		echo '</div>';
	}
	public function devicesNotFound($d) {
		if (!is_null($d)) {
			$url = $this->url(0).'/dormitories/';
			$urlAdd = $this->url(0).'/devices/';
			$urlIp = $this->url(0).'/ips/ds/';
			$urlSw = $this->url(0).'/switches/dorm/';
			$urlDev = $this->url(0).'/devices/dorm/';
			if (count($d) > 0) {
				echo '<h2>';
				if($d['leftRight'][0] != null){
					echo '<a href="'.$urlDev.$d['leftRight'][0]['alias'].'" ><</a> ';
				}
				echo $d['dorm']->name;
				if($d['leftRight'][2] != null){
					echo ' <a href="'.$urlDev.$d['leftRight'][2]['alias'].'" >></a>';
				}
				echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; '
					. '<a href="'.$urlIp.$d['dorm']->alias.'">komputery</a> &bull; '
					. '<a href="'.$urlSw.$d['dorm']->alias.'">switche</a> &bull; '
					. 'liczba urządzeń: 0)</small></h2>';
			}
		}
		
		echo $this->ERR('Nie znaleziono urządzeń<br/><a href="'.$urlAdd.':add">Dodaj nowe urządzenie</a>');
	}

	public function titleDevice(array $d) {
		echo $d['device']->write('titleDetails');
	}

	public function deviceDetails(array $d) {
		if ($this->_srv->get('msg')->get('deviceEdit/ok')) {
			echo $this->OK('Dane urządzenia zostały zmienione');
		}
		if ($this->_srv->get('msg')->get('inventoryCardAdd/ok')) {
			echo $this->OK('Karta wyposażenia została dodana');
		}
		if ($this->_srv->get('msg')->get('inventoryCardEdit/ok')) {
			echo $this->OK('Karta wyposażenia została zmieniona');
		}
		$d['device']->write('headerDetails', $d['leftRight'][0], $d['leftRight'][2]);
		$d['device']->write('details');
	}
	
	public function deviceHistory(array $d) {
		$d['history']->write('history', $d['device']);
	}
	
	public function deviceNotFound() {
		echo $this->ERR('Nie znaleziono urządzenia');
	}

	public function titleDeviceNotFound() {
		echo 'Nie znaleziono urządzenia';
	}
	
	public function deviceAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);

		echo '<h2>Dodawanie nowego urządzenia</h2>';
		echo $form->_start();
		echo $d['device']->write('formAdd', $d['dormitories'], $d['devModels']);
		echo $form->_submit('Dodaj');
		echo ' <a href="'.$url.'/devices/">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleDeviceAdd(array $d) {
		echo 'Dodawanie nowego urządzenia';
	}

	public function deviceEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);
		echo '<h2>Edycja urządzenia <a href="'.$this->url(0).'/devices/'.$d['device']->id.'">'.$d['device']->deviceModelName.'</a></h2>'; 
		echo $form->_start();
		echo $d['device']->write('formEdit', $d['dormitories'], $d['devModels']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$url.'/devices/'.$d['device']->id.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleDeviceEdit(array $d) {
		echo $d['device']->write('titleEditDetails');
	}

	public function inventoryCard(array $d) {
		echo '<h3>Karta wyposażenia</h3>';
		$d['inventoryCard']->write('details', $d['device']);
	}
	
	public function inventoryCardNotFound(array $d) {
		$acl = $this->_srv->get('acl');
		
		if($acl->sruAdmin('computer', 'inventoryCardAdd') || $acl->sruAdmin('device', 'inventoryCardAdd')) {
			echo '<h3>Karta wyposażenia</h3>';
			echo $this->ERR('Nie przypisano karty wyposażenia');
		}
	}
	
	public function inventoryCardHistory(array $d) {
		$d['history']->write('history', $d['inventoryCard']);
	}
	
	public function titleInventoryCardAdd(array $d) {
		echo 'Dodawanie karty wyposażenia';
	}
	
	public function inventoryCardAdd(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);
		$urlDevice = UFtpl_SruAdmin_InventoryCard::getDeviceUrl($d['device'], $url);
		
		echo '<h2>Nowa karta wyposażenia</h2>'; 
		echo $form->_start();
		echo $d['inventoryCard']->write('formAdd', $d['dormitories']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$urlDevice.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}
	
	public function titleInventoryCardEdit(array $d) {
		echo 'Edycja karty wyposażenia';
	}
	
	public function inventoryCardEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$url = $this->url(0);
		$urlDevice = UFtpl_SruAdmin_InventoryCard::getDeviceUrl($d['device'], $url);
		
		echo '<h2>Edycja karty wyposażenia urządzenia S/N '.$d['inventoryCard']->serialNo.'</h2>'; 
		echo $form->_start();
		echo $d['inventoryCard']->write('formEdit', $d['dormitories']);
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$urlDevice.'">Powrót</a>';
		echo $form->_end();
		echo $form->_end(true);
	}
	
	public function titleInventory(array $d) {
		echo 'Lista wyposażenia';
	}
	
	public function inventory(array $d) {
		echo '<h2>Lista wyposażenia SKOS</h2>';
		$d['inventory']->write('inventoryList');
	}
	
	public function inventoryNotFound(array $d) {
		echo $this->ERR('Błąd pobierania listy wyposażenia');
	}

	public function titleRoom(array $d) {
		echo $d['room']->write('titleDetails');
	}

	public function room(array $d) {
		if ($this->_srv->get('msg')->get('roomEdit/ok')) {
			echo $this->OK('Komentarz został zmieniony');
		}
		
		$d['room']->write('details', $d['leftRight'][0], $d['leftRight'][2]);
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
	
	public function roomDevices(array $d) {
		echo '<h3>Urządzenia</h3><ul>';
		$d['devices']->write('shortList');
		echo '</ul>';
	}

	public function roomDevicesNotFound(array $d) {
		if (empty($d) || $d['room']->typeId == UFbean_SruAdmin_Room::TYPE_SKOS) {
			echo '<h3>Urządzenia</h3>';
			echo $this->ERR('Brak urządzeń');
		}
	}
	
	public function roomSwitches(array $d) {
		echo '<h3>Switche</h3><ul>';
		$d['switches']->write('shortList');
		echo '</ul>';
	}

	public function roomSwitchesNotFound() {
		// jeśli nie ma switchy, to nci nie piszemy
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

	public function roomHistory(array $d) {
		$d['history']->write('history', $d['room']);
	}

	public function titleComputerAdd() {
		echo 'Dodaj komputer';
	}	
	public function computerAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset('Dodaj komputer');
		echo $d['computer']->write('formAdd', $d['user'], true, null, $d['servers'], $d['skosAdmins'], $d['waletAdmins'], $d['deviceModels'], $d['devicesInterfacable']);
		echo $form->_submit('Dodaj');
		echo $form->_end();
		echo $form->_end(true);
		
	}

	public function titlePenalties() {
		echo 'Aktywne kary';
	}	

	public function penalties(array $d) {
		$url = $this->url(0).'/penalties/';
		
		echo '<h2><a href="'.$url.'">Ostatnie akcje</a>| Aktywne kary | <a href="'.$url.'templates">Szablony</a></h2>';

		$d['penalties']->write('listPenalty', true);
	}	

	public function penaltiesNotFound() {
		echo $this->ERR('Nie znaleziono aktywnych kar');
	}

	public function titlePenaltyAdd(array $d) {
		echo 'Kara dla '.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')';
	}	

	public function penaltyAdd(array $d) {
		$urlUser = $this->url(0).'/users/'.$d['user']->id;
		
		echo '<h2>Kara dla <a href="'.$urlUser.'">'.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</a></h2>';
		
		echo '<div class="penalty">';
	
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset();
		isset($d['computerId']) ? $computerId = $d['computerId'] : $computerId = null;
		echo $d['penalty']->write('formAdd', $d['computers'], $d['templates'], $d['user'], $computerId, $d['ports']);
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

	public function userPenalties(array $d)	{	
		echo '<h2>Lista kar dla <a href="'.$this->url(1).'/'.$d['user']->id.'">'.$d['user']->name.' '.$d['user']->surname.' ('.$d['user']->login.')</a></h2>';

		$d['penalties']->write('listUserPenalty');
	}	
	
	public function titleComputerPenalties(array $d) {
		echo 'Lista kar i ostrzeżeń dla hosta '.$d['computer']->host;
	}	

	public function computerPenalties(array $d) {	
		echo '<h2>Lista kar dla hosta <a href="'.$this->url(1).'/'.$d['computer']->id.'">'.$d['computer']->host.'</a></h2>';

		$d['penalties']->write('listComputerPenalty');
	}

	public function penaltyActions(array $d) {
		$url = $this->url(0).'/penalties/';
		
		echo '<h2>Ostatnie akcje | <a href="'.$url.'active">Aktywne kary</a> | <a href="'.$url.'templates">Szablony</a></h2>';
		
		echo '<h3>Modyfikacje kar</h3>';
		if (!is_null($d['modifiedPenalties'])) {
			$d['modifiedPenalties']->write('penaltyLastModified');
		} else {
			$this->penaltiesNotFound();
		}
		echo '<h3>Nowe kary</h3>';
		if (!is_null($d['addedPenalties'])) {
			$d['addedPenalties']->write('penaltyLastAdded', true, true, 'penalties');
		} else {
			$this->penaltiesNotFound();
		}
		echo '<h3>Nowe ostrzeżenia</h3>';
		if (!is_null($d['addedWarnings'])) {
			$d['addedWarnings']->write('penaltyLastAdded', true, true, 'warnings');
		} else {
			$this->penaltiesNotFound();
		}
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
		$urlIpAll = $this->url(0).'/ips/';
		$urlIp = $this->url(0).'/ips/ds/';
		$urlDev = $this->url(0).'/devices/dorm/';
		$urlVlan = $this->url(0).'/ips/vlan/';
		if (!is_null($d['dorm'])) {
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlIp.$d['leftRight'][0]['alias'].'" ><</a> ';
			}
			echo $d['dorm']->name;
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlIp.$d['leftRight'][2]['alias'].'" >></a>';
			}
			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; '
				. 'zajętość puli (VLAN '.UFbean_SruAdmin_Vlan::getDefaultVlan().'): '.$d['used']->getIpCount().'/'.$d['sum']->getIpCount().' ~> '.($d['sum']->getIpCount() > 0 ? round($d['used']->getIpCount()/$d['sum']->getIpCount()*100) : 0).'% &bull; '
				. '<a href="'.$urlSw.$d['dorm']->alias.'">switche</a> &bull; '
				. '<a href="'.$urlDev.$d['dorm']->alias.'">urządzenia</a> &bull; '
				. '<a href="'.$urlIpAll.'">wszystkie IP</a>)</small></h2>';
		} else if (!is_null($d['vlan'])) {
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlVlan.$d['leftRight'][0]['id'].'" ><</a> ';
			}
			echo 'VLAN '.$d['vlan']->name.' ('.$d['vlan']->id.')';
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlVlan.$d['leftRight'][2]['id'].'" >></a>';
			}
			echo '<br/><small>(zajętość puli: '.$d['used']->getIpCount().'/'.$d['sum']->getIpCount().' ~> '.($d['sum']->getIpCount() > 0 ? round($d['used']->getIpCount()/$d['sum']->getIpCount()*100) : 0).'% &bull; <a href="'.$urlIpAll.'">wszystkie IP</a>)</small></h2>';
		} else {
			echo '<h2>Zestawienie numerów IP</h2>';
		}
		echo '<div class="ips">';

		$d['ips']->write('ips', $d['dorm'], $d['vlan']);
		echo '</div>';
	}
	
	public function ipsNotFound(array $d) {
		$url = $this->url(0).'/dormitories/';
		$urlSw = $this->url(0).'/switches/dorm/';
		$urlIpAll = $this->url(0).'/ips/';
		$urlIp = $this->url(0).'/ips/ds/';
		$urlVlan = $this->url(0).'/ips/vlan/';
		if (!is_null($d['dorm'])) {
						echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlIp.$d['leftRight'][0]['alias'].'" ><</a> ';
			}
			echo $d['dorm']->name;
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlIp.$d['leftRight'][2]['alias'].'" >></a>';
			}
			echo '<br/><small>(<a href="'.$url.$d['dorm']->alias.'">pokoje</a> &bull; zajętość puli: 0/0 ~> 0% &bull; <a href="'.$urlSw.$d['dorm']->alias.'">switche</a>)</small></h2>';
		} else if (!is_null($d['vlan'])) {
			echo '<h2>';
			if($d['leftRight'][0] != null){
				echo '<a href="'.$urlVlan.$d['leftRight'][0]['id'].'" ><</a> ';
			}
			echo 'VLAN '.$d['vlan']->name.' ('.$d['vlan']->id.')';
			if($d['leftRight'][2] != null){
				echo ' <a href="'.$urlVlan.$d['leftRight'][2]['id'].'" >></a>';
			}
			echo '<br/><small>(zajętość puli: 0 / 0 ~> 0% &bull; <a href="'.$urlIpAll.'">wszystkie IP</a>)</small></h2>';
		} else {
			echo '<h2>Zestawienie numerów IP</h2>';
		}
		echo $this->ERR('Brak przydzielonych adresów IP');
	}
	
	public function titleFwExceptions() {
		echo 'Wyjątki w firewallu';
	}
	
	public function fwExceptions(array $d) {
		if ($this->_srv->get('msg')->get('fwExceptionApplicationEdit/ok')) {
			echo $this->OK('Wniosek został zaopiniowany');
		}
		echo '<h2>Wyjątki w firewallu</h2>';
		$active = count($d['fwExceptionsActive']);
		echo '<h3>Aktywne ('.$active.')</h3>';
		if ($active > 0) {
			$d['fwExceptionsActive']->write('listExceptions');
		}
		$applications = count($d['fwApplications']);
		echo '<h3>Wnioski ('.$applications.')</h3>';
		if ($applications > 0) {
			$d['fwApplications']->write('listApplications', 0, true, false);
		}
	}
	
	public function titleFwExceptionEdit() {
		echo 'Zatwierdzanie wniosku o usługi serwerowe w SKOS PG';
	}
	
	public function fwExceptionEdit(array $d) {
		$form = UFra::factory('UFlib_Form');
		$acl = $this->_srv->get('acl');
		$editable = $acl->sruAdmin('fwexceptionapplication', 'edit', $d['fwApplication']->id);
		$url = $this->url(0).'/fwexceptions/';

		echo '<h2>Wniosek o usługi serwerowe w SKOS PG</h2>';
		echo $form->_start();
		$d['fwApplication']->write('skosFormEdit', $d['fwExceptions']);
		if ($editable) {
			echo $form->_submit('Zapisz');
		}
		echo $form->_end();
		echo '<p class="nav"><a href="'.$url.'">Powrót</a></p>';
		echo $form->_end(true);
	
	}
	
	public function fwExceptionNotFound() {
		echo $this->ERR('Nie znaleziono wniosku');
	}
	
	public function titleStatsUsers() {
		echo 'Statystyki użytkowników';
	}
	
	public function statsUsers(array $d) {
		echo '<h2>Użytkownicy | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		$d['users']->write('stats');
	}
	
	public function statsUsersNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function titleStatsPenalties() {
		echo 'Statystyki kar';
	}
	
	public function statsPenalties(array $d) {
		echo '<h2><a href="'.$this->url(1).'/users">Użytkownicy</a> | <a href="'.$this->url(1).'/dormitories">Akademiki</a> | Kary</h2>';
		$d['penalties']->write('stats');
	}
	
	public function statsPenaltiesNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function titleStatsDormitories() {
		echo 'Statystyki akademików';
	}
	
	public function statsDormitories(array $d) {
		echo '<h2><a href="'.$this->url(1).'/users">Użytkownicy</a> | Akademiki | <a href="'.$this->url(1).'/penalties">Kary</a></h2>';
		$d['users']->write('statsDorms');
	}
	
	public function statsDormitoriesNotFound(array $d) {
		echo $this->ERR('Błąd wyświetlenia statystyk');
	}

	public function adminPenaltiesAdded(array $d) {
		echo '<h3>Kary ostatnio dodane</h3>';
		$d['addedPenalties']->write('penaltyLastAdded', false, true, 'penalties');
	}

	public function adminWarningsAdded(array $d) {
		echo '<h3>Ostrzeżenia ostatnio dodane</h3>';
		$d['addedWarnings']->write('penaltyLastAdded', false, true, 'warnings');
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
	
	public function apisOtrsTickets(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->otrsUrl;
		
		if (is_null($d['tickets'])) {
			echo $this->ERR('Błąd pobierania zgłoszeń');
			return;
		} else if (count($d['tickets']) == 0) {
			echo $this->OK('Brak otwartych zgłoszeń');
			return;
		}
		echo '<ul>';
		foreach ($d['tickets'] as $ticket) {
			$ticketId = $ticket["TicketID"];
			echo '<li><a href="'.$url.'index.pl?Action=AgentTicketZoom;TicketID='.$ticketId.'">'.
				((isset($ticket['Title']) && $ticket['Title'] != '') ? $ticket['Title'] : '<i>bez tematu</i>').
				'</a> <small>(z '.$ticket['Created'].' od '.$ticket['CustomerUserID'].'; '.
				($ticket['LockID'] == '2' ? 'obsługiwany przez '.$ticket['Owner'] : 'nieprzydzielony').')</small></li>';
		}
		echo '</ul>';
	}
	
	public function apisZabbixProblems(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$url = $conf->zabbixUrl;
		
		if (is_null($d['problems'])) {
			echo $this->ERR('Błąd pobierania problemów');
			return;
		} else if (count($d['problems']) == 0) {
			echo $this->OK('Brak aktywnych problemów');
			return;
		}
		echo '<ul>';
		foreach ($d['problems'] as $problem) {
			echo '<li><a href="'.$url.'tr_status.php?hostid='.$problem->hosts[0]->hostid.'">'.$problem->hosts[0]->host.': '.$problem->description.'</a> <small>(z '.date(self::TIME_YYMMDD_HHMMSS, $problem->lastchange).')</small></li>';
		}
		echo '</ul>';
	}
	
	public function apisGetMacVendor(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		if (!is_null($d['vendor'])) {
			if ($conf->macVendorLookupAPIJson) {
				echo $d['vendor'][0]['company'];
			} else {
				echo $d['vendor'];
			}
		}
	}
	
	public function apisGetTasksSummary(array $d) {
		if (!is_null($d['tasks'])) {
			if ($d['tasks'] == 0) {
				echo $this->OK('brak zadań! :-)');
			} else {
				echo '<ul>';
				$this->displayTaskSummaryForService($d['zabbix'], 'Zabbiksie');
				$this->displayTaskSummaryForService($d['otrs'], 'OTRS');
				echo '</ul>';
			}
		}
	}
	
	private function displayTaskSummaryForService($count, $service) {
		if ($count > 0) {
			echo '<li class="ban"><a href="'.$this->url(0).'/admins/tasks">Zgłoszenia w '.$service.': '.$count.'</a></li>';
		}
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
					echo $computer['host'] . ' (' . $computer['ip'] . ') ';
				} else {
					echo $computer->host. ' (' . $computer->ip . ')';
				}
			}
		}
		echo "\n";
		echo 'Użytkownik: '.$d['user']->name.' "'.$d['user']->login.'" '.$d['user']->surname."\n";
		echo 'Admin: '.$d['admin']->name."\n";
		echo 'Szablon: '.$d['penalty']->templateTitle."\n";
		echo 'Min. długość (dni): '.(intval(($d['penalty']->amnestyAfter - $d['penalty']->startAt) / 24 / 3600))."\n";
		echo 'Powód: '.$d['penalty']->reason."\n";
		echo 'Komentarz: '.$d['penalty']->comment."\n";
		echo 'Link: https://'.$d['host'].'/admin/penalties/'.$d['penalty']->id."\n";
	}

	public function penaltyEditMailTitle(array $d) {
		if ($d['penalty']->typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Zmodyfikowano ostrzeżenie w DS'.substr($d['user']->dormitoryAlias, 2);
		} else {
			if ($d['penalty']->endAt <= NOW) {
				echo 'Zdjęto karę w DS'.substr($d['user']->dormitoryAlias, 2);
			} else if ($d['penalty']->endAt < $d['oldPenalty']->endAt) {
				echo 'Skrócono karę w DS'.substr($d['user']->dormitoryAlias, 2);
			} else if ($d['penalty']->endAt > $d['oldPenalty']->endAt) {
				echo 'Wydłużono karę w DS'.substr($d['user']->dormitoryAlias, 2);
			} else {
				echo 'Zmodyfikowano karę w DS'.substr($d['user']->dormitoryAlias, 2);
			}
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
		echo 'Min. długość (dni): '.(intval(($d['penalty']->amnestyAfter - $d['penalty']->startAt) / 24 / 3600));
		echo ($d['penalty']->amnestyAfter != $d['oldPenalty']->amnestyAfter) ? ' (było: '.(($d['oldPenalty']->amnestyAfter - $d['oldPenalty']->startAt) / 24 / 3600).')' : '';
		echo "\n";
		echo 'Powód: '.$d['penalty']->reason;
		echo ($d['penalty']->reason != $d['oldPenalty']->reason) ? ' (było: '.$d['oldPenalty']->reason.')' : '';
		echo "\n";
		echo 'Komentarz: '.$d['penalty']->comment."\n";
		echo 'Link: https://'.$d['host'].'/admin/penalties/'.$d['penalty']->id."\n";
	}

	public function switchPortModifiedMailTitle(array $d) {
		echo 'Zmodyfikowano port '.$d['port']->ordinalNo.' na switchu '.UFtpl_SruAdmin_Switch::displaySwitchName($d['port']->dormitoryAlias, $d['port']->switchNo, $d['port']->switchLab);
	}
	
	public function switchPortModifiedMailBody(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$host = $conf->sruUrl;

		echo 'Zmodyfikowano port '.$d['port']->ordinalNo.((!is_null($d['port']->locationAlias) && $d['port']->locationAlias != '') ? ' ('.$d['port']->locationAlias.')' : '').
				(!is_null($d['port']->connectedSwitchId) ? ' ('.$d['port']->connectedSwitchDorm.'-hp'.$d['port']->connectedSwitchNo.')' : '').
				' na switchu '.UFtpl_SruAdmin_Switch::displaySwitchName($d['port']->dormitoryAlias, $d['port']->switchNo, $d['port']->switchLab)."\n";
		if (!is_null($d['port']->comment) && $d['port']->comment != '') {
			echo 'Komentarz portu: '.$d['port']->comment."\n";
		}
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
	
	public function hostFwExceptionsChangedMailTitle(array $d) {
		echo 'Zmodyfikowano wyjątki FW hosta '.$d['host']->host;
	}
	
	public function hostFwExceptionsChangedMailBody(array $d) {
		echo $d['host']->write('hostFwExceptionsChangedMailBody', $d['deleted'], $d['added'], $d['admin']);
	}
	
	public function carerChangedToYouMailTitle(array $d) {
		echo 'Zostałes opiekunem hosta '.$d['host']->host;
	}
	
	public function carerChangedToYouMailBody(array $d) {
		echo $d['host']->write('carerChangedToYouMailBody', $d['admin']);
	}
}
