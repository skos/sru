<?
/**
 * template admina Waleta
 */
class UFtpl_SruWalet_Admin
extends UFtpl_Common {

	public static $adminTypes = array(
		UFacl_SruWalet_Admin::DORM 	=> 'Pracownik OS',
		UFacl_SruWalet_Admin::OFFICE 	=> 'Pracownik OS - SF',
		UFacl_SruWalet_Admin::HEAD 	=> 'Kierownik OS',
	    	UFacl_SruWalet_Admin::PORTIER 	=> 'Portier',
	);
	
	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login jest zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Nieprawidłowy format hasła',
		'password/mismatch' => 'Hasła różnią się',
	    	'password/same' => 'Hasło jest identyczne z poprzednim',
		'name' => 'Podaj nazwę',
		'name/regexp' => 'Nazwa zawiera niedozwolone znaki',
		'name/textMax' => 'Nazwa jest za długa',
		'email' => 'Adres email jest nieprawidłowy ',
		'dormitoryId' => 'Wybierz akademik',
		'typeId' => 'Wybierz uprawnienia',
	);	
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$this->_escape($d['name']).'</p>';
	}
	
	public function listAdmin(array $d, array $dorms, $tblId = 0) {
		$url = $this->url(0).'/admins/';
		$baseUrl = $this->url(0);

		echo '<table id="adminsT'.$tblId.'" class="bordered"><thead><tr>';
		echo '<th>Administrator</th>';
		echo '<th>Ostatnie logowanie</th>';
		echo '<th>DS-y pod opieką</th>';
		echo '</tr></thead><tbody>';

		foreach ($d as $c) {
			echo '<tr><td><a href="'.$url.$c['id'].'">';
			switch($c['typeId']) {
				case UFacl_SruWalet_Admin::HEAD:
						echo '<strong>'.$this->_escape($c['name']).'</strong></a>';
						break;
				case UFacl_SruWalet_Admin::OFFICE:
						echo '<i>'.$this->_escape($c['name']).'</i></a>';
						break;
				case UFacl_SruWalet_Admin::DORM:
						echo $this->_escape($c['name']).'</a>';
						break;
				case UFacl_SruWalet_Admin::PORTIER:
						echo '<u>'.$this->_escape($c['name']).'</u></a>';
						break;
			}
			echo '</td><td>'.($c['lastLoginAt'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $c['lastLoginAt'])).'</td>';
			if($c['typeId'] == UFacl_SruWalet_Admin::HEAD){
				echo '<td>wszystkie</td></tr>';
			}else if(is_null($dorms[$c['id']]) || $c['typeId'] == UFacl_SruWalet_Admin::PORTIER){
				echo '<td>żaden</td></tr>';
			}else{
				echo '<td>';
				foreach($dorms[$c['id']] as $dorm){
					echo '<a href="'.$baseUrl.'/dormitories/'.$dorm['dormitoryAlias'].'">'.strtoupper($dorm['dormitoryAlias']).'</a> ';
				}
				echo '</td></tr>';
			}
		}
		echo '</tbody></table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#adminsT<?=$tblId?>").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}

	public function listAdminSimple(array $d) {
		$url = $this->url(0).'/admins/';
		
		if(!count($d))
			return;

		echo '<ul>';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).'</a></li>';
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']);
	}

	public function details(array $d) {
		$session = $this->_srv->get('session');
		$sruConf = UFra::shared('UFconf_Sru');
		$url = $this->url(0);
		if (array_key_exists($d['typeId'], UFtpl_SruWalet_Admin::$adminTypes)) {
			$type = UFtpl_SruWalet_Admin::$adminTypes[$d['typeId']];
		} else {
			$type = UFtpl_SruAdmin_Admin::$adminTypes[$d['typeId']];
		}
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$type.
				' &bull; ostatnie logowanie: '.((is_null($d['lastLoginAt']) || $d['lastLoginAt'] == 0) ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt'])).
				' &bull; ostatnie nieudane logowanie: '.((is_null($d['lastInvLoginAt']) || $d['lastInvLoginAt'] == 0) ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastInvLoginAt'])).')</small></h2>';
		$timeToInvalidatePassword = $d['lastPswChange'] + $sruConf->passwordValidTime - time();
		if(($d['id'] == $session->authWaletAdmin || ($session->is('typeIdWalet') && $session->typeIdWalet == UFacl_SruWalet_Admin::HEAD)) 
			&& $d['active'] == true && ($timeToInvalidatePassword < $sruConf->passwordOutdatedWarning)) {
		    echo $this->ERR("<br />Hasło niedługo (za " . UFlib_Helper::secondsToTime($timeToInvalidatePassword) . ") straci ważność, należy je zmienić!<br />&nbsp;");
		}
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Telefon:</em> '.$d['phone'].'</p>';
		echo '<p><em>Jabber:</em> '.$d['jid'].'</p>';
		echo '<p><em>Adres:</em> '.$d['address'].'</p>';
	}

	public function listDorms(array $d, $dormList) {
		$url = $this->url(0);
		
		if (is_null($dormList)) {
			echo ' brak przypisanych DS</p>';
		} else {
			echo '</p><ul>';
			foreach ($dormList as $dorm) {
				echo '<li><a href="'.$url.'/dormitories/'.$dorm['dormitoryAlias'].'">'.$dorm['dormitoryName'].'</a></li>';
			}
			echo '</ul>';
		}
	}

	public function titleAdd(array $d) {
		echo 'Dodanie nowego administratora';
	}		
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 11;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->login('Login', array('class'=>'required'));
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'class'=>'required', 'after'=>UFlib_Helper::displayHint("Hasło musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny")));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		echo $form->name('Imię i nazwisko', array('after'=>UFlib_Helper::displayHint("Imię i nazwisko administratora lub inne oznaczenie."), 'class'=>'required')); 
		echo $form->typeId('Uprawnienia', array( 
			'type' => $form->SELECT, 
			'labels' => $form->_labelize(UFtpl_SruWalet_Admin::$adminTypes), 
			'after'=> UFlib_Helper::displayHint("Kierownik OS ma uprawnienia do wszystkich części Waleta, zaś Pracownik OS jedynie do wybranych Domów Studenckich. Pracownik OS - SF ma dostęp do obsadzenia każdego DSu."), 
		));
		echo $form->_end();

		echo '<div id="dorms">' . $form->_fieldset('Domy studenckie');
		$post = $this->_srv->get('req')->post;
		foreach ($dormitories as $dorm) {
			$permission = 0;
			try {
				$permission = $post->adminAdd['dorm'][$dorm['id']];
			} catch (UFex_Core_DataNotFound $e) {
			}
			echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminAdd[dorm]['.$dorm['id'].']', 'id'=>'adminAdd[dorm]['.$dorm['id'].']', 'value'=>$permission));
		}
		echo $form->_end() . '</div>';
		
?><script type="text/javascript">
(function (){
	form = document.getElementById('adminAdd_typeId');
	function changeVisibility() { 
		var div = document.getElementById("dorms"); 
		if (form.value == <? echo UFacl_SruWalet_Admin::HEAD; ?> || form.value == <? echo UFacl_SruWalet_Admin::PORTIER; ?>) { 
			div.style.display = "none"; 
			div.style.visibility = "hidden"; 
		} else { 
			div.style.display = "block"; 
			div.style.visibility = "visible"; 
		}
	}
	form.onchange = changeVisibility;
})()
</script><?

		echo $form->_fieldset();
		echo $form->email('E-mail', array('class'=>'required'));
		echo $form->phone('Telefon');
		echo $form->jid('Jabber', array('after'=>UFlib_Helper::displayHint("Adres w komunikatorze sieci Jabber.")));
		echo $form->address('Adres', array('after'=>UFlib_Helper::displayHint("Lokalizacja lub miejsce przebywania administratora.")));
	}

	public function formEdit(array $d, $dormitories, $dormList, $advanced=false) {
		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->name('Imię i nazwisko', array('after'=>UFlib_Helper::displayHint("Imię i nazwisko administratora lub inne oznaczenie."), 'class'=>'required'));
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'class'=>'required', 'after'=>UFlib_Helper::displayHint("Hasło musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny")));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		if($advanced) {
			echo $form->typeId('Uprawnienia', array( 
				'type' => $form->SELECT, 
				'labels' => $form->_labelize(UFtpl_SruWalet_Admin::$adminTypes), 
				'after'=> UFlib_Helper::displayHint("Kierownik OS ma uprawnienia do wszystkich części Waleta, zaś Pracownik OS jedynie do wybranych Domów Studenckich. Pracownik OS - SF ma dostęp do obsadzenia każdego DSu."), 
			));
			echo $form->active('Aktywny'.UFlib_Helper::displayHint("Tylko aktywni administratorzy mogą zalogować się do Waleta.").'', array('type'=>$form->CHECKBOX) );
		}

		echo $form->_end();

		if($advanced) {
			$post = $this->_srv->get('req')->post;
			echo '<div id="dorms">' . $form->_fieldset('Domy studenckie');
			foreach ($dormitories as $dorm) {
				$permission = 0;
				try {
					$permission = $post->adminEdit['dorm'][$dorm['id']];
				} catch (UFex_Core_DataNotFound $e) {
					if (!is_null($dormList)) {
						foreach ($dormList as $perm) {
							if ($perm['dormitory'] == $dorm['id']) {
								$permission = 1;
								break;
							}
						}
					}
				}
				echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dorm]['.$dorm['id'].']', 'id'=>'adminEdit[dorm]['.$dorm['id'].']', 'value'=>$permission));
			}
			echo $form->_end() . '</div>';
?><script type="text/javascript">
(function (){
	form = document.getElementById('adminEdit_typeId');
	function changeVisibility() { 
		var div = document.getElementById("dorms"); 
		if (form.value == <? echo UFacl_SruWalet_Admin::HEAD; ?> || form.value == <? echo UFacl_SruWalet_Admin::PORTIER; ?>) { 
			div.style.display = "none"; 
			div.style.visibility = "hidden"; 
		} else { 
			div.style.display = "block"; 
			div.style.visibility = "visible"; 
		}
	}
	form.onchange = changeVisibility;
	function selectType(typeId){ 
		form.selectedIndex = (typeId - 11);
	}
	selectType(<? echo $d['typeId']; ?>);
 	changeVisibility();
})()
</script><?
		}
		
		echo $form->_fieldset();
		echo $form->email('E-mail', array('class'=>'required'));
		echo $form->phone('Telefon');
		echo $form->jid('Jabber', array('after'=>UFlib_Helper::displayHint("Adres w komunikatorze sieci Jabber.")));
		echo $form->address('Adres', array('after'=>UFlib_Helper::displayHint("Lokalizacja lub miejsce przebywania administratora.")));
	}
	
	public function ownPswEdit(array $d){
	    $form = UFra::factory('UFlib_Form', 'adminOwnPswEdit', $d, $this->errors);
	    echo $form->_fieldset('Okresowa zmiana hasła');
	    echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'after'=> UFlib_Helper::displayHint("Hasło do logowania się do SRU. Musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny.")));
	    echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
	    echo $form->_submit('Zapisz');
	    echo $form->_end();
	}

	public function waletBar(array $d, $ip, $time, $invIp, $invTime) {
		$acl = $this->_srv->get('acl');
		$walet = UFra::factory('UFbean_SruWalet_Admin');
		$walet->getFromSession();
		$sruConf = UFra::shared('UFconf_Sru');
		$timeToInvalidatePassword = $d['lastPswChange'] + $sruConf->passwordValidTime - time();
		
		echo '<ul class="menu">';
		if($d['typeId'] != UFacl_SruWalet_Admin::PORTIER && $timeToInvalidatePassword < $sruConf->passwordOutdatedWarning){
		    echo '<a href="'.$this->url(0).'/admins/'.$walet->id.'/:changepassword"><span class="head-icon" title="Zbliża się czas wygaśnięcia hasła"><span class="ui-icon ui-icon-key"></span></span></a>';
		}
		echo '<li><a class="mainMenuItem" href="#">'.$this->_escape($d['name']).'</a>';
		echo '<ul>';
		if ($acl->sruWalet('admin', 'view')) {
			echo '<li><a href="'.$this->url(0).'/admins/'.$d['id'].'">Mój profil</a></li>';;
		}
		if (!is_null($time) && $time != 0 ) {
			echo '<li class="menuLoginItem">Ostatnie&nbsp;udane&nbsp;logowanie:<br/>'.date(self::TIME_YYMMDD_HHMM, $time);
			if (!is_null($ip)) {
				echo ' ('.$ip.')';
			}
			echo '</li>';
		}
		if (!is_null($invTime) && $invTime != 0 ) {
			echo '<li class="menuLoginItem">Ostatnie&nbsp;nieudane&nbsp;logowanie:<br/>'.date(self::TIME_YYMMDD_HHMM, $invTime);
			if (!is_null($invIp)) {
				echo ' ('.$invIp.')';
			}
			echo '</li>';
		}
		echo '<li><a href="'.$this->url(0).'/logout">Wyloguj</a></li>';
		echo '</ul></li></ul>';
?>
<script type="text/javascript">
	$(document).ready(function () {
		$('.menu').jqsimplemenu();
	});
</script>
<?
	}

	public function toDoList(array $d, $users) {
		$url = $this->url(0);
		
		if (is_null($users)) {
			echo $this->OK('Brak zadań!');
		} else {
			echo '<h3>Mieszkańcy bez uzupełnionych danych osobowych:</h3>';
			$lp = 0;
			echo '<p><label for="filter">Filtruj:</label> <input type="text" name="filter" value="" id="filter" /></p>';
			echo '<table id="withoutRegistryT" class="bordered"><thead><tr>';
			echo '<th>L.p.</th>';
			echo '<th>Imię</th>';
			echo '<th>Nazwisko</th>';
			echo '<th>Dom Studencki</th>';
			echo '<th>Akcja</th>';
			echo '</tr></thead><tbody>';
			foreach ($users as $dorm) {
				if (!is_null($dorm)) {
					foreach ($dorm as $user) {
						$lp++;
						echo '<td>'.$lp.'</td>';
						echo '<td><a href="'.$url.'/users/'.$user['id'].'">'.$this->_escape($user['name']).'</a></td>';
						echo '<td><a href="'.$url.'/users/'.$user['id'].'">'.$this->_escape($user['surname']).'</a></td>';
						echo '<td><a href="'.$url.'/dormitories/'.$user['dormitoryAlias'].'">'.strtoupper($user['dormitoryAlias']).'</a></td>';
						echo '<td><a href="'.$url.'/users/'.$user['id'].'/:edit">Edytuj</a></td></tr>';
					}
				}
			}
			echo '</body></table>';
		}
?>
<script type="text/javascript">
$(document).ready(function()
    {
        $("#withoutRegistryT").tablesorter({
			headers: {
				3: {
					sorter: "ds"
				},
				4: {
					sorter: false
				}
			}
		});
    }
);

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
}
