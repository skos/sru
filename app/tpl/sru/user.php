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
		null => '',
	);

	static public $languages = array(
		'pl' => 'polski',
		'en' => 'English',
	);

	static public $documentTypes = array(
		'0' => 'Dowód osobisty',
		'1' => 'Paszport',
		'2' => 'Inny',
		'3' => 'Brak (dziecko)',
	);
        
        static public $documentTypesShort = array( 
                '0' => 'D.O.', 
         	'1' => 'Pa.', 
                '2' => 'In.', 
        ); 

	protected static $userTypesForWaletAcademic = array(
		1 => 'Student studiów dziennych',
		2 => 'Doktorant',
		3 => 'Erasmus',
		4 => 'Student - obcokrajowiec',
		5 => 'Student studiów wieczorowych/zaocznych',
		6 => 'Student innej uczelni',
		7 => 'Nie student',
	);

	protected static $userTypesForWaletSummer = array(
		21 => 'Student-turysta',
		22 => 'Dydaktyka',
		23 => 'Turysta indywidualny',
	);

	protected static $userTypesForAdmin = array(
		51 => 'SKOS',
		52 => 'Administracja',
		53 => 'Organizacja',
		54 => 'Ex-admin',
	);

	public static $userTypesLimit = 50; // do wyszukiwania

	protected static $userTypesForSearch = array(
		0 => '',
	);

	protected static $userTypesForHistory = array(
		0 => 'nieznany',
	);

	protected static $userSummaryTypes = array(
		UFbean_Sru_User::DB_STUDENT_MAX => 'STUDENCI',
		UFbean_Sru_User::DB_TOURIST_MAX => 'TURYŚCI',
	);

	public static function getUserType($typeId) {
		$userTypes = self::$userTypesForWaletAcademic + self::$userTypesForWaletSummer + self::$userTypesForAdmin + self::$userTypesForHistory;
		return $userTypes[$typeId];
	}

	public static function getUserTypes() {
		$userTypes = self::$userTypesForSearch + self::$userTypesForAdmin + self::$userSummaryTypes + self::$userTypesForWaletAcademic + self::$userTypesForWaletSummer;
		return $userTypes;
	}

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
		'surname/regexp' => 'Nazwisko zawiera niedozwolone znaki',
		'surname/textMax' => 'Nazwisko jest za długie',
		'email' => 'Podaj prawidłowy email',
		'email/notnull' => 'Podaj prawidłowy email',
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
		'referralStart/5' => 'Nieprawidłowa data początku skierowania',
		'referralEnd/5' => 'Nieprawidłowa data końca skierowania',
		'referralEnd/tooOld' => 'Data końca skierowania musi być nowsza niż data początku skierowania',
		'lastLocationChange/5' => 'Nieprawidłowa data',
		'lastLocationChangeActive/invalid' => 'Nieprawidłowa data',
		'address/noAddress' => 'Podaj adres',
		'documentType/noDocumentType' => 'Podaj typ dokumentu tożsamości',
		'documentNumber/noDocumentNumber' => 'Podaj numer dokumentu tożsamości',
		'nationalityName/textMin' => 'Podaj narodowość',
		'sex' => 'Podaj płeć',
		'registryNo/noRegistryNo' => 'Podaj nr indeksu',
		'pesel/noPesel' => 'Podaj nr PESEL',
		'pesel/invalid' => 'Niepoprawny nr PESEL',
		'pesel/duplicated' => 'PESEL przypisany do innego mieszkańca',
		'typeId/noTypeId' => 'Określ typ',
		'birthDate/105' => 'Nieprawidłowy format daty',
		'message/notEmpty' => 'Wiadomość nie może być pusta',
	);

	/*
	 * Szablon wyświetlania ostatnio modyfikowanych użytkowników
	 * 
	 */
	public function userLastModified(array $d){
		$url = $this->url(0);
		
		echo '<ul>';
		foreach($d as $c){
			if ($c['banned'] == true) {
				echo '<li class="ban">';
			} else {
				echo '<li>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedat']);
			echo ' <small>zmodyfikował/dodał użytkownika: </small>';
			if($c['active'] == true){
				echo '<a href="'.$url.'/users/'.$c['id'].'">' . $this->_escape($c['name']);
				echo ' "'.$c['login'].'" '.$this->_escape($c['surname']).'</a>';
			}else{
				echo '<del><a href="'.$url.'/users/'.$c['id'].'">' . $this->_escape($c['name']);
				echo ' "'.$c['login'].'" '.$this->_escape($c['surname']).'</a></del>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function formLogin(array $d) {
                $form = UFra::factory('UFlib_Form', 'userLogin', $d);

                echo $form->login(_("Login"));
                echo $form->password(_("Hasło"), array('type' => $form->PASSWORD));
        }

	public function formInfo(array $d) {
		echo '<p>'.$d['name'].' '.$d['surname'].'</p>';
	}

	public function formAddAdmin(array $d, $dormitories) {
		$form = UFra::factory('UFlib_Form', 'userAdd', $d, $this->errors);

		echo $form->login('Login', array('class'=>'required'));
		echo $form->email('E-mail', array('class'=>'required'));
		echo $form->name('Imię', array('class'=>'required'));
		echo $form->surname('Nazwisko', array('class'=>'required'));
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$userTypesForAdmin),
		));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['dormitoryAlias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['dormitoryAlias'];
			}
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
			'after'=>UFlib_Helper::displayHint("Wiadomości e-mail będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails in the chosen language."),
		));
		$referralStart = date(self::TIME_YYMMDD, time());
		echo $form->commentSkos('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function formAddWalet(array $d, $dormitories, $faculties, $surname, $registryNo, $pesel) {
		$form = UFra::factory('UFlib_Form', 'userAdd', $d, $this->errors);
		$conf = UFra::shared('UFconf_Sru');
		$post = $this->_srv->get('req')->post;

		echo $form->name('Imię', array('class'=>'required'));
		try {
			$surname = $post->userAdd['surname'];
		} catch (UFex_Core_DataNotFound $e) {
			//
		}
		echo $form->surname('Nazwisko', array('class'=>'required', 'value'=>$surname));
		echo $form->sex('Płeć', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(array("Mężczyzna", "Kobieta"), '', ''),
			'class' => 'required',
		));

		$tmp = array();
		if (in_array('1', $conf->userTypesToRegister)) {
			$tmp = $tmp + array('----------Rok akademicki----------') + self::$userTypesForWaletAcademic;
		}
		if (in_array('2', $conf->userTypesToRegister)) {
			$tmp = $tmp + array(20 => '----------Wakacje----------') + self::$userTypesForWaletSummer;
		}
		echo $form->typeId('Typ mieszkańca', array(
			'type'=>$form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class' => 'required',
			'id' => 'userTypeSelector'
		));

		try {
			$registryNo = $post->userAdd['registryNo'];
		} catch (UFex_Core_DataNotFound $e) {
			//
		}
		echo $form->registryNo('Nr indeksu', array('value'=>$registryNo,
                    'after'=>'<span id="registryNoCheckResult"></span><br/>'));
		$tmp = array();
		foreach ($faculties as $fac) {
			if ($fac['id'] == 0) continue; // N/D powinno być na końcu
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['0'] = 'N/D';
		echo $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
			'class'=>'required',
			'id' => 'facultySelector'
		));
		echo $form->address('Adres', array('class'=>'necessary address', 
			'type'=>$form->TEXTAREA, 
			'rows'=>3,
			'after'=>UFlib_Helper::displayHint(nl2br("Format: \n kod pocztowy miejscowość \n ulica nr domu/nr mieszkania"))
		));
		
		echo $form->documentType('Typ dokumentu', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$documentTypes),
			'class'=>'necessary',
			'id' => 'documentTypeSelector'
		));

		echo $form->documentNumber('Numer dokumentu', array('class'=>'necessary'));
		echo $form->nationalityName('Narodowość', array('class'=>'necessary',
			'after'=>UFlib_Helper::displayHint("Np. &quot;polska&quot;, &quot;niemiecka&quot;, &quot;angielska&quot;")));
		try {
			$pesel = $post->userAdd['pesel'];
		} catch (UFex_Core_DataNotFound $e) {
			//
		}
		echo $form->pesel('PESEL', array('value'=>$pesel,
			'after'=>'<span id="peselValidationResult"></span><br/>'));

		echo $form->birthDate('Data urodzenia', array('type' => $form->CALENDER,'after'=>UFlib_Helper::displayHint("Data w formacie RRRR-MM-DD, np. 1988-10-06")));
		echo $form->birthPlace('Miejsce urodzenia');
		echo $form->userPhoneNumber('Nr telefonu mieszkańca');
		echo $form->guardianPhoneNumber('Nr telefonu opiekuna');

		echo '<legend>Zamieszkanie</legend>';
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['dormitoryAlias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['dormitoryAlias'];
			}
			$tmp[$dorm['dormitoryId']] = $temp[1] . ' ' . $dorm['dormitoryName'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
			'class'=>'required',
		));
		echo $form->locationAlias('Pokój', array('class'=>'required'));
		echo $form->overLimit('Dokwaterowany', array('type'=>$form->CHECKBOX));
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
			'after'=>UFlib_Helper::displayHint("Wiadomości e-mail będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails in the chosen language."),
		));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo '<legend>Meldunek</legend>';
		try {
			$referralStart = $post->userAdd['referralStart'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralStart = date(self::TIME_YYMMDD, time());
		}
		echo $form->referralStart('Początek skierowania', array(
			'type' => $form->CALENDER,
			'value'=>$referralStart,
			'class'=>'required',
			'after'=>UFlib_Helper::displayHint("Data początku pobytu."),
		));
		try {
			$referralEnd = $post->userAdd['referralEnd'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralEnd = $conf->usersAvailableTo;
		}
		echo $form->referralEnd('Koniec skierowania', array(
			'type' => $form->CALENDER,
			'value'=>$referralEnd,
			'class'=>'required',
			'after'=>UFlib_Helper::displayHint("Data końca pobytu."),
		));
                
?><script type="text/javascript">
$('#userTypeSelector').change(function(){
	var userTypeSelector = $('#userTypeSelector');
	if(userTypeSelector.val() == 23){
		$('#facultySelector').val(0);
	}
});
(function (){
	var typeId = document.getElementById('userTypeSelector');
	function registryChangeClass(){
		var registryNo = document.getElementById('userAdd_registryNo');
		if(typeId.value == 1 || typeId.value == 2 || typeId.value == 5) {
			registryNo.setAttribute('class', 'required');
		} else {
			registryNo.setAttribute('class', '');
		}
	}
	typeId.onchange = registryChangeClass;
	
	var documentTypeId = document.getElementById('documentTypeSelector');
	function documentNumberChangeClass(){
		var documentNo = document.getElementById('userAdd_documentNumber');
		if(documentTypeId.value != <?=UFbean_Sru_User::DOC_TYPE_NONE?>) {
			documentNo.setAttribute('class', 'necessary');
		} else {
			documentNo.setAttribute('class', '');
		}
	}
	documentTypeId .onchange = documentNumberChangeClass;

	var nationality = document.getElementById('userAdd_nationalityName');
	function peselChangeClass(){
		var pesel = document.getElementById('userAdd_pesel');
		if(nationality.value.toLowerCase() == '<?=UFbean_Sru_User::NATIONALITY_PL?>'){
			pesel.setAttribute('class', 'necessary');
		}else{
			pesel.setAttribute('class', '');
		}
	}
	nationality.onchange = peselChangeClass;
	
	var name = document.getElementById('userAdd_name');
	function changeSex() {
		var sex = document.getElementById('userAdd_sex');
		if (name.value.slice(-1) == 'a') {
			sex.value = 1;
		} else {
			sex.value = 0;
		}
	}
	name.onchange = changeSex;

	var pesel = document.getElementById('userAdd_pesel');
	var birthDate = document.getElementById('userAdd_birthDate');
	function validatePesel() {
		if (pesel.value == '') {
			document.getElementById('peselValidationResult').innerHTML = '';
			return;
		}
		if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else { // code for IE6, IE5
			xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText == 'false') {
					document.getElementById('peselValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
				} else {
					birthDate.value = xmlhttp.responseText.replace(/"/g, '');
					document.getElementById('peselValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
				}
			}
		}
		xmlhttp.open('GET',"<? echo $this->url(0); ?>/users/validatepesel/" + encodeURIComponent(pesel.value), true);
		xmlhttp.send();
	}
	pesel.onchange = validatePesel;
	if (birthDate.value == '') {
		window.onload = validatePesel;
	}
        
        var registryNo = document.getElementById('userAdd_registryNo');
        var registryNoErr = document.getElementById('userAdd_registryNoErr');
	function registryNoCheck() {
                if(registryNoErr) {
                        registryNoErr.innerHTML = '';
                        registryNoErr.setAttribute('class', '');
                }
                
                if (registryNo.value == '') {
                        document.getElementById('registryNoCheckResult').innerHTML = '';
                }
                
                if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                        xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                        xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
                }
                xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                if (xmlhttp.responseText == 'ok') {
                                        document.getElementById('registryNoCheckResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
                                } else if (xmlhttp.responseText == 'invalid') {
                                        document.getElementById('registryNoCheckResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
                                } else {
                                        document.getElementById('registryNoCheckResult').innerHTML = xmlhttp.responseText;
                                }
                        }
                }
                xmlhttp.open('GET',"<? echo $this->url(0); ?>/users/checkregistryno/"+ encodeURIComponent(registryNo.value), true);
                xmlhttp.send();                
        }
	
        registryNo.onchange = registryNoCheck;
        window.onload = registryNoCheck;
	window.onload = registryChangeClass;
                
        function delRegistryNoError() {
                if(registryNoErr.innerHTML == 'Nr indeksu przypisany do innego mieszkańca') {
                        registryNoErr.innerHTML = '';
                        registryNoErr.setAttribute('class', '');
                        registryNoCheck();
                }
        }
        
        window.onload = delRegistryNoError;

})()
$(function() {
	$( "#userAdd_nationalityName" ).autocomplete({
		source: function(req, resp) {
			$.getJSON("<? echo $this->url(0); ?>/users/quickcountrysearch/" + encodeURIComponent(req.term), resp);
		},
		minLength: 1
	});
});
</script><?
	}

	public function formEdit(array $d, $faculties) {
		$acl = $this->_srv->get('acl');
		
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = null;
		}
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);

		echo '<h1>'.$d['name'].' '.$d['surname'].'</h1>';

                if ($d['updateNeeded']) {
                        echo $this->ERR(_("Dane na Twoim koncie wymagają aktualizacji. Prosimy o wypełnienie prawidłowymi danymi wszystkich wymaganych pól (oznaczonych czerwoną obwódką). W celu ułatwienia kontaktu ze SKOS, możesz wypełnić także pola niewymagane."));
                }
                if ($d['changePasswordNeeded'] && !is_null($d['email'])) {
                        echo $this->ERR(_("Twoje hasło ze względów bezpieczeństwa musi zostać zmienione."));
                }
                if (is_null($d['email'])) {
                        echo $this->ERR(_("Twoje konto zostało dopiero założone. Wymagana jest zmiana hasła."));
                }
                echo '<p><label>' . _("Login:") . '</label><span class="userData"> ' . $d['login'] . '</span>' . UFlib_Helper::displayHint(_("Istnieje możliwość zmiany loginu u Administratora SKOS.")) . '</p>';
                if (!is_null($d['typeId']) && $d['typeId'] != '') {
                        echo '<p><label>' . _("Typ konta:") . '</label><span class="userData"> ' . _(self::getUserType($d['typeId'])) . '</span></p>';
                }
                if (!is_null($d['locationAlias']) && $d['locationAlias'] != '') {
                        echo '<p><label>' . _("Zameldowanie:") . '</label><span class="userData"> ' .  (($d['lang']=='pl')?$d['dormitoryName']:$d['dormitoryNameEn']) . _(', pok. ') . $d['locationAlias'] . '</span></p>';
                }
                if ($acl->sru('user', 'viewPersonalData')) {
                        if (!is_null($d['address']) && $d['address'] != '') {
                                echo '<p><label>' . _("Adres:") . '</label><span class="userData"> ' . nl2br($this->_escape($d['address'])) . '</span></p>';
                        }
                        if (!is_null($d['documentType']) && self::$documentTypes[$d['documentType']] != '' && !is_null($d['documentNumber']) && $d['documentNumber'] != '') {
                                echo '<p><label>' . _("Typ dokumentu:") . '</label><span class="userData"> ' . _(self::$documentTypes[$d['documentType']]) . '</span></p>';
                                echo '<p><label>' . _("Nr dokumentu:") . '</label><span class="userData"> ' . nl2br($this->_escape($d['documentNumber'])) . '</span></p>';
                        }
                        if (!is_null($d['nationality']) && $d['nationality'] != '') {
                                echo '<p><label>' . _("Narodowość:") . '</label><span class="userData"> ' . nl2br($this->_escape(_($d['nationalityName']))) . '</span></p>';
                        }
                        if (!is_null($d['pesel']) && $d['pesel'] != '') {
                                echo '<p><label>' . _("PESEL:") . '</label><span class="userData">' . $d['pesel'] . '</span></p>';
                        }
                        if (!is_null($d['birthDate']) && $d['birthDate'] != '') {
                                echo '<p><label>' . _("Data urodzenia:") . '</label><span class="userData"> ' . date(self::TIME_YYMMDD, $d['birthDate']) . '</span></p>';
                        }
                        if (!is_null($d['birthPlace']) && $d['birthPlace'] != '') {
                                echo '<p><label>' . _("Miejsce urodzenia:") . '</label><span class="userData"> ' . nl2br($this->_escape($d['birthPlace'])) . '</span></p>';
                        }
                        if (!is_null($d['userPhoneNumber']) && $d['userPhoneNumber'] != '') {
                                echo '<p><label>' . _("Tel. mieszkańca:") . '</label><span class="userData"> ' . nl2br($this->_escape($d['userPhoneNumber'])) . '</span></p>';
                        }
                        if (!is_null($d['guardianPhoneNumber']) && $d['guardianPhoneNumber'] != '') {
                                echo '<p><label>' . _("Tel. opiekuna:") . '</label><span class="userData"> ' . nl2br($this->_escape($d['guardianPhoneNumber'])) . '</span></p>';
                        }
                        if (!is_null($d['sex']) && $d['sex'] != '') {
                                echo '<p><label>' . _("Płeć:") . '</label><span class="userData"> ' . (!$d['sex'] ? 'Mężczyzna' : 'Kobieta') . '</span></p>';
                        }
                } else {
                        echo $this->ERR(_("Łączysz się przez niezabezpieczone połączenie - ze względów bezpieczeństwa Twoje dane osobowe nie są wyświetlane."));
		}
                echo $form->lang(_("Język"), array(
                        'type' => $form->SELECT,
                        'labels' => $form->_labelize(self::$languages),
                        'after' => UFlib_Helper::displayHint(_("Wiadomości e-mail będa przychodziły w wybranym języku.")),
                ));
                echo '<br />';

                if ($d['typeId'] != UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL && $d['typeId'] <= UFbean_Sru_User::DB_TOURIST_MAX) {
                        echo $form->_fieldset(_("Dane dotyczące studiów"));

                        if (!is_null($d['registryNo']) && $d['registryNo'] != '' && $acl->sru('user', 'viewPersonalData')) {
                                echo '<p><label>' . _("Nr indeksu:") . '</label><span class="userData"> ' . $d['registryNo'] . '</span></p>';
                        }
                        if (!is_null($d['facultyId'])) {
                                $tmp = array();
                                foreach ($faculties as $fac) {
                                        if ($fac['id'] == 0) continue; // N/D powinno być na końcu
                                        $tmp[$fac['id']] = $fac['name'];
                                }

                                $tmp['0'] = 'N/D';
                                echo '<p><label>' . _("Wydział:") . '</label><span class="userData"> ' . _($tmp[$d['facultyId']]) . '</span></p>';

                                if ($d['facultyId'] != $tmp['0']) {
                                        echo $form->studyYearId(_("Rok studiów"), array(
                                                'type' => $form->SELECT,
                                                'labels' => $form->_labelize(self::$studyYears),
                                                'class' => 'required',
                                        ));
                                }
                        }
                }

                echo '<br/>';
                echo $form->_fieldset(_("Zmiana chronionych danych - konieczne podanie aktualnego hasła"));
                if (is_null($d['email']) || $d['changePasswordNeeded']) {
                        echo $form->password3(_("Aktualne hasło"), array('type' => $form->PASSWORD, 'class' => 'required'));
                        echo $form->email(_("E-mail"), array('class' => 'required'));
                        echo $form->password(_("Nowe hasło"), array('type' => $form->PASSWORD, 'class' => 'required'));
                        echo $form->password2(_("Potwierdź hasło"), array('type' => $form->PASSWORD, 'class' => 'required'));
                } else {
                        echo $form->password3(_("Aktualne hasło"), array('type' => $form->PASSWORD));
                        echo $form->email(_("E-mail"), array('class' => 'required'));
                        echo $form->password(_("Nowe hasło"), array('type' => $form->PASSWORD));
                        echo $form->password2(_("Potwierdź hasło"), array('type' => $form->PASSWORD));
                }
                echo $form->_end();
        }

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);
		$cookieDisplay = UFlib_Request::getCookie('SRUDisplayUsers');

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
			}
			$tmp[$dorm['alias']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::getUserTypes()),
		));
		
		$url = explode('/', $this->url());
		if(!in_array('active:1', $url) && in_array('search', $url)) {
			echo $form->active('Tylko aktywni', array(
				'type' => $form->CHECKBOX,
				'checked' => false
			));
		} 
		else if(in_array('active:1', $url) && in_array('search', $url)) {
			echo $form->active('Tylko aktywni', array(
				'type' => $form->CHECKBOX,
				'checked' => false
			));
		} 
		else if($cookieDisplay == '1' || $cookieDisplay === false) {
			echo $form->active('Tylko aktywni', array(
				'type' => $form->CHECKBOX,
				'checked' => true,
			));
		} else {
			echo $form->active('Tylko aktywni', array(
				'type' => $form->CHECKBOX,
				'checked' => false,
			));
		}
	}

	public function formSearchWalet(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'userSearch', $d, $this->errors);

		echo $form->surname('Nazwisko', array('after'=>UFlib_Helper::displayHint("Nazwisko szukanego mieszkańca. Można łączyć z pozostałymi polami wyszukiwania.")));
		echo $form->registryNo('Nr indeksu', array('after'=>UFlib_Helper::displayHint("Numer indeksu szukanego mieszkańca. Można łączyć z pozostałymi polami wyszukiwania.")));
		echo $form->pesel('PESEL', array('after'=>UFlib_Helper::displayHint("Numer PESEL szukanego mieszkańca. Można łączyć z pozostałymi polami wyszukiwania.")));
?>
<script type="text/javascript">
	$(function() {
		$( "#userSearch_surname" ).autocomplete({
			source: function(req, resp) {
				$.getJSON("<? echo $this->url(0); ?>/users/quicksearch/" + encodeURIComponent(req.term), resp);
			},
			minLength: 2
		});
	});


</script>
<?
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			echo '<li'.($c['banned']?' class="ban"':'').'>'.(!$c['active']?'<del>':'').'<a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).' "'.$this->_escape($c['login']).'" '.$this->_escape($c['surname']).'</a>'.(strlen($c['commentSkos']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['commentSkos'].'" />':'').' <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span>'.(!$c['active']?'</del>':'').(strlen($c['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['locationComment'].'" />':'').'</li>';
		}
	}

	public function searchResultsWalet(array $d) {
		$url = $this->url(0);
		$acl = $this->_srv->get('acl');

		echo '<div class="legend">';
		echo '<table><tr><td class="unregistered">Mieszkaniec niezameldowany</td><td class="registeredOwn">Mieszkaniec zameldowany w nadzorowanym DS</td><td class="registeredOther">Mieszkaniec zameldowany w innym DS</td></tr></table>';
		echo '</div><br/>';

		echo '<table id="resultsT" class="bordered"><thead><tr>';
		echo '<th>Imię</th>';
		echo '<th>Nazwisko</th>';
		echo '<th>Dom Studencki</th>';
		echo '<th>Pokój</th>';
		echo '<th>Nr indeksu</th>';
		echo '<th>Edycja</th>';
		echo '<th>Wymeld.</th>';
		echo '</tr></thead><tbody>';

		foreach ($d as $c) {
			$canEdit = $acl->sruWalet('user', 'edit', $c['id']);
			$canDel = $acl->sruWalet('user', 'del', $c['id']);
			if ($canDel) {
				echo '<tr class="registeredOwn">';
			} else if ($canEdit) {
				echo '<tr>';
			} else {
				echo '<tr class="registeredOther">';
			}
			echo '<td>'.($acl->sruWalet('user','view',$c['id']) ? '<a href="'.$url.'/users/'.$c['id'].'">' : '').$this->_escape($c['name']).($acl->sruWalet('user','view',$c['id']) ? '</a>' : '').'</td>';
			echo '<td>'.($acl->sruWalet('user','view',$c['id']) ? '<a href="'.$url.'/users/'.$c['id'].'">' : '').$this->_escape($c['surname']).($acl->sruWalet('user','view',$c['id']) ? '</a>' : '').'</td>';
			echo '<td><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.strtoupper($c['dormitoryAlias']).'</a></td>';
			echo '<td>'.$c['locationAlias'].'</td>';
			echo '<td>'.$c['registryNo'].'</td>';
			echo '<td>'.($canEdit ? '<a href="'.$url.'/users/'.$c['id'].'/:edit">'.($canDel ? 'Edytuj' : 'Zamelduj') : '').'</a></td>';
			echo '<td>'.($canDel ? '<a href="'.$url.'/users/'.$c['id'].'/:del">Wymelduj</a>' : '').'</td>';
		}
		echo '</tbody></table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#resultsT").tablesorter({
			headers: {
				2: {
					sorter: "ds"
				},
				5: {
					sorter: false
				},
				6: {
					sorter: false
				}
			}
		});
    } 
);
</script>
<?
	}

	public function details(array $d) {
		$acl = $this->_srv->get('acl');
		
		if (is_null($d['facultyId'])) {
			$d['facultyId'] = null;
		}

		$url = $this->url(0);
		$urlUser = $url.'/users/'.$d['id'];
		echo '<h1>'.$this->_escape($d['name']).' '.$this->_escape($d['surname']).'</h1>';
		echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
		echo '<p><em>Typ:</em> '.self::getUserType($d['typeId']);
		echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small>'.(strlen($d['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['locationComment'].'" />':'').'</p>';
		if($d['typeId'] != UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL && $d['typeId'] <= 50) {
			echo '<p><em>Wydział:</em> '.(!is_null($d['facultyName'])?$d['facultyName']:'').'</p>';
			if($d['facultyId'] != 0) {
				echo '<p><em>Rok studiów:</em> '.self::$studyYears[$d['studyYearId']].'</p>';
			}
		}
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
		if (!is_null($d['lastInvLoginAt']) && $d['lastInvLoginAt'] != 0 ) {
			echo '<p><em>Ost. nieud. log.:</em> '.date(self::TIME_YYMMDD_HHMM, $d['lastInvLoginAt']);
			if (!is_null($d['lastInvLoginIp'])) {
				echo '<small> ('.$d['lastInvLoginIp'].')</small>';
			}
			echo '</p>';
		}
		if (!is_null($d['referralStart']) && $d['referralStart'] != 0) {
			echo '<p><em>Początek skierowania:</em> '.date(self::TIME_YYMMDD, $d['referralStart']).'</p>';
		}
		if (!is_null($d['referralEnd']) && $d['referralEnd'] != 0) {
			echo '<p><em>Koniec skierowania:</em> '.date(self::TIME_YYMMDD, $d['referralEnd']).'</p>';
		}
		echo '<p><em>Język:</em> '.self::$languages[$d['lang']];
		echo '<p class="displayOnHover"><em>Znajdź na:</em>';
		echo ' <a href="http://www.google.pl/search?q='.urlencode($d['name'].' '.$d['surname']).'">google</a>';
		echo ' <a href="http://www.facebook.com/search/results.php?q='.urlencode($d['name'].' '.$d['surname']).'">face-booczek</a>';
		echo '</p>';
		

		if (strlen($d['commentSkos'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['commentSkos'])).'</p>';
		}
		echo '</div>';
		echo '<p class="nav"><a href="'.$urlUser.'">Dane</a> ';
		if ($acl->sruAdmin('computer', 'addForUser', $d['id'])) {
			echo ' &bull; <a href="'.$urlUser.'/computers/:add">Dodaj komputer</a> ';
		}
		if ($acl->sruAdmin('penalty', 'addForUser', $d['id'])) {
			echo '&bull; <a href="'. $url.'/penalties/:add/user:'.$d['id'].'">Ukarz</a> ';
		}
		echo 	'&bull; <a href="'.$urlUser.'/history">Historia profilu</a>
		 	&bull; <a href="'.$urlUser.'/:edit">Edycja</a>
		  	&bull; <span id="userMoreSwitch"></span>'; 
		if (!$acl->sruAdmin('computer', 'addForUser', $d['id'])) {
			echo UFlib_Helper::displayHint("Komputer można dodać tylko aktywnemu użytkownikowi, który ma uzupełniony adres e-mail i rok studiów.", false);
		}
	
		if (strlen($d['commentSkos'])) echo ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['commentSkos'].'" />';
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
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.strtoupper($d['dormitoryAlias']).'</a>, '; 
		echo ($acl->sruWalet('room', 'edit') ? '<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'/:edit">' : '').$d['locationAlias'].($acl->sruWalet('room', 'edit') ?'</a>' : ''); 
		echo ' <small>('.$d['locationUsersMax'].'-os, '.UFtpl_SruAdmin_Room::getRoomType($d['locationTypeId']).')</small></p>';
		if ($acl->sruWalet('user', 'view', $d['id'])) {
			echo '<p><em>Dokwaterowany:</em> '.($d['overLimit'] ? 'tak' : 'nie').'</p>';
			echo '<p><em>Login:</em> '.$d['login'].(!$d['active']?' <strong>(konto nieaktywne)</strong>':'').'</p>';
			if(!is_null($d['registryNo']) && $d['registryNo'] != '') {
				echo '<p><em>Nr indeksu:</em> '.$d['registryNo'].'</p>';
			}
			if(!is_null($d['typeId']) && $d['typeId'] != '') {
				echo '<p><em>Typ:</em> '.self::getUserType($d['typeId']);
			}
			if(!is_null($d['email']) && $d['email'] != '') {
				echo '<p><em>E-mail:</em> <a href="mailto:'.$d['email'].'">'.$d['email'].'</a></p>';
			}
		}
		if($d['typeId'] != UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL && $d['typeId'] <= 50) {
			echo '<p><em>Wydział:</em> '.(!is_null($d['facultyName'])?$d['facultyName']:'').'</p>';
			if($d['facultyId'] != 0) {
				echo '<p><em>Rok studiów:</em> '.(!is_null($d['studyYearId'])?self::$studyYears[$d['studyYearId']]:'').'</p>';
			}
		}
		if ($acl->sruWalet('user', 'view', $d['id'])) {
			echo '<p><em>Adres:</em>'.nl2br($this->_escape($d['address'])).'</p>';
			echo '<p><em>Typ dokumentu:</em>'.self::$documentTypes[$d['documentType']].'</p>';
			echo '<p><em>Nr dokumentu:</em>'.nl2br($this->_escape($d['documentNumber'])).'</p>';
			echo '<p><em>Narodowość:</em>'.nl2br($this->_escape($d['nationalityName'])).'</p>';
			if(!is_null($d['pesel']) && $d['pesel'] != '') {
				echo '<p><em>PESEL:</em>'.nl2br($this->_escape($d['pesel'])).'</p>';
			}
			if(!is_null($d['birthDate']) && $d['birthDate'] != '') {
				echo '<p><em>Data urodzenia:</em>'.date(self::TIME_YYMMDD,$d['birthDate']).'</p>';
			}
			if(!is_null($d['birthPlace']) && $d['birthPlace'] != '') {
				echo '<p><em>Miejsce urodzenia:</em>'.nl2br($this->_escape($d['birthPlace'])).'</p>';
			}
			if(!is_null($d['userPhoneNumber']) && $d['userPhoneNumber'] != '') {
				echo '<p><em>Tel. mieszkańca:</em>'.nl2br($this->_escape($d['userPhoneNumber'])).'</p>';
			}
			if(!is_null($d['guardianPhoneNumber']) && $d['guardianPhoneNumber'] != '') {
				echo '<p><em>Tel. opiekuna:</em>'.nl2br($this->_escape($d['guardianPhoneNumber'])).'</p>';
			}
			echo '<p><em>Płeć:</em>'.(!$d['sex'] ? 'Mężczyzna' : 'Kobieta').'</p>';
			if (is_null($d['modifiedBy'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedBy']).'</a>';;
			}
			echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
			if (!is_null($d['referralStart']) && $d['referralStart'] != 0) {
				echo '<p><em>Początek skierowania:</em> '.date(self::TIME_YYMMDD, $d['referralStart']).'</p>';
			}
			if (!is_null($d['referralEnd']) && $d['referralEnd'] != 0) {
				echo '<p><em>Koniec skierowania:</em> '.date(self::TIME_YYMMDD, $d['referralEnd']).'</p>';
			}
			if (!is_null($d['lastLocationChange']) && $d['lastLocationChange'] != 0) {
				echo '<p><em>'.($d['active']? 'Zameldowany od' : 'Wymeldowany').':</em> '.date(self::TIME_YYMMDD, $d['lastLocationChange']).'</p>';
			}
			if (strlen($d['comment'])) {
				echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
			}
			echo '<p class="nav"><a href="'.$urlUser.'">Dane</a> ';
			echo 	'&bull; <a href="'.$urlUser.'/history">Historia profilu</a>';
			if ($acl->sruWalet('user', 'edit', $d['id'])) {
				echo ' &bull; <a href="'.$urlUser.'/:edit">'.($d['active'] ? 'Edycja' : 'Meldowanie').'</a>';
			}
			if ($acl->sruWalet('user', 'del', $d['id'])) {
				echo ' &bull; <a href="'.$urlUser.'/:del">Wymeldowanie</a>';
			}
		}
	}

	public function detailsUser(array $d) {
                echo '<p><em>' . _("Imię i nazwisko:") . '</em><span class="userData"> ' . $this->_escape($d['name']) . ' ' . $this->_escape($d['surname']) . '</span></p>';
                echo '<p><em>' . _("Typ konta:") . '</em><span class="userData"> ' . _(self::getUserType($d['typeId'])) . '</span>';
                echo '<p><em>' . _("E-mail:") . '</em><span class="userData"> <a href="mailto:' . $d['email'] . '">' . $d['email'] . '</a></span></p>';
                echo '<p><em>' . _("Zameldowanie:") . '</em><span class="userData"> ' . (($d['lang']=='pl')?$d['dormitoryName']:$d['dormitoryNameEn']) . _(', pok. '). $d['locationAlias'] . '</span></p>';
                if ($d['typeId'] != UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL && $d['typeId'] <= 50) {
                        echo '<p><em>' . _("Wydział:") . '</em><span class="userData"> ' . (!is_null($d['facultyName']) ? _($d['facultyName']) : '') . '</span></p>';
                        if ($d['facultyId'] != 0) {
                                echo '<p><em>' . _("Rok studiów:") . '</em><span class="userData"> ' . (!is_null($d['studyYearId']) ? self::$studyYears[$d['studyYearId']] : '') . '</span></p>';
                        }
                }
                echo '<p"><a class="userAction" href="' . $this->url(0) . '/profile">' . _("Edytuj/Szczegóły") . '</a>';
        }

	public function titleDetails(array $d) {
		echo $this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function titleEdit(array $d) {
		echo 'Edycja użytkownika: '.$this->_escape($d['name']).' '.$this->_escape($d['surname']).' ('.$d['login'].')';
	}

	public function formEditAdmin(array $d, $faculties, $dormitories) {
		$acl = $this->_srv->get('acl');

		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		if ($this->_srv->get('msg')->get('userEdit/errors/ip/noFreeAdmin')) {
			echo $this->ERR('Nie ma wolnych IP w tym DS-ie');
		}
		echo $form->login('Login');
		if ($acl->sruAdmin('user', 'add') && array_key_exists($d['typeId'], self::$userTypesForAdmin)) {
			echo $form->typeId('Typ', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(self::$userTypesForAdmin),
			));
		}
		echo $form->email('E-mail');
		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
		));
		if($d['typeId'] != UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL && $d['facultyId'] != 0 && $d['typeId'] <= 50) {
			echo $form->studyYearId('Rok studiów', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(self::$studyYears),
			));
		}
		if ($acl->sruAdmin('user', 'fullEdit', $d['id'])) {
			$tmp = array();
			foreach ($dormitories as $dorm) {
				$temp = explode("ds", $dorm['dormitoryAlias']);
				if (!isset($temp[1])) {
					$temp[1] = $dorm['dormitoryAlias'];
				}
				$tmp[$dorm['dormitoryId']] = $temp[1] . ' ' . $dorm['dormitoryName'];
			}
			echo $form->dormitory('Akademik', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo $form->locationAlias('Pokój');
		}
		echo $form->commentSkos('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		if ($acl->sruAdmin('user', 'fullEdit', $d['id']) && $acl->sruAdmin('user', 'add')) {
			echo $form->active('Aktywny', array('type'=>$form->CHECKBOX));
		}
		echo $form->_fieldset('Zmiana hasła');
			echo $form->password('Nowe hasło', array('type'=>$form->PASSWORD,  ));
			echo $form->password2('Potwierdź hasło', array('type'=>$form->PASSWORD));
		echo $form->_end();
	}

	public function formEditWalet(array $d, $dormitories, $faculties) {
		$conf = UFra::shared('UFconf_Sru');
		$d['locationId'] = $d['locationAlias'];
		$d['dormitory'] = $d['dormitoryId'];
		$post = $this->_srv->get('req')->post;

		if(!is_null($d['birthDate'])){
			$d['birthDate'] = date(self::TIME_YYMMDD, $d['birthDate']);
		}else{
			$d['birthDate'] = '';
		}
		
		$form = UFra::factory('UFlib_Form', 'userEdit', $d, $this->errors);
		echo $form->name('Imię', array('class'=>'required'));
		echo $form->surname('Nazwisko', array('class'=>'required'));
		echo $form->sex('Płeć', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(array("Mężczyzna", "Kobieta")),
			'class' => 'required'
		));
		
		$tmp = array();
		if (in_array('1', $conf->userTypesToRegister)) {
			$tmp = $tmp + array('----------Rok akademicki----------') + self::$userTypesForWaletAcademic;
		}
		if (in_array('2', $conf->userTypesToRegister)) {
			$tmp = $tmp + array(20 => '----------Wakacje----------') + self::$userTypesForWaletSummer;
		}
		echo $form->typeId('Typ mieszkańca', array(
			'type'=>$form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class' => 'required',
			'id' => 'userTypeSelector'
		));

		echo $form->registryNo('Nr indeksu', array('after'=>'<span id="registryNoCheckResult"></span><br/>'));
		
		$tmp = array();
		foreach ($faculties as $fac) {
			if ($fac['id'] == 0) continue; // N/D powinno być na końcu
			$tmp[$fac['id']] = $fac['name'];
		}
		$tmp['0'] = 'N/D';

		echo '<span id="facultyFields">' . $form->facultyId('Wydział', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class'=>'required',
			'id' => 'facultySelector'
		)) . '</span>';

		echo $form->address('Adres', array('class'=>'necessary address', 
			'type'=>$form->TEXTAREA, 
			'rows'=>3,
			'after'=>UFlib_Helper::displayHint(nl2br("Format: \n kod pocztowy miejscowość \n ulica nr domu/nr mieszkania"))));
		echo $form->documentType('Typ dokumentu', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$documentTypes),
			'class'=>'necessary',
			'id' => 'documentTypeSelector',
		));

		echo $form->documentNumber('Numer dokumentu', array('class'=>'necessary'));
		echo $form->nationalityName('Narodowość', array('class'=>'necessary',
														'after'=>UFlib_Helper::displayHint("Np. &quot;polska&quot;, &quot;niemiecka&quot;, &quot;angielska&quot;,")));
		echo $form->pesel("PESEL", array('after'=>'<span id="peselValidationResult"></span><br/>'));

		echo $form->birthDate('Data urodzenia', array('type' => $form->CALENDER,'after'=>UFlib_Helper::displayHint("Data w formacie RRRR-MM-DD, np. 1988-10-06")));
		echo $form->birthPlace("Miejsce urodzenia");
		echo $form->userPhoneNumber("Nr telefonu mieszkańca");
		echo $form->guardianPhoneNumber("Nr telefonu opiekuna");

		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['dormitoryAlias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['dormitoryAlias'];
			}
			$tmp[$dorm['dormitoryId']] = $temp[1] . ' ' . $dorm['dormitoryName'];
		}
		echo '<legend>Zamieszkanie</legend>';
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
			'class'=>'required',
		));
		echo $form->locationAlias('Pokój', array('class'=>'required'));
		if ($d['active']) {
			try {
				if (array_key_exists('lastLocationChangeActive', $post->userEdit)) {
					$change = $post->userEdit['lastLocationChangeActive'];
				}
			} catch (UFex_Core_DataNotFound $e) {
				$change = date(self::TIME_YYMMDD, time());
			}
			echo '<div id="changeLocationMore">';
			echo $form->lastLocationChangeActive('Data zmiany pokoju', array(
				'value'=>$change,
				'class'=>'required',
				'after'=>UFlib_Helper::displayHint("Data, kiedy mieszkaniec zmienił pokój."),
			));
			echo '</div>';
		}
		echo $form->overLimit('Dokwaterowany', array('type'=>$form->CHECKBOX));

		echo $form->lang('Język', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$languages),
			'after'=>UFlib_Helper::displayHint("Wiadomości e-mail będa przychodziły w wybranym języku.<br/><br/>You will receive e-mails in the chosen language."),
		));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo '<legend>Meldunek</legend>';
		if (!$d['active']) {
			echo $form->active('Zamelduj (aktywuj konto)', array('type'=>$form->CHECKBOX));
		} else {
			echo $form->printConfirmation('Wydrukuj nowe potwierdzenie zamledowania (zmień hasło)', array('type'=>$form->CHECKBOX));
		}
		try {
			$referralStart = $post->userEdit['referralStart'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralStart = date(self::TIME_YYMMDD, $d['referralStart']);
			if ((is_null($d['referralStart']) || $d['referralStart'] == 0) && $d['active'] == false) {
				$referralStart = '';
			} else if ((is_null($d['referralStart']) || $d['referralStart'] == 0) && $d['active'] == true) {
				$referralStart = date(self::TIME_YYMMDD, time());
			}
		}
		echo $form->referralStart('Początek skierowania', array(
			'type' => $form->CALENDER,
			'value'=>$referralStart,
			'class'=>'required',
			'after'=>UFlib_Helper::displayHint("Data początku pobytu."),
		));
		try {
			$referralEnd = $post->userEdit['referralEnd'];
		} catch (UFex_Core_DataNotFound $e) {
			$referralEnd = date(self::TIME_YYMMDD, $d['referralEnd']);
			if ((is_null($d['referralEnd']) || $d['referralEnd'] == 0) && $d['active'] == false) {
				$referralEnd = '';
			} else if ((is_null($d['referralEnd']) || $d['referralEnd'] == 0) && $d['active'] == true) {
				$referralEnd = $conf->usersAvailableTo;
			}
		}
		echo $form->referralEnd('Koniec skierowania', array('value'=>$referralEnd,
			'type' => $form->CALENDER,
			'class'=>'required',
			'after'=>UFlib_Helper::displayHint("Data końca pobytu."),
		));
		if ($d['active']) {
			try {
				if (array_key_exists('lastLocationChange', $post->userEdit)) {
					$checkIn = $post->userEdit['lastLocationChange'];
				}
			} catch (UFex_Core_DataNotFound $e) {
				$checkIn = date(self::TIME_YYMMDD, $d['lastLocationChange']);
				if ((is_null($d['lastLocationChange']) || $d['lastLocationChange'] == 0) && $d['active'] == false) {
					$checkIn = '';
				} else if ((is_null($d['lastLocationChange']) || $d['lastLocationChange'] == 0) && $d['active'] == true) {
					$checkIn = date(self::TIME_YYMMDD, time());
				}
			}
			echo '<div id="checkInMore">';
			echo $form->lastLocationChange('Data zameldowania', array(
				'type' => $form->CALENDER,
				'value'=>$checkIn,
				'class'=>'required',
				'after'=>UFlib_Helper::displayHint("Data, kiedy mieszkaniec wprowadził się do DSu."),
			));
			echo '</div>';
		}
		
?><script type="text/javascript">
$(document).ready(function(){
	if($('#userTypeSelector').val() == 23){
		$('#facultyFields').hide();
	}
});

$('#userTypeSelector').change(function(){
	var userTypeSelector = $('#userTypeSelector');
	if(userTypeSelector.val() == 23){
		$('#facultySelector').val(0);
	}else if(userTypeSelector.val() != 23 && $('#facultyFields').is(':hidden') == true){
		$('#facultyFields').show();
	}
});
(function (){
	var typeId = document.getElementById('userTypeSelector');
	function registryChangeClass(){
		var registryNo = document.getElementById('userEdit_registryNo');
		if(typeId.value == 1 || typeId.value == 2 || typeId.value == 5) {
			registryNo.setAttribute('class', 'required');
		} else {
			registryNo.setAttribute('class', '');
		}
	}
	typeId.onchange = registryChangeClass; 
	registryChangeClass();
	
	var documentTypeId = document.getElementById('documentTypeSelector');
	function documentNumberChangeClass(){
		var documentNo = document.getElementById('userEdit_documentNumber');
		if(documentTypeId.value != <?=UFbean_Sru_User::DOC_TYPE_NONE?>) {
			documentNo.setAttribute('class', 'necessary');
		} else {
			documentNo.setAttribute('class', '');
		}
	}
	documentTypeId .onchange = documentNumberChangeClass;
	documentNumberChangeClass();

	var nationality = document.getElementById('userEdit_nationalityName');
	function peselChangeClass(){
		var pesel = document.getElementById('userEdit_pesel');
		if(nationality.value.toLowerCase() == '<?=UFbean_Sru_User::NATIONALITY_PL?>'){
			pesel.setAttribute('class', 'necessary');
		}else{
			pesel.setAttribute('class', '');
		}
	}
	nationality.onchange = peselChangeClass;
	peselChangeClass();

	var name = document.getElementById('userEdit_name');
	function changeSex() {
		var sex = document.getElementById('userEdit_sex');
		if (name.value.slice(-1) == 'a') {
			sex.value = 1;
		} else {
			sex.value = 0;
		}
	}
	name.onchange = changeSex;
        
<?
	if (!$d['active']) {
?>
	var active = document.getElementById('userEdit_active');
	var refStart = document.getElementById('userEdit_referralStart');
	var refStartVal = document.getElementById('userEdit_referralStart').value;
	var refEnd = document.getElementById('userEdit_referralEnd');
	var refEndVal = document.getElementById('userEdit_referralEnd').value;
	var startVal = '<?=date(self::TIME_YYMMDD, time())?>';
	var endVal = '<?=$conf->usersAvailableTo?>';
	function referralsChangeValues(){
		if(active.checked){
			refStart.value = startVal;
			refEnd.value = endVal;
		}else{
			refStart.value = refStartVal;
			refEnd.value = refEndVal;
		}
	}
	active.onchange = referralsChangeValues;

<?
	} else {
?>
	var div = document.getElementById('changeLocationMore');
	var div2 = document.getElementById('checkInMore');
	var roomAlias = document.getElementById('userEdit_locationAlias');
	roomVal = '<?=$d['locationAlias']?>';
	function locationChangeValue() {
		var roomCurr = document.getElementById('userEdit_locationAlias').value;
		if(roomCurr == roomVal) {
			div.style.display = 'none';
			div2.style.display = 'block';
		} else {
			div.style.display = 'block';
			div2.style.display = 'none';
		}
	}
	roomAlias.onkeyup = locationChangeValue;
	locationChangeValue();
<?
	}
?>
	
	var pesel = document.getElementById('userEdit_pesel');
	function validatePesel() {
		if (pesel.value == '') {
			document.getElementById('peselValidationResult').innerHTML = '';
			return;
		}
		if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else { // code for IE6, IE5
			xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText == 'false') {
					document.getElementById('peselValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
				} else {
					var birthDate = document.getElementById('userEdit_birthDate');
					birthDate.value = xmlhttp.responseText.replace(/"/g, '');
					document.getElementById('peselValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
				}
			}
		}
		xmlhttp.open('GET',"<? echo $this->url(0); ?>/users/validatepesel/" + encodeURIComponent(pesel.value), true);
		xmlhttp.send();
	}
	pesel.onchange = validatePesel;
        

        var registryNo = document.getElementById('userEdit_registryNo');
        var registryNoErr = document.getElementById('userEdit_registryNoErr');
        
	function registryNoCheck() {
                if(registryNoErr) {
                        registryNoErr.innerHTML = '';
                        registryNoErr.setAttribute('class', '');
                }
                if (registryNo.value == '' || registryNo.value == '<?echo $d['registryNo'];?>') {
                        document.getElementById('registryNoCheckResult').innerHTML = '';
                }
                if(registryNo.value != '<?echo $d['registryNo'];?>'){
                        if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                                xmlhttp = new XMLHttpRequest();
                        } else { // code for IE6, IE5
                                xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
                        }
                        xmlhttp.onreadystatechange = function() {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                        if (xmlhttp.responseText == 'ok') {
                                                document.getElementById('registryNoCheckResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
                                        } else if (xmlhttp.responseText == 'invalid') {
                                                document.getElementById('registryNoCheckResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
                                        } else {
                                                document.getElementById('registryNoCheckResult').innerHTML = xmlhttp.responseText;
                                        }
                                }
                        }
                        xmlhttp.open('GET',"<? echo $this->url(0); ?>/users/checkregistryno/"+ encodeURIComponent(registryNo.value), true);
                        xmlhttp.send();
                } else {
                        document.getElementById('registryNoCheckResult').innerHTML = '';
                }
        }
	
        registryNo.onchange = registryNoCheck;
        window.onload = registryNoCheck;
        
        function delRegistryNoError() {
                        if(registryNoErr.innerHTML == 'Nr indeksu przypisany do innego mieszkańca') {
                                registryNoErr.innerHTML = '';
                                registryNoErr.setAttribute('class', '');
                                registryNoCheck();
                        }
        }
        
        window.onload = delRegistryNoError;
        
})()
$(function() {
	$( "#userEdit_nationalityName" ).autocomplete({
		source: function(req, resp) {
			$.getJSON("<? echo $this->url(0); ?>/users/quickcountrysearch/" + encodeURIComponent(req.term), resp);
		},
		minLength: 1
	});
});
</script><?
	}

	public function formDelWalet(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$post = $this->_srv->get('req')->post;
		try {
			$checkOut = $post->userDel['lastLocationChange'];
		} catch (UFex_Core_DataNotFound $e) {
			$checkOut = date(self::TIME_YYMMDD, time());
		}
		$form = UFra::factory('UFlib_Form', 'userDel', $d, $this->errors);
		echo $form->lastLocationChange('Data wymeldowania', array(
			'type' => $form->CALENDER,
			'value'=>$checkOut,
			'class'=>'required',
			'after'=>UFlib_Helper::displayHint("Data, kiedy mieszkaniec wyprowadził się z DSu."),
		));
	}

	public function shortList(array $d) {
		$url = $this->url(0).'/users/';
		foreach ($d as $c) {
			echo '<li'.($c['banned']?' class="ban"':'').'>'.(!$c['active']?'<del>':'').'<a href="'.$url.$c['id'].'">'.$this->_escape($c['name']).' '.$this->_escape($c['surname']).'</a>'.(!$c['active']?'</del>':'').(strlen($c['commentSkos']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['commentSkos'].'" />':'').'</li>';
		}
	}
	
	public function contactForm(array $d) {
                $form = UFra::factory('UFlib_Form', 'sendMessage', $d, $this->errors);
                echo _("Wiadomość do administratora SKOS:");
                echo $form->message('', array('type' => $form->TEXTAREA, 'rows' => 5));
                echo $form->_submit(_("Wyślij"));
                echo '<br/>' . _("Odpowiedź otrzymasz na maila zarejestrowanego w SRU.");
        }

	public function userBar(array $d, $ip, $time, $invIp, $invTime) {
		echo '<ul class="menu">';
		echo '<li><a class="mainMenuItem" href="#">'.$this->_escape($d['name']).' &quot;'.$this->_escape($d['login']).'&quot; '.$this->_escape($d['surname']).'</a>';
		echo '<ul>';
		echo '<li><a href="'.$this->url(0).'/profile/">'._("Profil").'</a>';
		if (!is_null($time) && $time != 0 ) {
			echo '<li class="menuLoginItem">'._("Ostatnie udane logowanie: ").'<br/>'.date(self::TIME_YYMMDD_HHMM, $time);
			if (!is_null($ip)) {
				echo ' ('._("z IP: ").$ip.')';
			}
			echo '</li>';
		}
		if (!is_null($invTime) && $invTime != 0 ) {
			echo '<li class="menuLoginItem">'._("Ostatnie nieudane logowanie: ").'<br/>'.date(self::TIME_YYMMDD_HHMM, $invTime);
			if (!is_null($invIp)) {
				echo ' ('._("z IP: ").$invIp.')';
			}
			echo '</li>';
		}
		echo '<li><a href="'.$this->url(0).'/logout">'._('Wyloguj').'</a></li>';
		echo '</ul></li></ul>';
?>
<script type="text/javascript">
	$(document).ready(function () {
		$('.menu').jqsimplemenu();
	});
</script>
<?
        }

	public function userAddMailTitlePolish(array $d) {
		echo 'Witamy w sieci SKOS';
	}

	public function userAddMailTitleEnglish(array $d) {
		echo 'Welcome in SKOS network';
	}

	public function userAddMailBodyPolish(array $d, $dutyHours) {
		$conf = UFra::shared('UFconf_Sru');
		if ($d['typeId'] == UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL) {
			$waletText = $conf->touristMailWaletText;
			$skosText = $conf->touristMailSkosText;
		} else {
			$waletText = $conf->userMailWaletText;
			$skosText = $conf->userMailSkosText;
		}

		echo 'Witamy w Sieci Komputerowej Osiedla Studenckiego Politechniki Gdańskiej!'."\n";
		echo "\n";
                if($d['sex']==true)
                {
                    echo 'Jeżeli otrzymałaś tę wiadomość, a nie masz konta w SKOS PG, prosimy o zignorowanie tej wiadomości.'."\n\n";
                }
                else
                {
                    echo 'Jeżeli otrzymałeś tę wiadomość, a nie masz konta w SKOS PG, prosimy o zignorowanie tej wiadomości.'."\n\n";   
                }
                echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Nasza sieć obejmuje swoim zasięgiem sieci LAN wszystkich Domów Studenckich Politechniki Gdańskiej, jest częścią Uczelnianej Sieci Komputerowej (USK PG) i dołączona jest bezpośrednio do sieci TASK.'."\n";
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo $skosText;
		echo "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo $waletText;
		echo "\n";
		if (!is_null($dutyHours)) {
			echo '- - - - - - - - - - -'."\n";
			echo "\n";
			$dutyHours->write('upcomingDutyHoursToEmailPolish', $d, 3);
			echo "\n";
		}
                
	}
	
	public function userAddMailBodyEnglish(array $d, $dutyHours) {
		echo 'Welcome in Gdańsk University of Technology Students’ Campus Computer Network (polish acronym - SKOS PG)!' . "\n";
		echo "\n";
		echo 'If you received this message but you don’t have an account in SKOS PG, please ignore it.' . "\n";
		echo '- - - - - - - - - - -'."\n";
		echo "\n";
		echo 'Any information about our network including FAQ you can find on our page'."\n";
		echo 'http://faq.ds.pg.gda.pl/'."\n";
		echo 'Any information about our dormitories you can find on our page'."\n";
		echo 'http://akademiki.pg.gda.pl/'."\n";
		echo "\n";
		if (!is_null($dutyHours)) {
			echo '- - - - - - - - - - -'."\n";
			echo "\n";
			$dutyHours->write('upcomingDutyHoursToEmailEnglish', $d, 3);
			echo "\n";
		}
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

	public function userAddByAdminMailTitle(array $d) {
		echo 'Twoje konto zostało utworzone';
	}
        
        public function userAddByAdminMailBody(array $d, $password) {
		echo 'Potwierdzamy, że Twoje konto zostało utworzone.'."\n\n";
		echo 'Login: '.$d['login']."\n";
                echo 'Hasło: '.$password."\n";
		echo 'Imię: '.$d['name']."\n";
		echo 'Nazwisko: '.$d['surname']."\n";
		echo 'E-mail: '.$d['email']."\n";
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
		echo 'Faculty: '.$d['facultyNameEn']."\n";
		echo 'Year of study: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
		echo $d['dormitoryNameEn']."\n";
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
			echo 'Konto aktywne: '.($d['active'] ? 'tak' : 'nie')."\n";
			echo 'E-mail: '.$d['email']."\n";
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
			echo 'Account: '.($d['active'] ? 'active' : 'not active')."\n";
			echo 'E-mail: '.$d['email']."\n";
			echo 'Faculty: '.$d['facultyNameEn']."\n";
			echo 'Year of study: '.UFtpl_Sru_User::$studyYears[$d['studyYearId']]."\n";
			echo $d['dormitoryNameEn']."\n";
			echo 'Room: '.$d['locationAlias']."\n";
		}
	}

	public function stats(array $d) {
		$sum = 0;
		$woman = 0;
		$faculties = array();
		foreach ($d as $u) {
			if ($u['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			if (is_null($u['facultyName']) || $u['facultyName'] == '') {
				$u['facultyName'] = 'N/D';
			}
			if(!array_key_exists($u['facultyName'], $faculties)) {
				$faculties[$u['facultyName']] = new PeopleCounter();
			}
			if($u['sex']==true) {
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
			if ($u['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			if (is_null($u['studyYearId']) || $u['studyYearId'] == '') {
				$u['studyYearId'] = '';
			}
			if(!array_key_exists(' '.$u['studyYearId'], $years)) {
				$years[' '.$u['studyYearId']] = new PeopleCounter();
			}
			if ($u['sex']) {
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

		echo '<h3>Rozkład płci uwzględniając typ konta:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Typ konta</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
		$types = array();
		foreach ($d as $u) {
			if ($u['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			if(!array_key_exists($u['typeId'], $types)) {
				$types[$u['typeId']] = new PeopleCounter();
			}
			if ($u['sex']) {
				$types[$u['typeId']]->addUser(true);
			} else {
				$types[$u['typeId']]->addUser();
			}
		}
		arsort($types);
		$chartDataWoman = '';
		$chartDataMan = '';
		$chartLabel = '';
		$chartLabelR = '';
		$chartDataType = '';
		$chartLabelType = '';
		$typeSum = 0;
		while ($type = current ($types)) {
			echo '<tr><td>'.self::getUserType(key($types)).'</td>';
			echo '<td>'.$type->getUsers().'</td>';
			echo '<td>'.$type->getWomen().'</td>';
			echo '<td>'.($type->getUsers() - $type->getWomen()).'</td></tr>';
			$chartDataWoman = $chartDataWoman.(round($type->getWomen()/$type->getUsers()*100)).',';
			$chartDataMan = $chartDataMan.(round(($type->getUsers()-$type->getWomen())/$type->getUsers()*100)).',';
			$chartLabel = self::getUserType(key($types)).'|'.$chartLabel;
			$chartLabelR = (round($type->getWomen()/$type->getUsers()*100)).'% / '.(round(($type->getUsers()-$type->getWomen())/$type->getUsers()*100)).'%|'.$chartLabelR;
			$chartDataType = (round($type->getUsers()/$sum*100)).','.$chartDataType;
			$chartLabelType = self::getUserType(key($types)).': '.round($type->getUsers()/$sum*100).'%|'.$chartLabelType;
			$typeSum++;
			next($types);
		}
		echo '</table>';
		$chartDataWoman = substr($chartDataWoman, 0, -1);
		$chartDataMan = substr($chartDataMan, 0, -1);
		$chartDataType = substr($chartDataType, 0, -1);
		
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=700x150&chd=t:'.$chartDataType;
		echo '&cht=p3&chl='.$chartLabelType.' alt=""/>';
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x'.($typeSum*30).'&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartDataWoman.'|'.$chartDataMan.'&chxt=y,r&chxl=0:|'.$chartLabel.'1:|'.$chartLabelR.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład kar uwzględniając typ konta:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Typ konta</th><th>Kar</th><th>Obecnie ukaranych</th></tr>';
		$bannedTypes = array();
		$bannedTypesActive = array();
		$bannedSum = 0;
		$bannedActiveSum = 0;
		$bannedTypesSum = 0;
		foreach ($d as $u) {
			if ($u['banned']) {
				if(!array_key_exists($u['typeId'], $bannedTypesActive)) {
					$bannedTypesActive[$u['typeId']] = 1;
					$bannedActiveSum++;
				} else {
					$bannedTypesActive[$u['typeId']]++;
					$bannedActiveSum++;
				}
			}
			if ($u['bans'] > 0) {
				if(!array_key_exists($u['typeId'], $bannedTypes)) {
					$bannedTypes[$u['typeId']] = $u['bans'];
					$bannedTypesSum++;
					$bannedSum += $u['bans'];
				} else {
					$bannedTypes[$u['typeId']] += $u['bans'];
					$bannedSum += $u['bans'];
				}
			}
		}
		arsort($bannedTypes);
		$chartDataType = '';
		$chartActiveDataType = '';
		$chartLabelType = '';
		$chartActiveLabelType = '';
		while ($type = current ($bannedTypes)) {
			echo '<tr><td>'.self::getUserType(key($bannedTypes)).'</td>';
			echo '<td>'.$type.'</td>';
			echo '<td>'.(key_exists(key($bannedTypes), $bannedTypesActive) ? $bannedTypesActive[key($bannedTypes)] : '0').'</td></tr>';
			$activeBanned = (key_exists(key($bannedTypes), $bannedTypesActive) ? $bannedTypesActive[key($bannedTypes)] : 0);
			$chartDataType = $type.','.$chartDataType;
			$chartActiveDataType = $activeBanned.','.$chartActiveDataType;
			$chartLabelType = self::getUserType(key($bannedTypes)).': '.round($type/$bannedSum*100).'%|'.$chartLabelType;
			$chartActiveLabelType = self::getUserType(key($bannedTypes)).': '.round($activeBanned/$bannedActiveSum*100).'%|'.$chartActiveLabelType;
			next($bannedTypes);
		}
		echo '<tr><td>ŚREDNIO (kar/typ)</td><td>'.round($bannedSum/$bannedTypesSum,2).'</td><td>'.round($bannedActiveSum/$bannedTypesSum,2).'</td></tr>';
		echo '</table>';

		$chartDataType = substr($chartDataType, 0, -1);
		$chartActiveDataType = substr($chartActiveDataType, 0, -1);
		$chartLabelType = substr($chartLabelType, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=700x200&chd=t:'.$chartActiveDataType.'|'.$chartDataType;
		echo '&cht=pc&chl='.$chartActiveLabelType.$chartLabelType.'" alt=""/>';
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
				if ($u['sex']) {
					$activeBannedWomanSum++;
				}
			}
			$banSum += $u['bans'];
			if ($u['sex']) {
					$womanBanSum += $u['bans'];
			}
			if ($u['bans'] > 0) {
				$bannedSum++;
				if ($u['sex']) {
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
		if ($bannedSum > 0) {
			echo '<tr><td>ŚREDNIO (kar/użytkownik)</td><td>'.round($banSum/$bannedSum,2).'</td><td>'.($bannedWomanSum > 0 ? round($womanBanSum/$bannedWomanSum,2) : '').'</td><td>'.round(($banSum - $womanBanSum)/($bannedSum - $bannedWomanSum),2).'</td></tr>';
		}
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
			if ($u['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			if ($u['sex']) {
				$dormitories[$u['dormitoryAlias']]->addUser(true);
			} else {
				$dormitories[$u['dormitoryAlias']]->addUser();
			}
			$dormitories[$u['dormitoryAlias']]->addToGroupFaculty($u['facultyName'], $u['sex']);
			$dormitories[$u['dormitoryAlias']]->addToGroupYear($u['studyYearId'], $u['sex']);
			$dormitories[$u['dormitoryAlias']]->addToGroupType($u['typeId'], $u['sex']);
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

		echo '<h3>Rozkład płci uwzględniając akademik i typ konta:</h3>';
		reset($dormitories);
		while ($dorm = current($dormitories)) {
			echo '<h4>'.$this->displayDormUrl(substr(key($dormitories),1)).'</h4>';
			echo '<table style="text-align: center; width: 100%;">';
			echo '<tr><th>Typ konta</th><th>Użytkowników</th><th>Kobiet</th><th>Mężczyzn</th></tr>';
			$chartDataType = '';
			$chartLabel = '';
			$types = $dorm->getGroupType();
			while ($type = current($types)) {
				echo '<tr><td>'.self::getUserType(key($types)).'</td>';
				echo '<td>'.$type->getUsers().'</td>';
				echo '<td>'.$type->getWomen().'</td>';
				echo '<td>'.($type->getUsers() - $type->getWomen()).'</td></tr>';
				$chartDataType = (round($type->getUsers()/$dorm->getUsers()*100)).','.$chartDataType;
				$chartLabel = self::getUserType(key($types)).': '.round($type->getUsers()/$dorm->getUsers()*100).'%|'.$chartLabel;
				next($types);
			}
			echo '</table>';
			$chartDataType = substr($chartDataType, 0, -1);
			echo '<div style="text-align: center;">';
			echo '<img src="http://chart.apis.google.com/chart?chs=800x150&chd=t:'.$chartDataType;
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
	private $groupType = array();
	private $bans = 0;
	private $activeBans = 0;

	public function addToGroupFaculty($key, $value) {
		if (is_null($key) || $key == '') {
			$key = 'N/D';
		}
		if(!array_key_exists(' '.$key, $this->groupFaculty)) {
			$this->groupFaculty[' '.$key] = new PeopleCounter();
		}
		if ($value) {
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
		if (is_null($key) || $key == '') {
			$key = '';
		}
		if(!array_key_exists(' '.$key, $this->groupYear)) {
			$this->groupYear[' '.$key] = new PeopleCounter();
		}
		if ($value) {
			$this->groupYear[' '.$key]->addUser(true);
		} else {
			$this->groupYear[' '.$key]->addUser();
		}
	}

	public function addToGroupType($key, $value) {
		if(!array_key_exists($key, $this->groupType)) {
			$this->groupType[$key] = new PeopleCounter();
		}
		if ($value) {
			$this->groupType[$key]->addUser(true);
		} else {
			$this->groupType[$key]->addUser();
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

	public function getGroupType() {
		ksort($this->groupType);
		return $this->groupType;
	}
}
