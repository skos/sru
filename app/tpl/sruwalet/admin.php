<?
/**
 * template admina Waleta
 */
class UFtpl_SruWalet_Admin
extends UFtpl_Common {

	protected $adminTypes = array(
		UFacl_SruWalet_Admin::DORM 	=> 'Kierownik DS',
		UFacl_SruWalet_Admin::HEAD 	=> 'Kierownik OS',
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

		echo '<table id="adminsT" style="width: 100%;"><thead><tr>';
		echo '<th>Administrator</th>';
		echo '<th>Ostatnie logowanie</th>';
		echo '</tr></thead><tbody>';

		foreach ($d as $c) {
			echo '<tr><td style="border-top: 1px solid;"><a href="'.$url.$c['id'].'">';
			switch($c['typeId']) {
				case 12:
						echo '<strong>'.$this->_escape($c['name']).'</strong></a>';
						break;
				case 11:
						echo $this->_escape($c['name']).'</a>';
						break;
			}
			echo '</td><td style="border-top: 1px solid;">'.($c['lastLoginAt'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $c['lastLoginAt'])).'</td></tr>';
		}
		echo '</tbody></table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#adminsT").tablesorter(); 
    } 
);
</script>
<?
	}

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']);
	}

	public function details(array $d, $dormList) {
		$url = $this->url(0);
		echo '<h2>'.$this->_escape($d['name']).'<br/><small>('.$this->adminTypes[$d['typeId']].' &bull; ostatnie logowanie: '.date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt']).')</small></h2>';

		echo '<p><em>Domy studenckie:</em>';
		if (is_null($dormList)) {
			echo ' brak przypisanych DS</p>';
		} else {
			echo '</p><ul>';
			foreach ($dormList as $dorm) {
				echo '<li><a href="'.$url.'/dormitories/'.$dorm['dormitoryAlias'].'">'.$dorm['dormitoryName'].'</a></li>';
			}
			echo '</ul>';
		}

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

		echo $form->_end();

		echo $form->_fieldset('Domy studenckie');
		foreach ($dormitories as $dorm) {
			echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dorm]['.$dorm['id'].']', 'id'=>'adminEdit[dorm]['.$dorm['id'].']'));
		}
		echo $form->_end();

		echo $form->_fieldset();
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres');
	}

	public function formEdit(array $d, $dormitories, $dormList, $advanced=false) {
		$form = UFra::factory('UFlib_Form', 'adminEdit', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->name('Nazwa'); 
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
		echo $form->password2('Powtórz hasło', array('type'=>$form->PASSWORD));

		if($advanced) {
			echo $form->typeId('Uprawnienia', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($this->adminTypes),
			));	
			echo $form->active('Aktywny', array('type'=>$form->CHECKBOX) );
		}

		echo $form->_end();

		if($advanced) {
			echo $form->_fieldset('Domy studenckie');
			foreach ($dormitories as $dorm) {
				$permission = 0;
				if (!is_null($dormList)) {
					foreach ($dormList as $perm) {
						if ($perm['dormitory'] == $dorm['id']) {
							$permission = 1;
							break;
						}
					}
				}
				echo $form->dormPerm($dorm['name'], array('type'=>$form->CHECKBOX, 'name'=>'adminEdit[dorm]['.$dorm['id'].']', 'id'=>'adminEdit[dorm]['.$dorm['id'].']', 'value'=>$permission));
			}
			echo $form->_end();
		}
		
		echo $form->_fieldset();
		echo $form->email('E-mail');
		echo $form->phone('Telefon');
		echo $form->gg('GG');
		echo $form->jid('Jabber');
		echo $form->address('Adres');
	}

	public function waletBar(array $d, $ip, $time) {
		echo '<a href="'.$this->url(0).'/admins/'.$d['id'].'">'.$this->_escape($d['name']).'</a> ';
		if (!is_null($time) && $time != 0 ) {
			echo 'Ostatnie&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $time).' ' ;
		}
		if (!is_null($ip)) {
			echo '('.$ip.') ';
		}
	}
}
