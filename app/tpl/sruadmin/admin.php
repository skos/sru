<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Admin
extends UFtpl_Common {

	protected $adminTypes = array(
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
	);	
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'adminLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formLogout(array $d) {
		echo '<p>'.$this->_escape($d['name']).'</p>';
	}
	
	public function listAdmin(array $d) {
		$url = $this->url(0).'/admins/';
		
		$lastDom = '-';
					
		foreach ($d as $c)
		{
			if($lastDom != $c['dormitoryId'])
			{
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
			switch($c['typeId'])
			{
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
			echo '<li><a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).'</a></li>';
		}
		echo '</ul>';
	}	
	public function titleDetails(array $d) {
		echo $this->_escape($d['name']);
	}	
	public function details(array $d) {
		$url = $this->url(0);
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$this->adminTypes[$d['typeId']].' &bull; ostatnie logowanie: '.date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt']).')</small></h2>';
		echo '<p><em>Login:</em> '.$d['login'].'</p>';
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

		echo $form->_fieldset();
		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));
		echo $form->name('Nazwa'); 
		echo $form->typeId('Uprawnienia', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($this->adminTypes),
		));
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres');

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

	public function formEdit(array $d, $dormitories, $advanced=false) {

		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		echo $form->_fieldset();
		
		echo $form->name('Nazwa'); 
		
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));

		if($advanced)
		{
			echo $form->typeId('Uprawnienia', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($this->adminTypes),
			));	
			echo $form->active('Aktywny', array('type'=>$form->CHECKBOX) );

		}

		echo $form->_end();
		
		echo $form->_fieldset();
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres');

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
}
