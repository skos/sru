<?

class UFconf_Sru
extends UFconf {

	#protected $computerAvailableTo = '+ 30 days';
	protected $computerAvailableTo = '2011-07-01';	// data waznosci noworejestrowanych komputerow
	protected $computerAvailableMaxTo = '2011-07-01';	// data na przycisku dostepnym administratorom

	protected $noEthers = array(
		'153.19.208.22',
	);

	protected $sendEmail = true;	// wysylac maile dot. kar, edycji danych i danych kompow?
	protected $emailPrefix = '[SRU]';	// prefix maili wysyłanych ze SRU
	protected $exclusions = array('ADMINISTRACJA', 'SKOS', 'Samorząd Studentów', 'Studencka Agencja');	// wykluczenia nazw (imion) ze zliczeń etc.

	protected $switchFirmware = array( // aktualne wersje firmware'u używanych switchy
	);

	protected $masterSwitch = '';
	protected $communityRead = '';
	protected $communityWrite = '';
	protected $roomRegex = '';
	protected $switchRegex = '';

	protected $jabberServer = '';
	protected $jabberPort = 0;
	protected $jabberUser = '';
	protected $jabberPassword = '';
	protected $jabberResource = '';
	protected $jabberDomain = '';
	protected $ggGate = '';

	protected $userPrintWaletText = '';
	protected $userPrintSkosText = '';
}
