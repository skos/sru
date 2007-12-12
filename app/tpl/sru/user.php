<?
/**
 * szablon beana uzytkownika
 */
class UFtpl_Sru_User
extends UFtpl {

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
		'-' => 'N/D',
	);

	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
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
		'locationId' => 'Podaj pokój',
		'locationId/noDormitory' => 'Wybierz akademik',
		'locationId/noRoom' => 'Pokój nie istnieje',
		'password3/invalid' => 'Podałeś nieprawidłowe hasło',

	);

	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'userLogin', $d);

		$form->login('Login');
		$form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$d['name'].' '.$d['surname'].'</p>';
	}

	public function formAdd(array $d, $dormitories, $faculties) {
		$form = UFra::factory('UFlib_Form', 'userAdd', $d, $this->errors);


		$form->_fieldset('Konto');
		$form->login('Login');
		$form->password('Wybierz hasło', array('type'=>$form->PASSWORD));
		$form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		$form->email('E-mail');
		$form->_end();

		$form->_fieldset('Dane osobowe');
		$form->name('Imię');
		$form->surname('Nazwisko');
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['-'] = 'N/D';
		$form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		$form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears, '', ''),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		$form->locationId('Pokój');
	}

	public function formEdit(array $d, $dormitories, $faculties) {
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '-';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '-';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);


		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['-'] = 'N/D';
		$form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		$form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		$form->locationId('Pokój');
		$form->_fieldset('Zmiana chronionych danych');
			$form->password3('Aktualne hasło', array('type'=>$form->PASSWORD));
			echo '<p>Do zmiany poniższych danych wymagane jest podanie aktualnego hasła.</p>';
			$form->email('E-mail');
			$form->password('Nowe hasło', array('type'=>$form->PASSWORD ));
			$form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		$form->_end();
	}

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);

		$form->login('Login');
		$form->name('Imię');
		$form->surname('Nazwisko');
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li><a href="'.$url.'/users/'.$c['id'].'">'.$c['name'].' '.$c['surname'].'</a> <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span></li>';
		}
	}

	public function details(array $d) {
		$url = $this->url(0);
		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';
		echo '<p><em>Login:</em> '.$d['login'].'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small></p>';
		echo '<p><em>Wydział:</em> '.$d['facultyName'].'</p>';
		echo '<p><em>Rok studiów:</em> '.self::$studyYears[$d['studyYearId']].'</p>';
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$d['modifiedBy'].'</a>';;
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		if (strlen($d['comment'])) {
			echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
	}

	public function titleDetails(array $d) {
		echo $d['name'].' '.$d['surname'].' ('.$d['login'].')';
	}

	public function titleEdit(array $d) {
		echo $d['name'].' '.$d['surname'].' ('.$d['login'].')';
	}

	public function formEditAdmin(array $d, $dormitories, $faculties) {
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$d['changeComputersLocations'] = 1;
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '-';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '-';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		$form->login('Login');
		$form->email('E-mail');
		$form->name('Imię');
		$form->surname('Nazwisko');
		
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['-'] = 'N/D';
		$form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		$form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		$form->locationId('Pokój');
		$form->changeComputersLocations('Zmień miesce także wszystkim zarejestrowanym komputerom', array('type'=>$form->CHECKBOX));
		$form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		$form->_fieldset('Zmiana hasła');
			$form->password('Nowe hasło', array('type'=>$form->PASSWORD,  ));
			$form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		$form->_end();
	}
}
