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
		-1 => '',
	);

	static public $languages = array(
		'pl' => 'polski',
		'en' => 'English',
	);

	protected $errors = array(
		'login' => 'Podaj login',
		'login/regexp' => 'Login zawiera niedozwolone znaki',
		'login/duplicated' => 'Login zajęty',
		'login/textMax' => 'Login jest za długi',
		'password' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/tooShort' => 'Hasło musi mieć co najmniej 6 znaków',
		'password/mismatch' => 'Hasła się różnią',
		'password/needNewOne' => 'Musisz zdefiniować nowe hasło',
		'name' => 'Podaj imię',
		'name/regexp' => 'Imię zawiera niedozwolone znaki',
		'name/textMax' => 'Imię jest za długie',
		'surname' => 'Podaj nazwisko',
		'surnname/regexp' => 'Nazwisko zawiera niedozwolone znaki',
		'surname/textMax' => 'Nazwisko jest za długie',
		'email' => 'Podaj prawidłowy email',
		'email/notnull' => 'Podaj prawidłowy email',
		'gg' => 'Podaj prawidłowy numer',
		'gg/textMin' => 'Numer zbyt krótki',
		'facultyId' => 'Wybierz wydział',
		'studyYearId' => 'Wybierz rok studiów',
		'dormitory' => 'Wybierz akademik',
		'dormitory/movedActive' => 'Przed zmianą DSu należy wymeldować mieszkańca',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'password3/invalid' => 'Podałeś nieprawidłowe hasło',
		'studyYearId/noFaculty' => 'Nieokreślony wydział',
		'registryNo/regexp' => 'Niepoprawny numer indeksu',
		'registryNo/101' => 'Niepoprawny numer indeksu',
		'registryNo/duplicated' => 'Nr indeksu przypisany do innego mieszkańca',
		'referralStart/active' => 'Zameldowany mieskzaniec musi mieć podaną datę początku skierowania',
		'referralStart/5' => 'Nieprawidłowa data początku skierowania',
		'referralStart/both' => 'Zameldowany mieszkaniec powinien mieć ustawioną tylko datę początku skierowania, zaś niezameldowany tylko datę końca skierowania.',
		'referralEnd/inactive' => 'Niezameldowany mieszkaniec musi mieć podaną datę końca skierowania',
		'referralEnd/5' => 'Nieprawidłowa data końca skierowania',
	);

	/*
	 * Szablon wyświetlania ostatnio modyfikowanych użytkowników
	 * 
	 */
	public function userLastModified(array $d){
		$url = $this->url(0);
		
		foreach($d as $c){
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			echo ' <small>zmodyfikował/dodał użytkownika: </small><a href="'.$url.'/users/'.$c['id'].'">';
			echo $this->_escape($c['name']).' "'.$c['login'].'" '.$this->_escape($c['surname']).'</a>';
			echo '</li>';
		}
	}
	
	public function formLogin(array $d) {
		$form = UFra::factory('UFlib_Form', 'userLogin', $d);

		echo $form->login('Login');
		echo $form->password('Hasło', array('type'=>$form->PASSWORD));
	}

	public function formInfo(array $d) {
		echo '<p>'.$d['name'].' '.$d['surname'].'</p>';
	}

	public function formAdd(array $d, $dormitories, $faculties, $surname, $registryNo) {
		$form = UFra::factory('UFlib_Form', 'userAdd', $d, $this->errors);

		echo $form->name('Imię', array('class'=>'required'));
		echo $form->surname('Nazwisko', array('class'=>'required', 'value'=>(is_null($surname) ? '' : $surname)));
		echo $form->registryNo('Nr indeksu', array('value'=>(is_null($registryNo) ? '' : $registryNo)));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['dormitoryAlias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['dormitoryAlias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['dormitoryId']] = $temp[1] . ' ' . $dorm['dormitoryName'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
			'class'=>'required',
		));
		echo $form->locationAlias('Pokój', array('class'=>'required'));
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
			'after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Wiadomości e-mail i GG będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails and gg messages in the chosen language." /><br/>',
		));
		$referralStart = date(self::TIME_YYMMDD, time());
		echo $form->referralStart('Początek skier.', array('value'=>$referralStart, 'class'=>'required'));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
?>
<script>
$("#main img[title]").tooltip({ position: "center right"});
</script>
<?
	}

	public function formEdit(array $d, $faculties) {
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '-1';
		}
		if (is_null($d['studyYearId']) || is_null($d['email'])) {
			$d['studyYearId'] = '-1';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);


		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';
		if ($d['updateNeeded']) {
			echo $this->ERR('Dane na Twoim koncie wymagają aktualizacji. Prosimy o wypełnienie prawidłowymi danymi wszystkich wymaganych pól (oznaczonych czerwoną obwódką). W celu ułatwienia kontaktu ze SKOS, możesz wypełnić także pola niewymagane.');
		}
		if (is_null($d['email'])) {
			echo $this->ERR('Twoje konto zostało dopiero założone. Wymagana jest zmiana hasła.');
		}
		$tmp = array();
		foreach ($faculties as $fac) {
			$tmp[$fac['id']] = $fac['name'];
		}
		
		$tmp['0'] = 'N/D';
		$tmp['-1'] = '';
		echo $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class'=>'required',
		));
		echo $form->studyYearId('Rok studiów', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$studyYears),
			'class'=>'required',
		));
		echo $form->gg('Gadu-Gadu', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Jeżeli podasz nr GG, będą na niego przesyłane informacje o zmianie statusu konta i Twoich komputerów." /><br/>'));
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
			'after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Wiadomości e-mail i GG będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails and gg messages in the chosen language." /><br/>',
		));

		echo $form->_fieldset('Zmiana chronionych danych');
		if (is_null($d['email'])) {
			echo $form->password3('Aktualne hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
			echo '<p>Do zmiany poniższych danych wymagane jest podanie aktualnego hasła.</p>';
			echo $form->email('E-mail', array('class'=>'required'));
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD, 'class'=>'required'));
		} else {
			echo $form->password3('Aktualne hasło', array('type'=>$form->PASSWORD));
			echo '<p>Do zmiany poniższych danych wymagane jest podanie aktualnego hasła.</p>';
			echo $form->email('E-mail', array('class'=>'required'));
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		}
		echo $form->_end();

?>
<script>
$("#main img[title]").tooltip({ position: "center right"});
</script>
<?
	}

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);

		echo $form->login('Login');
		echo $form->name('Imię');
		echo $form->surname('Nazwisko');
		echo $form->registryNo('Nr indeksu');
		echo $form->email('E-mail');
		echo $form->room('Pokój');
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');                                         
		$dorms->listAll();

		$tmp = array();
		foreach ($dorms as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['alias']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
			));
	}

	public function formSearchWalet(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);

		echo $form->surname('Nazwisko', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Nazwisko szukanego mieszkańca. Można podać łącznie z numerem indeksu." /><br/>'));
		echo $form->registryNo('Nr indeksu', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Numer indeksu szukanego mieszkańca. Można podać łącznie z nazwiskiem." /><br/>'));
?>
<script>
$("#main img[title]").tooltip({ position: "center right"});

	$(function() {
		$( "#userSearch_surname" ).autocomplete({
			source: function(req, resp) {
				$.getJSON("<? echo $this->url(0); ?>/users/quicksearch/" + encodeURIComponent(req.term), resp);
			},
			minLength: 3
		});
	});


</script>
<?
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li>'.(!$c['active']?'<del>':'').'<a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).' "'.$this->_escape($c['login']).'" '.$this->_escape($c['surname']).'</a> <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function searchResultsWalet(array $d) {
		$url = $this->url(0);
		$acl = $this->_srv->get('acl');

		echo '<div class="ips">';
		echo '<table><tr><td style="color: #000;">Mieszkaniec niezameldowany</td><td style="background: #cff; color: #000;">Mieszkaniec zameldowany w nadzorowanym DS</td><td style="background: #ccf; color: #000;">Mieszkaniec zameldowany w innym DS</td></tr></table>';
		echo '</div><br/>';

		echo '<table id="resultsT" style="width: 100%;"><thead><tr>';
		echo '<th>Imię</th>';
		echo '<th>Nazwisko</th>';
		echo '<th>Dom Studencki</th>';
		echo '<th>Pokój</th>';
		echo '<th>Nr indeksu</th>';
		echo '</tr></thead><tbody>';

		$usersMax = 0;
		$userCount = 0;
		$usersFree = 0;
		foreach ($d as $c) {
			if (!$c['active'] || is_null($c['referralStart']) || $c['referralStart'] == 0) {
				echo '<tr>';
			} else if ($acl->sruWalet('user', 'edit', $c['id'])) {
				echo '<tr style="background: #cff;">';
			} else {
				echo '<tr style="background: #ccf;">';
			}
			echo '<td><a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).'</a></td>';
			echo '<td><a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['surname']).'</a></td>';
			echo '<td><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.strtoupper($c['dormitoryAlias']).'</a></td>';
			echo '<td>'.$c['locationAlias'].'</td>';
			echo '<td>'.$c['registryNo'].'</td>';
		}
		echo '</tbody></table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#resultsT").tablesorter(); 
    } 
);
</script>
<?
	}

	public function details(array $d) {
		$url = $this->url(0);
		$urlUser = $url.'/users/'.$d['id'];
		echo '<h1>'.$this->_escape($d['name']).' '.$this->_escape($d['surname']).'</h1>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>Nr indeksu:</em> '.$d['registryNo'].'</p>';
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
		if (!is_null($d['registryNo'])) {
			echo '<p><em>Nr indeksu:</em> '.$d['registryNo'].'</p>';
		}
		if (!is_null($d['lastLoginAt']) && $d['lastLoginAt'] != 0 ) {
			echo '<p><em>Ost. logowanie:</em> '.date(self::TIME_YYMMDD_HHMM, $d['lastLoginAt']);
			if (!is_null($d['lastLoginIp'])) {
				echo '<small> ('.$d['lastLoginIp'].')</small>';
			}
			echo '</p>';
		}
		if (!is_null($d['referralStart']) && $d['referralStart'] != 0) {
			echo '<p><em>Początek skier.:</em> '.date(self::TIME_YYMMDD, $d['referralStart']).'</p>';
		}
		if (!is_null($d['referralEnd']) && $d['referralEnd'] != 0) {
			echo '<p><em>Koniec skier.:</em> '.date(self::TIME_YYMMDD, $d['referralEnd']).'</p>';
		}
		echo '<p><em>Język:</em> '.self::$languages[$d['lang']];
		echo '<p class="displayOnHover"><em>Znajdź na:</em>';
		echo ' <a href="http://www.google.pl/search?q='.urlencode($d['name'].' '.$d['surname']).'">google</a>';
		echo ' <a href="http://nasza-klasa.pl/search?query='.urlencode($d['name'].' '.$d['surname']).'">nasza-klasa</a>';
		echo ' <a href="http://wyczajka.net/p/'.urlencode($d['name'].'_'.$d['surname']).'">wyczajka</a>';
		echo ' <a href="http://www.facebook.com/#!/search/?q='.urlencode($d['name'].' '.$d['surname']).'">face-booczek</a>';
		echo '</p>';
		

		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		echo '</div>';
		echo '<p class="nav"><a href="'.$urlUser.'">Dane</a> ';
		echo ' &bull; <a href="'.$urlUser.'/computers/:add">Dodaj komputer</a> ';
		$acl = $this->_srv->get('acl');
		if ($acl->sruAdmin('penalty', 'addForUser', $d['id'])) {
			echo '&bull; <a href="'. $url.'/penalties/:add/user:'.$d['id'].'">Ukarz</a> ';
		}
		echo 	'&bull; <a href="'.$urlUser.'/history">Historia profilu</a>
		  	&bull; <a href="'.$urlUser.'/servicehistory">Historia usług</a>
		 	&bull; <a href="'.$urlUser.'/:edit">Edycja</a>
		  	&bull; <span id="userMoreSwitch"></span>'; 
	
		if (strlen($d['comment'])) echo ' <img src="'.UFURL_BASE.'/i/gwiazdka.png" />';
		echo '<p>';
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

	public function detailsWalet(array $d) {
		$url = $this->url(0);
		$acl = $this->_srv->get('acl');
		$urlUser = $url.'/users/'.$d['id'];

		echo '<h1>'.$this->_escape($d['name']).' '.$this->_escape($d['surname']).'</h1>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.strtoupper($d['dormitoryAlias']).'</a>, '.$d['locationAlias'].'</p>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>Nr indeksu:</em> '.$d['registryNo'].'</p>';
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Wydział:</em> '.(!is_null($d['facultyName'])?$d['facultyName']:'N/D').'</p>';
		echo '<p><em>Rok studiów:</em> '.self::$studyYears[$d['studyYearId']].'</p>';
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedBy']).'</a>';;
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		if (!is_null($d['referralStart']) && $d['referralStart'] != 0) {
			echo '<p><em>Początek skier.:</em> '.date(self::TIME_YYMMDD, $d['referralStart']).'</p>';
		}
		if (!is_null($d['referralEnd']) && $d['referralEnd'] != 0) {
			echo '<p><em>Koniec skier.:</em> '.date(self::TIME_YYMMDD, $d['referralEnd']).'</p>';
		}
		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		echo '<p class="nav"><a href="'.$urlUser.'">Dane</a> ';
		echo 	'&bull; <a href="'.$urlUser.'/history">Historia profilu</a>';
		if ($acl->sruWalet('user', 'edit', $d['id'])) {
			echo ' &bull; <a href="'.$urlUser.'/:edit">Edycja</a>';
		}
	}

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function titleEdit(array $d) {
		echo $this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function formEditAdmin(array $d, $faculties) {
		$d['locationId'] = $d['locationAlias'];
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = '0';
		}
		if (is_null($d['studyYearId'])) {
			$d['studyYearId'] = '0';
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		if ($this->_srv->get('msg')->get('userEdit/errors/ip/noFreeAdmin')) {
			echo $this->ERR('Nie ma wolnych IP w tym DS-ie');
		}
		echo $form->login('Login');
		echo $form->email('E-mail');
		echo $form->gg('Gadu-Gadu');
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
		));
		
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
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_fieldset('Zmiana hasła');
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD,  ));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		echo $form->_end();
	}

	public function formEditWalet(array $d, $dormitories) {
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$post = $this->_srv->get('req')->post;

		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		echo $form->name('Imię', array('class'=>'required'));
		echo $form->surname('Nazwisko', array('class'=>'required'));
		echo $form->registryNo('Nr indeksu');
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['dormitoryAlias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['dormitoryAlias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['dormitoryId']] = $temp[1] . ' ' . $dorm['dormitoryName'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class'=>'required',
		));
		echo $form->locationAlias('Pokój', array('class'=>'required'));
		
		try {
			$referralStart = $post->userEdit['referralStart'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralStart = $d['referralStart'];
			if (!is_null($d['referralStart']) && ($d['referralStart'] == 0 || $d['active'] == false)) {
				$referralStart = date(self::TIME_YYMMDD, time());
			} else if (!is_null($d['referralStart'])) {
				$referralStart = date(self::TIME_YYMMDD, $d['referralStart']);
			}
		}
		try {
			$referralEnd = $post->userEdit['referralEnd'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralEnd = '';
		}
		echo $form->referralStart('Początek skier.', array('value'=>$referralStart));
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
			'after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Wiadomości e-mail i GG będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails and gg messages in the chosen language." /><br/>',
		));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		if ($d['active'] && !$this->_srv->get('msg')->get('userEdit/errors/referralEnd')) {
			echo '<p><a href="#" onclick="return changeUnregisterVisibility();">Wyrejestruj</a></p>';
			echo '<div id="unregisterMore" style="display: none;">';
		}
		echo $form->referralEnd('Koniec skier.', array('value'=>$referralEnd));
		echo $form->active('Konto aktywne', array('type'=>$form->CHECKBOX));
		if ($d['active'] && !$this->_srv->get('msg')->get('userEdit/errors/referralEnd')) {
			echo '</div>';
		}

?><script type="text/javascript">
var rsOld = document.getElementById('userEdit_referralStart').value;
function changeUnregisterVisibility() {
	var um = document.getElementById('unregisterMore');
	if (um.style.display == 'none') {
		um.style.display = 'block';
		var re = document.getElementById('userEdit_referralEnd');
		re.value = "<? echo date(self::TIME_YYMMDD, time()); ?>";
		var rs = document.getElementById('userEdit_referralStart');
		rs.value = "";
		var ac = document.getElementById('userEdit_active');
		ac.checked = false;
	} else {
		um.style.display = 'none';
		var re = document.getElementById('userEdit_referralEnd');
		re.value = "";
		var rs = document.getElementById('userEdit_referralStart');
		rs.value = rsOld;
		var ac = document.getElementById('userEdit_active');
		ac.checked = true;
	}
}
</script>
<script>
$("#main img[title]").tooltip({ position: "center right"});
</script><?
	}

	public function shortList(array $d) {
		$url = $this->url(0).'/users/';
		foreach ($d as $c) {
			echo '<li>'.(!$c['active']?'<del>':'').'<a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function userBar(array $d, $ip, $time) {
		echo 'Witaj, '.$this->_escape($d['name']) .' &quot;'. $this->_escape($d['login']) .'&quot; '. $this->_escape($d['surname']) . '!<br/>';
		
		if (!is_null($time) && $time != 0 ) {
			echo '<small>Ostatnie&nbsp;logowanie: '.date(self::TIME_YYMMDD_HHMM, $time).'</small>' ;
		}
		if (!is_null($ip)) {
			echo '<small>, z IP: '.$ip.'</small><br/>' ;
		}
	}

	public function userAddMailTitlePolish(array $d) {
		echo 'Witamy w sieci SKOS';
	}

	public function userAddMailTitleEnglish(array $d) {
		echo 'Welcome in SKOS network';
	}

	public function userAddMailBodyPolish(array $d, $password) {
		echo 'Witamy w Sieci Komputerowej Osiedla Studenckiego Politechniki Gdańskiej!'."\n";
		echo "\n";
		echo 'Jeżeli otrzymałeś/aś tę wiadomość, a nie chciałeś/aś założyć konta w SKOS PG,'."\n";
		echo 'prosimy o zignorowanie tej wiadomości.'."\n\n";
		echo 'Aby dokończyć proces aktywacji konta, zgłoś się do swojego administratora'."\n";
		echo 'lokalnego z wejściówką do DS-u. Godziny, w których możesz go zastać znajdziesz'."\n";
		echo 'tutaj: http://skos.ds.pg.gda.pl/'."\n";
		echo "\n";
		echo 'W razie jakichkolwiek problemów zachęcamy do skorzystania z FAQ:'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Dane, na które zostało założone konto:'."\n";
		echo 'Imię: '.$d['name']."\n";
		echo 'Nazwisko: '.$d['surname']."\n";
		echo $d['dormitoryName']."\n";
		echo 'Pokój: '.$d['locationAlias']."\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Twoje hasło to: '.$password."\n";
		echo "\n";
		echo 'System Rejestracji Użytkowników: http://sru.ds.pg.gda.pl/'."\n";
		echo 'PROSIMY O ZMIANĘ HASŁA ZARAZ PO PIERWSZYM ZALOGOWANIU SIĘ!'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Nasza sieć obejmuje swoim zasięgiem sieci LAN wszystkich Domów Studenckich'."\n";
		echo 'Politechniki Gdańskiej, jest częścią Uczelnianej Sieci Komputerowej (USK PG) i'."\n";
		echo 'dołączona jest bezpośrednio do sieci TASK.'."\n";
		echo "\n";
		echo 'Wszelkie informacje na temat funkcjonowania sieci, godzin dyżurów'."\n";
		echo 'administratorów SKOS PG oraz Regulamin SKOS PG znajdziesz na stronie'."\n";
		echo 'http://skos.ds.pg.gda.pl/ , zaś bieżące komunikaty na grupie dyskusyjnej ds.siec.komunikaty'."\n";
	}
	
	public function userAddMailBodyEnglish(array $d, $password) {
		echo 'Welcome in Gdańsk University of Technology Students’ Campus Computer Network (polish acronym - SKOS PG)!' . "\n";
		echo "\n";
		echo 'If you received this message but you didn’t want to create an account in SKOS PG, please ignore it.' . "\n";
		echo 'To finish activation procedure you must go to your local administrator in his duty hours:'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo 'with your dormitory card.'."\n";
		echo "\n";
		echo 'If you have problems using Internet in our network, please refer to FAQ:'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Account was created for:'."\n";
		echo 'Name: '.$d['name']."\n";
		echo 'Surname: '.$d['surname']."\n";
		echo $d['dormitoryName']."\n";
		echo 'Room: '.$d['locationAlias']."\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Your password: '.$password."\n";
		echo "\n";
		echo 'Users’ Registration System (System Rejestracji Użytkowników): http://sru.ds.pg.gda.pl/'."\n";
		echo 'PLEASE CHANGE YOUR PASSWORD AFTER THE FIRST LOGON!'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Any information about our network you can find on our page'."\n";
		echo 'http://skos.ds.pg.gda.pl/'."\n";
	}

	public function userRecoverPasswordMailTitlePolish(array $d) {
		echo 'Zmiana hasła';
	}

	public function userRecoverPasswordMailTitleEnglish(array $d) {
		echo 'Password recovery';
	}

	public function userRecoverPasswordMailBodyTokenPolish(array $d, $token, $host) {
		echo 'Kliknij poniższy link, aby zmienić hasło do Twojego konta w SRU'."\n";
		echo '(Systemie Rejestracji Użytkowników):'."\n";
		echo 'http://'.$host.$this->url(0).'/'.$token->token."\n\n";
		echo 'Otrzymasz KOLEJNY e-mail zawierający nowe hasło do SRU.'."\n\n";
	}

	public function userRecoverPasswordMailBodyTokenEnglish(array $d, $token, $host) {
		echo 'Follow this link to change your password in Users’ Registration System:'."\n";
		echo 'http://'.$host.$this->url(0).'/'.$token->token."\n\n";
		echo 'You will receive THE NEXT e-mail with the new password.'."\n\n";
	}

	public function userRecoverPasswordMailBodyPasswordPolish(array $d, $password, $host) {
		echo 'Twój login: '.$d['login']."\n";
		echo 'Twoje nowe hasło: '.$password."\n\n";
		echo 'System Rejestracji Użytkowników: http://'.$host.'/'."\n";
		echo 'PROSIMY O ZMIANĘ HASŁA ZARAZ PO PIERWSZYM ZALOGOWANIU!'."\n\n";
	}

	public function userRecoverPasswordMailBodyPasswordEnglish(array $d, $password, $host) {
		echo 'Your login: '.$d['login']."\n";
		echo 'Your new password: '.$password."\n\n";
		echo 'Users’ Registration System: http://'.$host.'/'."\n";
		echo 'PLEASE CHANGE YOUR PASSWORD JUST AFTER THE FIRS LOG IN!'."\n\n";
	}

	public function dataChangedMailTitlePolish(array $d) {
		echo 'Twoje dane zostały zmienione';
	}

	public function dataChangedMailTitleEnglish(array $d) {
		echo 'Your personal data have been changed';
	}
	
	public function dataChangedMailBodyPolish(array $d) {
		echo 'Potwierdzamy, że zmiana Twoich danych w SKOS PG została zapisana.'."\n\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Imię: '.$d['name']."\n";
		echo 'Nazwisko: '.$d['surname']."\n";
		echo 'E-mail: '.$d['email']."\n";
		echo 'Gadu-Gadu: '.$d['gg']."\n";
		echo 'Wydział: '.$d['facultyName']."\n";
		echo 'Rok studiów: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
		echo $d['dormitoryName']."\n";
		echo 'Pokój: '.$d['locationAlias']."\n";
	}

	public function dataChangedMailBodyEnglish(array $d) {
		echo 'We comfirm, that change of your personal data in SKOS PG has been saved.'."\n\n";
		echo 'Login: '.$d['login']."\n";
		echo 'Name: '.$d['name']."\n";
		echo 'Surname: '.$d['surname']."\n";
		echo 'E-mail: '.$d['email']."\n";
		echo 'Gadu-Gadu: '.$d['gg']."\n";
		echo 'Faculty: '.$d['facultyName']."\n";
		echo 'Year of study: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
		echo $d['dormitoryName']."\n";
		echo 'Room: '.$d['locationAlias']."\n";
	}

	public function dataAdminChangedMailBodyPolish(array $d, $history = null) {
		echo 'Informujemy, że Twoje dane w SKOS PG uległy zmianie.'."\n\n";
		if ($history instanceof UFbean_SruAdmin_UserHistoryList) {
			$history->write('mail', $d);
		} else {
			echo 'Login: '.$d['login']."\n";
			echo 'Imię: '.$d['name']."\n";
			echo 'Nazwisko: '.$d['surname']."\n";
			echo 'E-mail: '.$d['email']."\n";
			echo 'Gadu-Gadu: '.$d['gg']."\n";
			echo 'Wydział: '.$d['facultyName']."\n";
			echo 'Rok studiów: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
			echo $d['dormitoryName']."\n";
			echo 'Pokój: '.$d['locationAlias']."\n";
		}
	}

	public function dataAdminChangedMailBodyEnglish(array $d, $history = null) {
		echo 'We inform, that your personal data in SKOS PG has been changed.'."\n\n";
		if ($history instanceof UFbean_SruAdmin_UserHistoryList) {
			$history->write('mailEn', $d);
		} else {
			echo 'Login: '.$d['login']."\n";
			echo 'Name: '.$d['name']."\n";
			echo 'Surname: '.$d['surname']."\n";
			echo 'E-mail: '.$d['email']."\n";
			echo 'Gadu-Gadu: '.$d['gg']."\n";
			echo 'Faculty: '.$d['facultyName']."\n";
			echo 'Year of study: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
			echo $d['dormitoryName']."\n";
			echo 'Room: '.$d['locationAlias']."\n";
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
		$chartDataFac = '';
		$chartLabelFac = '';
		while ($fac = current($faculties)) {
			echo '<tr><td>'.key($faculties).'</td>';
			echo '<td>'.$fac->getUsers().'</td>';
			echo '<td>'.$fac->getWomen().'</td>';
			echo '<td>'.($fac->getUsers() - $fac->getWomen()).'</td></tr>';
			$chartDataWoman = $chartDataWoman.(round($fac->getWomen()/$fac->getUsers()*100)).',';
			$chartDataMan = $chartDataMan.(round(($fac->getUsers()-$fac->getWomen())/$fac->getUsers()*100)).',';
			$chartLabel = key($faculties).'|'.$chartLabel;
			$chartLabelR = (round($fac->getWomen()/$fac->getUsers()*100)).'% / '.(round(($fac->getUsers()-$fac->getWomen())/$fac->getUsers()*100)).'%|'.$chartLabelR;
			$chartDataFac = (round($fac->getUsers()/$sum*100)).','.$chartDataFac;
			$chartLabelFac = key($faculties).': '.round($fac->getUsers()/$sum*100).'%|'.$chartLabelFac;
			next($faculties);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		$chartDataFac = substr($chartDataFac, 0, -1);

		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartDataFac;
		echo '&cht=p3&chl='.$chartLabelFac.' alt=""/>';

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
			if (strtolower(substr($u['name'], -1)) == 'a') {
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
		$chartDataYear = '';
		$chartLabelYear = '';
		while ($year = current ($years)) {
			echo '<tr><td>'.self::$studyYears[substr(key($years),1)].'</td>';
			echo '<td>'.$year->getUsers().'</td>';
			echo '<td>'.$year->getWomen().'</td>';
			echo '<td>'.($year->getUsers() - $year->getWomen()).'</td></tr>';
			$chartDataWoman = $chartDataWoman.(round($year->getWomen()/$year->getUsers()*100)).',';
			$chartDataMan = $chartDataMan.(round(($year->getUsers()-$year->getWomen())/$year->getUsers()*100)).',';
			$chartLabel = self::$studyYears[substr(key($years),1)].'|'.$chartLabel;
			$chartLabelR = (round($year->getWomen()/$year->getUsers()*100)).'% / '.(round(($year->getUsers()-$year->getWomen())/$year->getUsers()*100)).'%|'.$chartLabelR;
			$chartDataYear = (round($year->getUsers()/$sum*100)).','.$chartDataYear;
			$chartLabelYear = self::$studyYears[substr(key($years),1)].': '.round($year->getUsers()/$sum*100).'%|'.$chartLabelYear;
			next($years);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		$chartDataYear = substr($chartDataYear, 0, -1);

		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartDataYear;
		echo '&cht=p3&chl='.$chartLabelYear.' alt=""/>';
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
				if (strtolower(substr($u['name'], -1)) == 'a') {
					$activeBannedWomanSum++;
				}
			}
			$banSum += $u['bans'];
			if (strtolower(substr($u['name'], - 1)) == 'a') {
					$womanBanSum += $u['bans'];
			}
			if ($u['bans'] > 0) {
				$bannedSum++;
				if (strtolower(substr($u['name'], -1)) == 'a') {
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
		echo '<tr><td>ŚREDNIO (kar/użytkownik)</td><td>'.round($banSum/$bannedSum,2).'</td><td>'.round($womanBanSum/$bannedWomanSum,2).'</td><td>'.round(($banSum - $womanBanSum)/($bannedSum - $bannedWomanSum),2).'</td></tr>';
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
			if (substr($u['dormitoryAlias'], 0, 2) != 'ds') {
				$u['dormitoryAlias'] = ' '.strtoupper($u['dormitoryAlias']);
			} else {
				$u['dormitoryAlias'] = ' '.substr($u['dormitoryAlias'], 2);
			}
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
			echo '<tr><td>'.$this->displayDormUrl(substr(key($dormitories),1)).'</td>';
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
			if (is_numeric(substr(key($dormitories), 1, 1))) {
				$chartLabel = 'DS'.key($dormitories).'|'.$chartLabel;
			} else {
				$chartLabel = key($dormitories).'|'.$chartLabel;
			}
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
			echo '<h4>'.$this->displayDormUrl(substr(key($dormitories),1)).'</h4>';
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
			echo '<h4>'.$this->displayDormUrl(substr(key($dormitories),1)).'</h4>';
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
			echo '<tr><td>'.$this->displayDormUrl(substr(key($dormitories),1)).'</td>';
			echo '<td>'.$dorm->getBans().'</td>';
			if ($dorm->getUsers() == 0) {
				$bansPerUser = 0;
			} else {
				$bansPerUser = round(($dorm->getBans()/$dorm->getUsers()),3);
			}
			echo '<td>'.$bansPerUser.'</td></tr>';
			$chartData = $dorm->getBans().','.$chartData;
			if (is_numeric(substr(key($dormitories), 1, 1))) {
				$chartLabel = 'DS'.key($dormitories).': '.round($dorm->getBans()/$banSum*100).'%|'.$chartLabel;
			} else {
				$chartLabel = key($dormitories).': '.round($dorm->getBans()/$banSum*100).'%|'.$chartLabel;
			}
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
			echo '<tr><td>'.$this->displayDormUrl(substr(key($dormitories),1)).'</td>';
			echo '<td>'.$dorm->getActiveBans().'</td>';
			if ($dorm->getUsers() == 0) {
				$bansPerUser = 0;
			} else {
				$bansPerUser = round(($dorm->getActiveBans()/$dorm->getUsers()),3);
			}
			echo '<td>'.$bansPerUser.'</td></tr>';
			$chartData = $dorm->getActiveBans().','.$chartData;
			if (is_numeric(substr(key($dormitories), 1, 1))) {
				$chartLabel = 'DS'.key($dormitories).': '.round($dorm->getActiveBans()/$activeBanSum*100).'%|'.$chartLabel;
			} else {
				$chartLabel = key($dormitories).': '.round($dorm->getActiveBans()/$activeBanSum*100).'%|'.$chartLabel;
			}
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

	private function displayDormUrl($dorm) {
		if (is_numeric(substr($dorm, 0, 1))) {
			return '<a href="'.$this->url(0).'/dormitories/ds'.$dorm.'">DS '.$dorm.'</a>';
		} else {
			return '<a href="'.$this->url(0).'/dormitories/'.strtolower($dorm).'">'.$dorm.'</a>';
		}
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
		if (strtolower(substr($value, -1)) == 'a') {
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
		if (strtolower(substr($value, -1)) == 'a') {
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
