<?
/**
 * szablon beana uzytkownika
 */
class UFtpl_Sru_User
extends UFtpl {

	protected $studyYears = array(
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
		'login/duplicated' => 'Login zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/mismatch' => 'Hasła się różnią',
		'name' => 'Podaj imię',
		'name/textMax' => 'Imię jest za długie',
		'surname' => 'Podaj nazwisko',
		'surname/textMax' => 'Nazwisko jest za długie',
		'email' => 'Podaj prawidłowy email',
		'facultyId' => 'Wybierz wydział',
		'studyYearId' => 'Wybierz rok studiów',
		'dormitory' => 'Wybierz akademik',
		'locationId' => 'Podaj pokój',
		'locationId/noDormitory' => 'Wybierz akademik',
		'locationId/noRoom' => 'Pokój nie istnieje',

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
		$form->password('Hasło', array('type'=>$form->PASSWORD));
		$form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
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
			'labels' => $form->_labelize($this->studyYears, '', ''),
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
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);


		$form->_fieldset();
		$form->email('E-mail');
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
			'labels' => $form->_labelize($this->studyYears, '', ''),
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
}
