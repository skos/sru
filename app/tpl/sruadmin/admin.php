<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Admin
extends UFtpl {

	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		$form->login('Login');
		$form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$d['name'].'</p>';
	}

	/*
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
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '-';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '-';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);


		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';
		$form->email('E-mail');
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
			'labels' => $form->_labelize($this->studyYears),
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
	}
	*/
}
