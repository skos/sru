<?

class UFconf_Sru
extends UFconf {

	#protected $computerAvailableTo = '+ 30 days';
	protected $computerAvailableTo = '2010-07-01';	// data waznosci noworejestrowanych komputerow
	protected $computerAvailableMaxTo = '2010-07-01';	// data na przycisku dostepnym administratorom

	protected $noEthers = array(
		'153.19.208.22',
	);

	protected $checkWalet = false;	// sprawdzac dane uzytkownikow z baza osiedla?
	protected $sendEmail = true;	// wysylac maile dot. kar, edycji danych i danych kompow?
	protected $emailPrefix = '[SRU]';	// prefix maili wysyłanych ze SRU
	protected $exclusions = array('ADMINISTRACJA', 'SKOS', 'Samorząd Studentów', 'Studencka Agencja');	// wykluczenia nazw (imion) ze zliczeń etc.
	protected $excludedWalet = array(); // wykluczeni z migracji

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
}
