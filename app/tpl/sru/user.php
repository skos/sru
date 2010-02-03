<?
/**
 * szablon beana uzytkownika
 */
class UFtpl_Sru_User
extends UFtpl_Common {

	static public $studyYears = array(
		1 => '1',
		2 => '2',
		3 => '3',
		4 => '4',
		5 => '5',
		6 => 'Doktoranckie 1',
		7 => 'Doktoranckie 2',
		8 => 'Doktoranckie 3',
		9 => 'Doktoranckie 4',
		10 => 'Uzupełniające 1',
		11 => 'Uzupełniające 2',
		0 => 'N/D',
	);

	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/tooShort' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/mismatch' => 'Hasła się różnią',
		'name' => 'Podaj imię',
		'name/regexp' => 'Imię zawiera niedozwolone znaki',
		'name/textMax' => 'Imię jest za długie',
		'surname' => 'Podaj nazwisko',
		'surnname/regexp' => 'Nazwisko zawiera niedozwolone znaki',
		'surname/textMax' => 'Nazwisko jest za długie',
		'email' => 'Podaj prawidłowy email',
		'gg' => 'Podaj prawidłowy numer',
		'gg/textMin' => 'Numer zbyt krótki',
		'facultyId' => 'Wybierz wydział',
		'studyYearId' => 'Wybierz rok studiów',
		'dormitory' => 'Wybierz akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'password3/invalid' => 'Podałeś nieprawidłowe hasło',
	);

	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'userLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formInfo(array $d) {
		echo '<p>'.$d['name'].' '.$d['surname'].'</p>';
	}

	public function formAdd(array $d, $dormitories, $faculties, $admin=false) {
		$form = UFra::factory('UFlib_Form', 'userAdd', $d, $this->errors);


		echo $form->_fieldset('Konto');
		echo $form->login('Login');
		echo $form->email('E-mail');
		echo '<p>Hasło zostanie przesłane na powyższy adres.</p>';
		echo $form->_end();

		echo $form->_fieldset('Dane osobowe');
		if ($this->_srv->get('msg')->get('userAdd/errors/walet/notFound')) {
			if ($admin) {
				echo $this->ERR('Użytkownik nie jest zameldowany w tym pokoju. '.$form->ignoreWalet('Zignoruj', array('type'=>$form->CHECKBOX)));
			} else {
				echo $this->ERR('Podana osoba nie jest zameldowana w tym pokoju');
			}
		}
		echo $form->name('Imię');
		echo $form->surname('Nazwisko');
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['0'] = 'N/D';
		echo $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears, '', ''),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->locationAlias('Pokój');
	}

	public function formEdit(array $d, $dormitories, $faculties) {
		$d['dormitory'] = $d['dormitoryId'];
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '0';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '0';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);


		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';
		if ($this->_srv->get('msg')->get('userEdit/errors/walet/notFound')) {
			echo $this->ERR('Użytkownik nie jest zameldowany w tym pokoju');
		}

		if ($this->_srv->get('msg')->get('userEdit/errors/ip/noFree')) {
			echo $this->ERR('Nie ma wolnych IP w tym DS-ie - skontaktuj się ze swoim administratorem lokalnym w godzinach dyżurów');
		}
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['0'] = 'N/D';
		echo $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo $form->gg('Gadu-Gadu');

		echo $form->_fieldset('Zmiana chronionych danych');
			echo $form->password3('Aktualne hasło', array('type'=>$form->PASSWORD));
			echo '<p>Do zmiany poniższych danych wymagane jest podanie aktualnego hasła.</p>';
			echo $form->email('E-mail');
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD ));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		echo $form->_end();
	}

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);

		echo $form->login('Login');
		echo $form->name('Imię');
		echo $form->surname('Nazwisko');
		echo $form->email('E-mail');
		echo $form->room('Pokój');
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');                                         
		$dorms->listAll();

		$tmp = array();
		foreach ($dorms as $dorm) {
			$tmp[$dorm['alias']] = $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
			));
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li>'.(!$c['active']?'<del>':'').'<a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a> <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function details(array $d) {
		$url = $this->url(0);
		$urlUser = $url.'/users/'.$d['id'];
		echo '<h1>'.$this->_escape($d['name']).' '.$this->_escape($d['surname']).'</h1>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		if ($d['gg']) {
			echo '<p><em>Gadu-Gadu:</em> <a href="gg:'.$d['gg'].'">'.$d['gg'].'</a></p>';
		}
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small></p>';
		echo '<p><em>Wydział:</em> '.(!is_null($d['facultyName'])?$d['facultyName']:'N/D').'</p>';
		echo '<p><em>Rok studiów:</em> '.self::$studyYears[$d['studyYearId']].'</p>';
		if ($d['banned']) {
			$bans = '<a href="'.$urlUser.'/penalties">'.$d['bans'].' <strong>(aktywne)</strong></a>';
		} elseif ($d['bans']>0) {
			$bans = '<a href="'.$urlUser.'/penalties">'.$d['bans'].'</a>';
		} else {
			$bans= '0';
		}
		echo '<p><em>Kary:</em> '.$bans.'</p>';
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedBy']).'</a>';;
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		echo '<div id="userMore">';
		echo '<p class="displayOnHover"><em>Znajdź na:</em>';
		echo ' <a href="http://www.google.pl/search?q='.urlencode($d['name'].' '.$d['surname']).'">google</a>';
		echo ' <a href="http://nasza-klasa.pl/search?query='.urlencode($d['name'].' '.$d['surname']).'">nasza-klasa</a>';
		echo ' <a href="http://wyczajka.net/p/'.urlencode($d['name'].'_'.$d['surname']).'">wyczajka</a>';
		echo '</p>';
		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		echo '</div>';
		echo '<p class="nav"><a href="'.$urlUser.'">Dane</a> ';
		$acl = $this->_srv->get('acl');
		if ($acl->sruAdmin('penalty', 'addForUser', $d['id'])) {
			echo '<a href="'. $url.'/penalties/:add/user:'.$d['id'].'">Ukarz</a> ';
		}
		echo '<a href="'.$urlUser.'/history">Historia profilu</a> <a href="'.$urlUser.'/servicehistory">Historia usług</a> <a href="'.$urlUser.'/:edit">Edycja</a> <span id="userMoreSwitch"></span></p>';
?><script type="text/javascript">
function changeVisibility() {
	var div = document.getElementById('userMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('userMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Szczegóły');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function titleEdit(array $d) {
		echo $this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function formEditAdmin(array $d, $dormitories, $faculties) {
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$d['changeComputersLocations'] = 1;
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '0';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '0';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		if ($this->_srv->get('msg')->get('userEdit/errors/walet/notFound')) {
			$pswd = false;
			try {
				$post = $this->_srv->get('req')->post->userEdit;
				if (!empty($post['password'])) {
					$pswd = true;
				}
			} catch (UFex $e) {
			}
			echo $this->ERR('Użytkownik nie jest zameldowany w tym pokoju.<br />'.$form->ignoreWalet('Zignoruj'.($pswd?' <strong>(ponownie wpisz hasło)</strong>':''), array('type'=>$form->CHECKBOX)));
		}
		if ($this->_srv->get('msg')->get('userEdit/errors/ip/noFreeAdmin')) {
			echo $this->ERR('Nie ma wolnych IP w tym DS-ie');
		}
		echo $form->login('Login');
		echo $form->name('Imię');
		echo $form->surname('Nazwisko');
		echo $form->email('E-mail');
		echo $form->gg('Gadu-Gadu');
		
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['0'] = 'N/D';
		echo $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo $form->changeComputersLocations('Zmień miejsce także wszystkim zarejestrowanym komputerom', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_fieldset('Zmiana hasła');
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD,  ));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		echo $form->_end();
		echo $form->active('Konto aktywne', array('type'=>$form->CHECKBOX));
	}

	public function userAddMailBody(array $d, $password) {
		echo 'Imię: '.$d['name']."\n";
		echo 'Nazwisko: '.$d['surname']."\n";
		echo $d['dormitoryName']."\n";
		echo 'Pokój: '.$d['locationAlias']."\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Twoje hasło to: '.$password."\n";
	}
	
	public function userAddMailBodyEnglish(array $d, $password) {
		echo 'Name: '.$d['name']."\n";
		echo 'Surname: '.$d['surname']."\n";
		echo $d['dormitoryName']."\n";
		echo 'Room: '.$d['locationAlias']."\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Your password: '.$password."\n";
	}


	public function shortList(array $d) {
		$url = $this->url(0).'/users/';
		foreach ($d as $c) {
			echo '<li>'.(!$c['active']?'<del>':'').'<a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function userBar(array $d) {
		echo '<a href="'. $this->url(0) .'/">Strona główna</a> | ';
		echo $this->_escape($d['name']) .' &quot;'. $this->_escape($d['login']) .'&quot; '. $this->_escape($d['surname']) . ' | ';
	}

	public function mailChange(array $d, $history = null) {
		if ($history instanceof UFbean_SruAdmin_UserHistoryList) {
			$history->write('mail', $d);
		} else {
			echo 'Imię: '.$d['name']."\n";
			echo 'Nazwisko: '.$d['surname']."\n";
			echo $d['dormitoryName']."\n";
			echo 'Pokój: '.$d['locationAlias']."\n";
			echo 'Login: '.$d['login']."\n";
			echo 'Numer GG: '.$d['gg']."\n";
		}
	}

	public function mailChangeEn(array $d, $history = null) {
		if ($history instanceof UFbean_SruAdmin_UserHistoryList) {
			$history->write('mailEn', $d);
		} else {
			echo 'Imię: '.$d['name']."\n";
			echo 'Nazwisko: '.$d['surname']."\n";
			echo $d['dormitoryName']."\n";
			echo 'Pokój: '.$d['locationAlias']."\n";
			echo 'Login: '.$d['login']."\n";
			echo 'Numer GG: '.$d['gg']."\n";
		}
	}

	public function stats(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$sum = 0;
		$woman = 0;
		$faculties = array();
		foreach ($d as $u) {
			if (in_array($u['name'], $conf->exclusions)) {
				continue;
			}
			if ($u['facultyName'] == '') {
				$u['facultyName'] = " N/D";
			}
			if(!array_key_exists($u['facultyName'], $faculties)) {
				$faculties[$u['facultyName']] = new PeopleCounter();
			}
			if (strtolower(substr($u['name'], -1)) == 'a') {
				$sum++;
				$woman++;
				$faculties[$u['facultyName']]->addUser(true);
			} else {
				$sum++;
				$faculties[$u['facultyName']]->addUser();
			}
		}
		echo '<div class="stats">';
		echo '<h3>Rozkład płci:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		echo '<tr><td>'.$sum.'</td><td>'.$woman.'</td><td>'.($sum - $woman).'</td></tr>';
		echo '</table>';
		$womanProc = round($woman/$sum,2);
		$manProc = round(($sum-$woman)/$sum,2);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$womanProc.','.$manProc.'&cht=p3&chl=Kobiety: '.($womanProc*100).'%|Mężczyźni: '.($manProc*100).'%" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład płci uwzględniając wydział:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Wydział</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		ksort($faculties);
		$chartDataWoman = '';
		$chartDataMan = '';
		$chartLabel = '';
		$chartLabelR = '';
		while ($fac = current($faculties)) {
			echo '<tr><td>'.key($faculties).'</td>';
			echo '<td>'.$fac->getUsers().'</td>';
			echo '<td>'.$fac->getWomen().'</td>';
			echo '<td>'.($fac->getUsers() - $fac->getWomen()).'</td></tr>';
			$chartDataWoman = $chartDataWoman.(round($fac->getWomen()/$fac->getUsers()*100)).',';
			$chartDataMan = $chartDataMan.(round(($fac->getUsers()-$fac->getWomen())/$fac->getUsers()*100)).',';
			$chartLabel = key($faculties).'|'.$chartLabel;
			$chartLabelR = (round($fac->getWomen()/$fac->getUsers()*100)).'% / '.(round(($fac->getUsers()-$fac->getWomen())/$fac->getUsers()*100)).'%|'.$chartLabelR;
			next($faculties);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x300&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartDataWoman.'|'.$chartDataMan.'&chxt=y,r&chxl=0:|'.$chartLabel.'1:|'.$chartLabelR.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład płci uwzględniając rok studiów:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Rok studiów</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		$years = array();
		foreach ($d as $u) {
			if(!array_key_exists(' '.$u['studyYearId'], $years)) {
				$years[' '.$u['studyYearId']] = new PeopleCounter();
			}
			if (substr($u['name'], -1) == 'a') {
				$years[' '.$u['studyYearId']]->addUser(true);
			} else {
				$years[' '.$u['studyYearId']]->addUser();
			}
		}
		ksort($years);
		$chartDataWoman = '';
		$chartDataMan = '';
		$chartLabel = '';
		$chartLabelR = '';
		while ($year = current ($years)) {
			echo '<tr><td>'.self::$studyYears[substr(key($years),1)].'</td>';
			echo '<td>'.$year->getUsers().'</td>';
			echo '<td>'.$year->getWomen().'</td>';
			echo '<td>'.($year->getUsers() - $year->getWomen()).'</td></tr>';
			$chartDataWoman = $chartDataWoman.(round($year->getWomen()/$year->getUsers()*100)).',';
			$chartDataMan = $chartDataMan.(round(($year->getUsers()-$year->getWomen())/$year->getUsers()*100)).',';
			$chartLabel = self::$studyYears[substr(key($years),1)].'|'.$chartLabel;
			$chartLabelR = (round($year->getWomen()/$year->getUsers()*100)).'% / '.(round(($year->getUsers()-$year->getWomen())/$year->getUsers()*100)).'%|'.$chartLabelR;
			next($years);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x350&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartDataWoman.'|'.$chartDataMan.'&chxt=y,r&chxl=0:|'.$chartLabel.'1:|'.$chartLabelR.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład płci uwzględniając kary:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Kary</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		$banned = array();
		$bansNumber = array();
		$bansNumber[0] = 0;
		$bannedSum = 0;
		$bannedWomanSum = 0;
		$activeBannedSum = 0;
		$activeBannedWomanSum = 0;
		$banSum = 0;
		$womanBanSum = 0;
		foreach ($d as $u) {
			if ($u['banned']) {
				$activeBannedSum++;
				if (substr($u['name'], -1) == 'a') {
					$activeBannedWomanSum++;
				}
			}
			$banSum += $u['bans'];
			if (substr($u['name'], strlen($u['name']) - 1, 1) == 'a') {
					$womanBanSum += $u['bans'];
			}
			if ($u['bans'] > 0) {
				$bannedSum++;
				if (substr($u['name'], strlen($u['name']) - 1, 1) == 'a') {
						$bannedWomanSum++;
				}
				$urlUser = $this->url(0).'/users/'.$u['id'];
				$keyString = '<a href="'.$urlUser.'">'.$u['name'].' "'.$u['login'].'" '.$u['surname'].'</a>';
				$banned[$keyString] = $u['bans'];
				if(!array_key_exists($u['bans'], $bansNumber)) {
					$bansNumber[$u['bans']] = 1;
				} else {
					$bansNumber[$u['bans']]++;
				}
			} else {
				$bansNumber[0]++;
			}
		}
		echo '<tr><td>Aktywne (użytkownicy)</td><td>'.$activeBannedSum.'</td><td>'.$activeBannedWomanSum.'</td><td>'.($activeBannedSum - $activeBannedWomanSum).'</td></tr>';
		echo '<tr><td>Suma (użytkownicy)</td><td>'.$bannedSum.'</td><td>'.$bannedWomanSum.'</td><td>'.($bannedSum - $bannedWomanSum).'</td></tr>';
		echo '<tr><td>Suma (kary)</td><td>'.$banSum.'</td><td>'.$womanBanSum.'</td><td>'.($banSum - $womanBanSum).'</td></tr>';
		echo '<tr><td>ŚREDNIO (kar/użytkownik)</td><td>'.round($banSum/$bannedSum,2).'</td><td>'.round($womanBanSum/$bannedSum,2).'</td><td>'.round(($banSum - $womanBanSum)/$bannedSum,2).'</td></tr>';
		echo '</table>';
		$womanActiveProc = round($activeBannedWomanSum/$activeBannedSum,2);
		$manActiveProc = round(($activeBannedSum - $activeBannedWomanSum)/$activeBannedSum,2);
		$womanSumProc = round($bannedWomanSum/$bannedSum,2);
		$manSumProc = round(($bannedSum-$bannedWomanSum)/$bannedSum,2);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x200&chd=t:'.$womanActiveProc.','.$manActiveProc.'|'.$womanSumProc.','.$manSumProc;
		echo '&cht=pc&chl=Aktywne dla kobiet: '.($womanActiveProc*100).'%|Aktywne dla mężczyzn: '.($manActiveProc*100).'%|Suma dla kobiet: ';
		echo ($womanSumProc*100).'%|Suma dla mężczyzn: '.($manSumProc*100).'%" alt=""/>';
		echo '</div>';

		echo '<h3>Top 10 ukaranych:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Login</th><th>Liczba kar</th></tr>';
		arsort($banned);
		$topBanSum = 0;
		$i = 0;
		$chartData = '';
		$chartLabel = '';
		while ($b = current ($banned)) {
			echo '<tr><td>'.key($banned).'</td><td>'.$b.'</td></tr>';
			$chartData = $chartData.$b.',';
			$chartLabel = $b.'|'.$chartLabel;
			$topBanSum = $topBanSum + $b;
			$i++;
			if ($i >= 10) {
				break;
			}
			next ($banned);
		}
		echo '</table>';
		reset($banned);
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=400x290&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($banned).'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład liczby kar:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Liczba kar</th><th>Liczba osób</th></tr>';
		$chartData = '';
		$chartLabel = '';
		ksort($bansNumber);

		while ($b = current ($bansNumber)) {
			echo '<tr><td>'.key($bansNumber).'</td><td>'.$b.'</td></tr>';
			$chartData = (round($b/$sum, 2)*100).','.$chartData;
			$chartLabel = key($bansNumber).' kar: '.(round($b/$sum, 2)*100).'%|'.$chartLabel;
			next ($bansNumber);
		}
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartData.'&cht=p3';
		echo '&chl='.$chartLabel.'%" alt=""/>';
		echo '</div>';
		echo '</div>';
	}

	function statsDorms(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$dormitories = array();
		$dormitoriesId = array();
		$banSum = 0;
		$activeBanSum = 0;
		foreach ($d as $u) {
			$u['dormitoryAlias'] = ' '.substr($u['dormitoryAlias'], 2);
			if(!array_key_exists($u['dormitoryAlias'], $dormitories)) {
				$dormitories[$u['dormitoryAlias']] = new ExtenededPeopleCounter();
			}
			if (in_array($u['name'], $conf->exclusions)) {
				continue;
			}
			if (strtolower(substr($u['name'], -1)) == 'a') {
				$dormitories[$u['dormitoryAlias']]->addUser(true);
			} else {
				$dormitories[$u['dormitoryAlias']]->addUser();
			}
			$dormitories[$u['dormitoryAlias']]->addToGroupFaculty($u['facultyName'], $u['name']);
			$dormitories[$u['dormitoryAlias']]->addToGroupYear($u['studyYearId'], $u['name']);
			$dormitories[$u['dormitoryAlias']]->addBans($u['bans']);
			$dormitories[$u['dormitoryAlias']]->addActiveBans($u['activeBans']);
			$banSum = $banSum + $u['bans'];
			$activeBanSum = $activeBanSum + $u['activeBans'];
		}
		echo '<div class="stats">';
		echo '<h3>Rozkład płci uwzględniając akademik:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Akademik</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		ksort($dormitories);
		$chartDataWoman = '';
		$chartDataMan = '';
		$chartLabel = '';
		$chartLabelR = '';
		while ($dorm = current($dormitories)) {
			echo '<tr><td><a href="'.$this->url(0).'/dormitories/ds'.substr(key($dormitories),1).'">DS'.key($dormitories).'</a></td>';
			echo '<td>'.$dorm->getUsers().'</td>';
			echo '<td>'.$dorm->getWomen().'</td>';
			echo '<td>'.($dorm->getUsers() - $dorm->getWomen()).'</td></tr>';
			if ($dorm->getUsers() == 0) {
				$sufixW = 0;
				$sufixM = 0;
			} else {
				$sufixW = round($dorm->getWomen()/$dorm->getUsers()*100);
				$sufixM = round(($dorm->getUsers()-$dorm->getWomen())/$dorm->getUsers()*100);
			}
			$chartDataWoman = $chartDataWoman.$sufixW.',';
			$chartDataMan = $chartDataMan.$sufixM.',';
			$chartLabel = 'DS'.key($dormitories).'|'.$chartLabel;
			$chartLabelR = $sufixW.'% / '.$sufixM.'%|'.$chartLabelR;
			next($dormitories);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x380&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartDataWoman.'|'.$chartDataMan.'&chxt=y,r&chxl=0:|'.$chartLabel.'1:|'.$chartLabelR.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład płci uwzględniając akademik i wydział:</h3>';
		reset($dormitories);
		while ($dorm = current($dormitories)) {
			echo '<h4>DS'.key($dormitories).'</h4>';
			echo '<table style="text-align: center; width: 100%;">';
			echo '<tr><th>Wydział</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
			$chartDataFac = '';
			$chartLabel = '';
			$faculties = $dorm->getGroupFaculty();
			while ($fac = current($faculties)) {
				echo '<tr><td>'.key($faculties).'</td>';
				echo '<td>'.$fac->getUsers().'</td>';
				echo '<td>'.$fac->getWomen().'</td>';
				echo '<td>'.($fac->getUsers() - $fac->getWomen()).'</td></tr>';
				$chartDataFac = (round($fac->getUsers()/$dorm->getUsers()*100)).','.$chartDataFac;
				$chartLabel = key($faculties).': '.round($fac->getUsers()/$dorm->getUsers()*100).'%|'.$chartLabel;
				next($faculties);
			}
			echo '</table>';
			$chartDataFac = substr($chartDataFac, 0, -1);
			echo '<div style="text-align: center;">';
			echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartDataFac;
			echo '&cht=p3&chl='.$chartLabel.' alt=""/>';
			echo '</div>';

			next($dormitories);
		}

		echo '<h3>Rozkład płci uwzględniając akademik i rok studiów:</h3>';
		reset($dormitories);
		while ($dorm = current($dormitories)) {
			echo '<h4>DS'.key($dormitories).'</h4>';
			echo '<table style="text-align: center; width: 100%;">';
			echo '<tr><th>Rok studiów</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
			$chartDataYear = '';
			$chartLabel = '';
			$years = $dorm->getGroupYear();
			while ($year = current($years)) {
				echo '<tr><td>'.self::$studyYears[substr(key($years),1)].'</td>';
				echo '<td>'.$year->getUsers().'</td>';
				echo '<td>'.$year->getWomen().'</td>';
				echo '<td>'.($year->getUsers() - $year->getWomen()).'</td></tr>';
				$chartDataYear = (round($year->getUsers()/$dorm->getUsers()*100)).','.$chartDataYear;
				$chartLabel = self::$studyYears[substr(key($years),1)].': '.round($year->getUsers()/$dorm->getUsers()*100).'%|'.$chartLabel;
				next($years);
			}
			echo '</table>';
			$chartDataYear = substr($chartDataYear, 0, -1);
			echo '<div style="text-align: center;">';
			echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartDataYear;
			echo '&cht=p3&chl='.$chartLabel.' alt=""/>';
			echo '</div>';

			next($dormitories);
		}

		echo '<h3>Rozkład kar uwzględniając akademik:</h3>';
		reset($dormitories);
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Akademik</th><th>Kar</th><th>Kar na mieszkańca</th></tr>';
		$chartData = '';
		$chartLabel = '';
		$avSum = 0;
		while ($dorm = current($dormitories)) {
			echo '<tr><td><a href="'.$this->url(0).'/dormitories/ds'.substr(key($dormitories),1).'">DS'.key($dormitories).'</a></td>';
			echo '<td>'.$dorm->getBans().'</td>';
			if ($dorm->getUsers() == 0) {
				$bansPerUser = 0;
			} else {
				$bansPerUser = round(($dorm->getBans()/$dorm->getUsers()),3);
			}
			echo '<td>'.$bansPerUser.'</td></tr>';
			$chartData = $dorm->getBans().','.$chartData;
			$chartLabel = 'DS'.key($dormitories).': '.round($dorm->getBans()/$banSum*100).'%|'.$chartLabel;
			next($dormitories);
			$avSum += $bansPerUser;
		}
		echo '<td>ŚREDNIO:</td><td>'.round(($banSum/count($dormitories)),1).'</td><td>'.round(($avSum/count($dormitories)),3).'</td>';
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.' alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład aktywnych kar uwzględniając akademik:</h3>';
		reset($dormitories);
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Akademik</th><th>Kar</th><th>Kar na mieszkańca</th></tr>';
		$chartData = '';
		$chartLabel = '';
		$avSum = 0;
		while ($dorm = current($dormitories)) {
			echo '<tr><td><a href="'.$this->url(0).'/dormitories/ds'.substr(key($dormitories),1).'">DS'.key($dormitories).'</a></td>';
			echo '<td>'.$dorm->getActiveBans().'</td>';
			if ($dorm->getUsers() == 0) {
				$bansPerUser = 0;
			} else {
				$bansPerUser = round(($dorm->getActiveBans()/$dorm->getUsers()),3);
			}
			echo '<td>'.$bansPerUser.'</td></tr>';
			$chartData = $dorm->getActiveBans().','.$chartData;
			$chartLabel = 'DS'.key($dormitories).': '.round($dorm->getActiveBans()/$activeBanSum*100).'%|'.$chartLabel;
			next($dormitories);
			$avSum += $bansPerUser;
		}
		echo '<td>ŚREDNIO:</td><td>'.round(($activeBanSum/count($dormitories)),1).'</td><td>'.round(($avSum/count($dormitories)),3).'</td>';
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.' alt=""/>';
		echo '</div>';
		echo '</div>';
	}
}

class PeopleCounter
{
	private $users = 0;
	private $women = 0;
	
	public function addUser($woman = false) {
		$this->users++;
		if ($woman) {
			$this->women++;
		}
	}

	public function getUsers() {
		return $this->users;
	}

	public function getWomen() {
		return $this->women;
	}
}

class ExtenededPeopleCounter
extends PeopleCounter
{
	private $groupFaculty = array();
	private $groupYear = array();
	private $bans = 0;
	private $activeBans = 0;

	public function addToGroupFaculty($key, $value) {
		if ($key == '') {
			$key = ' N/D';
		}
		if(!array_key_exists(' '.$key, $this->groupFaculty)) {
			$this->groupFaculty[' '.$key] = new PeopleCounter();
		}
		if (substr($value, -1) == 'a') {
			$this->groupFaculty[' '.$key]->addUser(true);
		} else {
			$this->groupFaculty[' '.$key]->addUser();
		}
	}

	public function addBans($value) {
		$this->bans = $this->bans + $value;
	}

	public function getBans() {
		return $this->bans;
	}

	public function addActiveBans($value) {
		$this->activeBans = $this->activeBans + $value;
	}

	public function getActiveBans() {
		return $this->activeBans;
	}

	public function addToGroupYear($key, $value) {
		if(!array_key_exists(' '.$key, $this->groupYear)) {
			$this->groupYear[' '.$key] = new PeopleCounter();
		}
		if (substr($value, -1) == 'a') {
			$this->groupYear[' '.$key]->addUser(true);
		} else {
			$this->groupYear[' '.$key]->addUser();
		}
	}

	public function getGroupFaculty() {
		ksort($this->groupFaculty);
		return $this->groupFaculty;
	}

	public function getGroupYear() {
		ksort($this->groupYear);
		return $this->groupYear;
	}
}