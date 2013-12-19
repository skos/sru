<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Admin
extends UFtpl_Common {

	public static $adminTypes = array(
		UFacl_SruAdmin_Admin::CENTRAL 	=> 'Administrator Centralny',
		UFacl_SruAdmin_Admin::CAMPUS 	=> 'Administrator Lokalny (team leader)',
		UFacl_SruAdmin_Admin::LOCAL	=> 'Administrator Lokalny',
		UFacl_SruAdmin_Admin::BOT	=> 'BOT',
	);
	
	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login jest zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Nieprawidłowy format hasła',
		'password/mismatch' => 'Hasła różnią się',
		'password/same' => 'Hasło jest identyczne z poprzednim',
		'password/sameAsInner' => 'Hasło jest identyczne zhasłem wewnętrznym',
		'passwordInner' => 'Nieprawidłowy format hasła',
		'passwordInner/mismatch' => 'Hasła różnią się',
		'passwordInner/same' => 'Hasło jest identyczne z poprzednim',
		'passwordInner/sameAsMain' => 'Hasło jest identyczne z hasłem do SRU',
		'name' => 'Podaj nazwę',
		'name/regexp' => 'Nazwa zawiera niedozwolone znaki',
		'name/textMax' => 'Nazwa jest za długa',
		'email' => 'Adres email jest nieprawidłowy ',
		'dormitoryId' => 'Wybierz akademik',
		'typeId' => 'Wybierz uprawnienia',
		'activeTo/tooOld' => 'Data starsza niż aktualna',
		'active/tooOld' => 'Przeszła data aktywności',
	);	
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$this->_escape($d['name']).'</p>';
	}
	
	public function listAdmin(array $d, $waletAdmin = false) {
		$url = $this->url(0).'/admins/';
		$acl = $this->_srv->get('acl');

		$lastDom = '-';
		foreach ($d as $c) {
			if ($waletAdmin && !$acl->sruWalet('dorm', 'view', $c['dormitoryAlias'])) {
				continue;
			}
			if($lastDom != $c['dormitoryId']) {
				if($lastDom != '-')
					echo '</ul>';
				if (is_null($c['dormitoryName'])) {
					echo '<h3>Spoza akademików</h3>';
				} else {
					echo '<h3><a href="'.$this->url(0).'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryName'].'</a></h3>';
				}
				echo '<ul>';		
			}
			
			echo '<li><a href="'.$url.$c['id'].'">';
			switch($c['typeId']) {
				case 1:
						echo '<strong>'.$this->_escape($c['name']).'</strong>';
						break;
				case 2:
						echo '<em>'.$this->_escape($c['name']).'</em>';
						break;
				case 3:
						echo $this->_escape($c['name']);
						break;
			}
			echo '</a></li>';
			
			$lastDom = $c['dormitoryId'];
			
		}
		echo '</ul>';
	}
	public function listBots(array $d) {
		$url = $this->url(0).'/admins/';
		
		if(!count($d))
			return;

		echo '<ul>';	
		foreach ($d as $c)
		{
			if($c['active'] == false){
				echo '<li><del><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).'</a></del></li>';
			}else{
				echo '<li><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).'</a></li>';
			}
		}
		echo '</ul>';
	}
	public function titleDetails(array $d) {
		echo $this->_escape($d['name']);
	}	
	public function details(array $d) {
		if (array_key_exists($d['typeId'], UFtpl_SruAdmin_Admin::$adminTypes)) {
			$type = UFtpl_SruAdmin_Admin::$adminTypes[$d['typeId']];
		} else {
			$type = UFtpl_SruWalet_Admin::$adminTypes[$d['typeId']];
		}
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$type.
				' &bull; ostatnie logowanie: '.((is_null($d['lastLoginAt']) || $d['lastLoginAt'] == 0) ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt'])).
				' &bull; ostatnie nieudane logowanie: '.((is_null($d['lastInvLoginAt']) || $d['lastInvLoginAt'] == 0) ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastInvLoginAt'])).')</small></h2>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Telefon:</em> '.$d['phone'].'</p>';
		echo '<p><em>Gadu-Gadu:</em> '.$d['gg'].'</p>';
		echo '<p><em>Jabber:</em> '.$d['jid'].'</p>';
		echo '<p><em>Adres:</em> '.$d['address'].'</p>';

		if(!is_null($d['activeTo'])){
			echo '<p><em>Data dezaktywacji:</em> '.date(self::TIME_YYMMDD, $d['activeTo']).'</p>';
		}else{
			echo '<p><em>Data dezaktywacji:</em>Data dezaktywacji nie została podana</p>';
		}
		echo '<p><em>Ostatnia zmiana hasła:</em> '.((is_null($d['lastPswChange']) || $d['lastPswChange'] == 0) ? 'brak' : date(self::TIME_YYMMDD_HHMM, $d['lastPswChange'])).'</p>';
		echo '<p><em>Ostatnia zmiana hasła wew.:</em> '.((is_null($d['lastPswInnerChange']) || $d['lastPswInnerChange'] == 0) ? 'brak' : date(self::TIME_YYMMDD_HHMM, $d['lastPswInnerChange'])).'</p>';
		if(($d['id'] == $this->_srv->get('session')->authAdmin || ($this->_srv->get('session')->is('typeId') 
					&& ($this->_srv->get('session')->typeId == UFacl_SruAdmin_Admin::CENTRAL 
						|| $this->_srv->get('session')->typeId == UFacl_SruAdmin_Admin::CAMPUS)))
					&& $d['active'] == true && $d['activeTo'] - time() <= UFra::shared('UFconf_Sru')->adminDeactivateAfter && $d['activeTo'] - time() >= 0){
			echo $this->ERR("<br />Konto niedługo ulegnie dezaktywacji, należy przedłużyć jego ważność w CUI!<br />&nbsp;");
		}
	}

	public function listDorms($d, $dormList) {
		$url = $this->url(0);

		echo '</p><ul>';
		foreach ($dormList as $dorm) {
			echo '<li><a href="'.$url.'/dormitories/'.$dorm['dormitoryAlias'].'">'.$dorm['dormitoryName'].'</a></li>';
		}
		echo '</ul>';
	}

	public function titleAdd(array $d) {
		echo 'Dodanie nowego administratora';
	}

	private $instrukcjaObslugiPolaAktywnyDo = 'Wypełnia administrator centralny. Data w formacie YYYY-MM-DD<br/>Możliwości:<br/>1. Brak daty - administrator nigdy nie zostanie zdezaktywowany automatycznie.<br />2. Data >= now() - administrator zostanie zdezaktywowany w danym dniu.';
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 3;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'after'=> UFlib_Helper::displayHint("Hasło do logowania się do SRU. Musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny.")));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		echo $form->name('Nazwa', array('after'=>UFlib_Helper::displayHint("Imię i nazwisko administratora lub inne oznaczenie.")));
		if($this->_srv->get('acl')->sruAdmin('admin', 'addChangeActiveDate')) {
			 echo $form->activeTo('Aktywny do', array('type' => $form->CALENDER, 'after'=> UFlib_Helper::displayHint($this->instrukcjaObslugiPolaAktywnyDo)));
		}
		echo $form->typeId('Uprawnienia', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFtpl_SruAdmin_Admin::$adminTypes),
		));
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres', array('after'=> UFlib_Helper::displayHint("Lokalizacja lub miejsce przebywania administratora. Zawartość tego pola pojawi się w tabeli dyżurów, więc powinna być zgodna z formatem wpisywanych tam danych.")));
		
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		$tmp['0'] = 'N/D';
		echo $form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
	}

	public function formEdit(array $d, $dormitories, $dutyHours, $dormList, $advanced=false) {
		if(!is_null($d['activeTo'])){
			$d['activeTo'] = date(self::TIME_YYMMDD, $d['activeTo']);
		}else{
			$d['activeTo'] = '';
		}
		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		echo $form->_fieldset('Dane podstawowe');
		
		echo $form->name('Nazwa', array('after'=> UFlib_Helper::displayHint("Imię i nazwisko administratora lub inne oznaczenie.")));
		
		echo $form->password('Hasło', array('type'=>$form->PASSWORD, 'after'=> UFlib_Helper::displayHint("Hasło do logowania się do SRU. Musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny.")));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		echo $form->passwordInner('Hasło wewnętrzne', array('type'=>$form->PASSWORD, 'after'=> UFlib_Helper::displayHint("Hasło do logowania się do systemów SKOS. Musi mieć co najmniej 8 znaków, zawierać co najmniej 1 dużą literę, 1 małą literę, 1 cyfrę i 1 znak specjalny.")));
		echo $form->passwordInner2('Powtórz hasło wewnętrzne', array('type'=>$form->PASSWORD));

		if($this->_srv->get('acl')->sruAdmin('admin', 'addChangeActiveDate'))
			echo $form->activeTo('Aktywny do', array('type' => $form->CALENDER, 'after'=>UFlib_Helper::displayHint($this->instrukcjaObslugiPolaAktywnyDo)));
		else
			echo $form->activeTo('Aktywny do', array('disabled' => true, 'after'=>UFlib_Helper::displayHint($this->instrukcjaObslugiPolaAktywnyDo)));
		if($advanced)
		{
			echo $form->typeId('Uprawnienia', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(UFtpl_SruAdmin_Admin::$adminTypes),
			));	
			echo $form->active('Aktywny', array('type'=>$form->CHECKBOX) );

		}
		
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$this->url(0).'/admins/'.$d['id'].'">Powrót</a>';
		echo $form->_end();
		
		echo $form->_fieldset('Dane kontaktowe');
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres', array('after'=>UFlib_Helper::displayHint("Lokalizacja lub miejsce przebywania administratora. Zawartość tego pola pojawi się w tabeli dyżurów, więc powinna być zgodna z formatem wpisywanych tam danych.")));

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		$tmp[''] = 'N/D';
		echo $form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->_submit('Zapisz');
		echo ' <a href="'.$this->url(0).'/admins/'.$d['id'].'">Powrót</a>';
		echo $form->_end();

		echo $form->_fieldset('Godziny dyżurów');
		if ($this->_srv->get('msg')->get('adminEdit/errors/startHour')) {
			echo $this->ERR('Godzina rozpoczęcia dyżuru jest niepoprawna');
		}
		if ($this->_srv->get('msg')->get('adminEdit/errors/endHour')) {
			echo $this->ERR('Godzina zakończenia dyżuru jest niepoprawna');
		}

		$dHours = array();
		if ($dutyHours != null) {
			foreach ($dutyHours as $dh) {
				$dHours[$dh['day']] = new DutyHour($dh['startHour'], $dh['endHour'], $dh['active'], $dh['comment']);
			}
		}
		for ($i = 1; $i <= 7; $i++) {
			$dHour = '';
			$dhComment = '';
			$dhActive = true;
			if (isset($dHours[$i])) {
				$dHour = $dHours[$i]->getHours();
				$dhComment = $dHours[$i]->getComment();
				$dhActive = $dHours[$i]->getActive();
			}
			echo $form->dutyHours(UFtpl_SruAdmin_DutyHours::getDayName($i), array('name'=>'adminEdit[dutyHours]['.$i.']', 'value'=>$dHour, 'after'=>UFlib_Helper::displayHint("Godziny dyżurów danego dnia w formacie: 20:00-21:00")));
			echo $form->dhComment('Komentarz', array('name'=>'adminEdit[dhComment]['.$i.']', 'value'=>$dhComment));
			echo $form->dhActive('Odbędzie się', array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dhActive]['.$i.']', 'value'=>$dhActive));
			echo '<br />';
		}

		echo $form->_submit('Zapisz');
		echo ' <a href="'.$this->url(0).'/admins/'.$d['id'].'">Powrót</a>';
		echo $form->_end();

		if($this->_srv->get('acl')->sruAdmin('admin', 'changeAdminDorms', $d['id'])) {
			$post = $this->_srv->get('req')->post;
			echo '<div id="dorms">' . $form->_fieldset('Domy studenckie pod opieką');
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
			
			echo $form->_submit('Zapisz');
			echo ' <a href="'.$this->url(0).'/admins/'.$d['id'].'">Powrót</a>';
			echo $form->_end();
		}

		if($this->_srv->get('acl')->sruAdmin('admin', 'changeUsersAndHostsDisplay', $d['id'])) {
			echo $form->_fieldset('Ustawienia');

			$textDisplayUsers = 'Widok pokoju: użytkownicy i hosty - tylko aktywne; wyszukiwanie użytkowników: tylko aktywni';
			if(isset($_COOKIE['SRUDisplayUsers']) && $_COOKIE['SRUDisplayUsers'] == "1")
				echo $form->displayUsers($textDisplayUsers, array('type'=>$form->CHECKBOX, 'value'=>'1'));
			else
				echo $form->displayUsers($textDisplayUsers, array('type'=>$form->CHECKBOX, 'value'=>'0'));
			
			echo $form->_submit('Zapisz');
			echo ' <a href="'.$this->url(0).'/admins/'.$d['id'].'">Powrót</a>';
			echo $form->_end();
		}
	}

	public function adminBar(array $d, $ip, $time, $invIp, $invTime) {
		echo '<a href="'.$this->url(0).'/admins/'.$d['id'].'">'.$this->_escape($d['name']).'</a> ';
		if (!is_null($time) && $time != 0 ) {
			echo '<br/><small>Ostatnie&nbsp;udane&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $time).'</small> ' ;
			if (!is_null($ip)) {
				echo '<small>('.$ip.')</small> ';
			}
		}
		if (!is_null($invTime) && $invTime != 0 ) {
			echo '<br/><small>Ostatnie&nbsp;nieudane&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $invTime).'</small> ' ;
			if (!is_null($invIp)) {
				echo '<small>('.$invIp.')</small> ';
			}
		}
		echo '<br/>';
	}

	public function toDoList(array $d, $users) {
		$url = $this->url(0);
		echo '<h3>Problemy w Zabbiksie:</h3>';
		echo '<div id="zabbixproblems"><img class="loadingImg" src="'.UFURL_BASE.'/i/img/ladowanie.gif" alt="Trwa ładowanie problemów..." /></div>';

?>
<script>
$("#zabbixproblems").load('<?=UFURL_BASE?>/admin/apis/zabbixproblems');
</script>
<?
		echo '<h3>Otwarte tickety w OTRS:</h3>';
		echo '<div id="otrstickets"><img class="loadingImg" src="'.UFURL_BASE.'/i/img/ladowanie.gif" alt="Trwa ładowanie ticketów..." /></div>';

?>
<script>
$("#otrstickets").load('<?=UFURL_BASE?>/admin/apis/otrstickets');
</script>
<?
		if (!is_null($users)) {
			echo '<h3>Hosty bez przypisanego opiekuna:</h3>';
			echo '<ul>';
			foreach ($users as $dorm) {
				if (!is_null($dorm)) {
					foreach ($dorm as $comp) {
						echo '<li'.($comp['banned']?' class="ban"' : '').'><a href="'.$url.'/dormitories/'.$comp['dormitoryAlias'].'">'.strtoupper($comp['dormitoryAlias']).'</a>: <a href="'.$url.'/computers/'.$comp['id'].'">'.$comp['host'].' <small>'.$comp['ip'].'/'.$comp['mac'].'</small></a> <span>'.(is_null($comp['availableTo']) ? '' : date(self::TIME_YYMMDD, $comp['availableTo'])).'</span>'.(strlen($comp['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$comp['comment'].'" />':'').'</li>';
					}
				}
			}
			echo '</ul>';
		}
	}
	
	public function apiAdminsOutdated(array $d) {
		foreach ($d as $c) {
			echo $c['login']."\n";
		}
	}

	public function activeOnDateAdmins(array $d, $date) {
		$url = $this->url(0).'/admins/';
		$admins = array();
		$inactive = array();

		foreach ($d as $c) {
			if(!array_key_exists($c['id'], $admins) && !array_key_exists($c['id'], $inactive)) {
				if ($c['active']) {
					$admins[$c['id']] = $c['name'];
				} else {
					$inactive[$c['id']] = 0;
				}
			}
		}

		asort($admins);
		echo '<h2>Administratorzy aktywni w dniu: '.$date.' ('.count($admins).')</h2>';
		echo '<ul>';
		while (!is_null(key($admins))) {
			if (current($admins)) {
				echo '<li><a href="'.$url.key($admins).'">'.current($admins).'</a></li>';
				next($admins);
			}
		}
		echo '</ul>';
	}
}

class DutyHour
{
	private $hours;
	private $active;
	private $comment;

	function __construct($startHour, $endHour, $active, $comment) {
		$this->hours = $this->formatHour($startHour).'-'.$this->formatHour($endHour);
		$this->active = $active;
		$this->comment = $comment;
	}

	public function getHours() {
		return $this->hours;
	}

	public function getActive() {
		return $this->active;
	}

	public function getComment() {
		return $this->comment;
	}

	private function formatHour($hour) {
		return substr($hour, 0, -2).':'.substr($hour, -2);
	}
}
