<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Admin
extends UFtpl_Common {

	public static $adminTypes = array(
		UFacl_SruAdmin_Admin::CENTRAL 	=> 'Administrator Centralny',
		UFacl_SruAdmin_Admin::CAMPUS 	=> 'Administrator Osiedlowy',
		UFacl_SruAdmin_Admin::LOCAL		=> 'Administrator Lokalny',
		UFacl_SruAdmin_Admin::BOT		=> 'BOT',
	);
	
	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login jest zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/mismatch' => 'Hasła różnią się',
		'name' => 'Podaj nazwę',
		'name/regexp' => 'Nazwa zawiera niedozwolone znaki',
		'name/textMax' => 'Nazwa jest za długa',
		'email' => 'Adres email jest nieprawidłowy ',
		'dormitoryId' => 'Wybierz akademik',
		'typeId' => 'Wybierz uprawnienia',
		'activeTo/tooOld' => 'Data starsza niż aktualna',
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
		$url = $this->url(0);
		if (array_key_exists($d['typeId'], UFtpl_SruAdmin_Admin::$adminTypes)) {
			$type = UFtpl_SruAdmin_Admin::$adminTypes[$d['typeId']];
		} else {
			$type = UFtpl_SruWalet_Admin::$adminTypes[$d['typeId']];
		}
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$type.' &bull; ostatnie logowanie: '.date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt']).')</small></h2>';
		echo '<p><em>Login:</em> '.$d['login'].'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Telefon:</em> '.$d['phone'].'</p>';
		echo '<p><em>Gadu-Gadu:</em> '.$d['gg'].'</p>';
		echo '<p><em>Jabber:</em> '.$d['jid'].'</p>';
		echo '<p><em>Adres:</em> '.$d['address'].'</p>';
		if($this->_srv->get('acl')->sruAdmin('admin', 'seeActiveTo', $d['id'])){
			if(!is_null($d['activeTo'])){
				echo '<p><em>Data dezaktywacji:</em> '.date(self::TIME_YYMMDD, $d['activeTo']).'</p>';
			}else{
				echo '<p><em>Data dezaktywacji nie została podana</em></p>';
			}
			if($d['active'] == true && $d['activeTo'] - time() <= UFra::shared('UFconf_Sru')->adminDeactivateAfter && $d['activeTo'] - time() >= 0)
				echo $this->ERR("Konto niedługo ulegnie dezaktywacji, należy przedłużyć jego ważność w CUI!");
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
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 3;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		echo $form->name('Nazwa', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Imię i nazwisko administratora lub inne oznaczenie." /><br/>'));
		$instrukcjaObslugiPolaAktywnyDo = 'Wypełnia centralny. Data w formacie YYYY-MM-DD<br/>Możliwe warunki to:<br/>1. Brak daty - administrator nigdy nie zostanie zdezaktywowany automatycznie.<br />2. Data >= now - administrator zostanie zdezaktywowany gdy przyjdzie na niego czas.<br />';
		if($this->_srv->get('acl')->sruAdmin('admin', 'addChangeActiveDate'))
			 echo $form->activeTo('Aktywny do', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="' . $instrukcjaObslugiPolaAktywnyDo . '" /><br/>')); 
		echo $form->typeId('Uprawnienia', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFtpl_SruAdmin_Admin::$adminTypes),
		));
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Lokalizacja lub miejsce przebywania administratora. Zawartość tego pola pojawi się w tabeli dyżurów, więc powinna być zgodna z formatem wpisywanych tam danych." /><br/>'));
		
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
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

		echo $form->_fieldset();
		
		echo $form->name('Nazwa', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Imię i nazwisko administratora lub inne oznaczenie." /><br/>'));
		
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		$instrukcjaObslugiPolaAktywnyDo = 'Wypełnia centralny. Data w formacie YYYY-MM-DD<br/>Możliwe warunki to:<br/>1. Brak daty - administrator nigdy nie zostanie zdezaktywowany automatycznie.<br />2. Data >= now - administrator zostanie zdezaktywowany gdy przyjdzie na niego czas.<br />';
		if($this->_srv->get('acl')->sruAdmin('admin', 'addChangeActiveDate'))
			echo $form->activeTo('Aktywny do', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="' . $instrukcjaObslugiPolaAktywnyDo . '" /><br/>'));
		else
			echo $form->activeTo('Aktywny do', array('disabled' => true, 'after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="' . $instrukcjaObslugiPolaAktywnyDo . '" /><br/>'));
		if($advanced)
		{
			echo $form->typeId('Uprawnienia', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(UFtpl_SruAdmin_Admin::$adminTypes),
			));	
			echo $form->active('Aktywny', array('type'=>$form->CHECKBOX) );

		}
		

		echo $form->_end();
		
		echo $form->_fieldset('Dane kontaktowe');
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Lokalizacja lub miejsce przebywania administratora. Zawartość tego pola pojawi się w tabeli dyżurów, więc powinna być zgodna z formatem wpisywanych tam danych." /><br/>'));

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		$tmp[''] = 'N/D';
		echo $form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
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
			echo $form->dutyHours(UFtpl_SruAdmin_DutyHours::getDayName($i), array('name'=>'adminEdit[dutyHours]['.$i.']', 'value'=>$dHour));
			echo $form->dhComment('Komentarz', array('name'=>'adminEdit[dhComment]['.$i.']', 'value'=>$dhComment));
			echo $form->dhActive('Odbędzie się', array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dhActive]['.$i.']', 'value'=>$dhActive));
		}

		echo $form->_end();

		if($this->_srv->get('acl')->sruAdmin('admin', 'changeAdminDorms')) {
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
			echo $form->_end();
		}

		if($this->_srv->get('acl')->sruAdmin('admin', 'changeUsersAndHostsDisplay', $d['id'])) {
			echo $form->_fieldset('Ustawienia');

			if(isset($_COOKIE['SRUDisplayUsers']) && $_COOKIE['SRUDisplayUsers'] == "1")
				echo $form->displayUsers('Widok pokoju: użytkownicy i hosty - tylko aktywne', array('type'=>$form->CHECKBOX, 'value'=>'1'));
			else
				echo $form->displayUsers('Widok pokoju: użytkownicy i hosty - tylko aktywne', array('type'=>$form->CHECKBOX, 'value'=>'0'));
			echo $form->_end();
		}
		echo $form->_fieldset();
	}

	public function adminBar(array $d, $ip, $time) {
		echo '<a href="'.$this->url(0).'/admins/'.$d['id'].'">'.$this->_escape($d['name']).'</a> ';
		if (!is_null($time) && $time != 0 ) {
			echo 'Ostatnie&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $time).' ' ;
		}
		if (!is_null($ip)) {
			echo '('.$ip.') ';
		}
	}
	
	public function apiAdminsOutdated(array $d) {
		foreach ($d as $c) {
			echo $c['login']."\n";
		}
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
