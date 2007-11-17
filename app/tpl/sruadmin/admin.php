<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Admin
extends UFtpl {

	//@todo te nazwy tak maja byc?
	protected $adminTypes = array(
		1 => 'Administrator Centralny',
		2 => 'Administrator Osiedlowy',
		3 => 'Administrator Lokalny',
		4 => 'BOT',
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
	);	
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		$form->login('Login');
		$form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$d['name'].'</p>';
	}
	
	public function listAdmin(array $d) {
		$url = $this->url(0).'/admins/';
		
		$lastDom = '';
		
		//@todo: dziala ok jesli dormitoryId jest NOT NULL, hrynek?
			
		foreach ($d as $c)
		{
			if($lastDom != $c['dormitoryId'])
			{
				if($lastDom != '')		
					echo '</ul>';
				if (is_null($c['dormitoryName'])) {
					echo '<h3>Spoza akademików</h3>';
				} else {
					echo '<h3>'.$c['dormitoryName'].'</h3>';
				}
				echo '<ul>';		
			}
			
			if ($c['active']) {
				echo '<li><a href="'.$url.$c['id'].'">';
			} else {
				echo '<li><a class="inactive" href="'.$url.$c['id'].'">';  //@todo dac .inactive do styla
			}
			switch($c['typeId'])
			{
				case 1:
						echo '<strong>'.$c['name'].'</strong>';
						break;
				case 2:
						echo '<em>'.$c['name'].'</em>';
						break;						
				case 3:
						echo $c['name'];
						break;							
			}

			echo '</a></li>';
			
			$lastDom = $c['dormitoryId'];
			
		}
		echo '</ul>';
	}
	public function titleDetails(array $d) {
		echo $d['name'];
	}	
	public function details(array $d) {
		$url = $this->url(0);
		echo '<h2>'.$d['name'].'<br/><small>('.$this->adminTypes[$d['typeId']].')</small></h2>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Telefon:</em> '.$d['phone'].'</p>';
		echo '<p><em>Gadu-Gadu:</em> '.$d['gg'].'</p>';
		echo '<p><em>Jabber:</em> '.$d['jid'].'</p>';
		echo '<p><em>Adres:</em> '.$d['address'].'</p>';							
	}	

	public function titleAdd(array $d) {
		echo 'Dodanie nowego administratora';
	}		
	public function formAdd(array $d, $dormitories) {
		if (!isset($d['typeId'])) {
			$d['typeId'] = 3;
		}
		$form = UFra::factory('UFlib_Form', 'adminAdd', $d, $this->errors);

		$form->_fieldset();
		$form->login('Login');
		//@todo: a skoro konta z haslem wklepuje admin, to moze lepiej by bylo gdyby ono bylo generowane i szlo na maila?
		$form->password('Hasło', array('type'=>$form->PASSWORD));
		$form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		$form->name('Nazwa'); //@todo nazwac jakos rozsadnie:P
		$form->typeId('Uprawnienia', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($this->adminTypes),
		));
		$form->email('E-mail');
		$form->phone('Telefon');
		$form->gg('GG');
		$form->jid('Jabber');
		$form->address('Adres');

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		$tmp['-'] = 'N/D';
		$form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));		
	}
/*
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
