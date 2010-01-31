<?

class UFconf_Sru
extends UFconf {

	#protected $computerAvailableTo = '+ 30 days';
	protected $computerAvailableTo = '2009-10-15';	// data waznosci noworejestrowanych komputerow
	protected $computerAvailableMaxTo = '2009-10-15';	// data na przycisku dostepnym administratorom

	protected $noEthers = array(
		'153.19.208.22',
	);

	protected $checkWalet = true;	// sprawdzac dane uzytkownikow z baza osiedla?
	protected $sendEmail = true;	// wysylac maile dot. kar, edycji danych i danych kompow?
	protected $emailPrefix = '[SRU]';	// prefix maili wysyłanych ze SRU
	protected $exclusions = array('ADMINISTRACJA', 'SKOS', 'Samorząd Studentów', 'Studencka Agencja');	// wykluczenia nazw (imion) ze zliczeń etc.
}
