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

	public function formLogout(array $d) {
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
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li><a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a> <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span></li>';
		}
	}

	public function details(array $d) {
		$url = $this->url(0);
		echo '<h1>'.$this->_escape($d['name']).' '.$this->_escape($d['surname']).'</h1>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small></p>';
		echo '<p><em>Wydział:</em> '.(!is_null($d['facultyName'])?$d['facultyName']:'N/D').'</p>';
		echo '<p><em>Rok studiów:</em> '.self::$studyYears[$d['studyYearId']].'</p>';
		if ($d['banned']) {
			$bans = '<a href="'.$url.'/users/'.$d['id'].'/bans">'.$d['bans'].' <strong>(aktywne)</strong></a>';
		} elseif ($d['bans']>0) {
			$bans = '<a href="'.$url.'/users/'.$d['id'].'/bans">'.$d['bans'].'</a>';
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
		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
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
			echo $this->ERR('Użytkownik nie jest zameldowany w tym pokoju. '.$form->ignoreWalet('Zignoruj', array('type'=>$form->CHECKBOX)));
		}
		echo $form->login('Login');
		echo $form->email('E-mail');
		echo $form->name('Imię');
		echo $form->surname('Nazwisko');
		
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

	public function shortList(array $d) {
		$url = $this->url(0).'/users/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a></li>';
		}
	}

	public function userBar(array $d) {
		echo '<a href="'. $this->url(0) .'/">Strona główna</a> | ';
		echo $this->_escape($d['name']) .' &quot;'. $this->_escape($d['login']) .'&quot; '. $this->_escape($d['surname']) . ' | ';
	}
}
