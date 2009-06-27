<?

class UFconf_Sru
extends UFconf {

	#protected $computerAvailableTo = '+ 30 days';
	protected $computerAvailableTo = '2009-10-15';	// data waznosci noworejestrowanych komputerow
	protected $computerAvailableMaxTo = '2009-10-15';	// data na przycisku dostepnym administratorom

	protected $noEthers = array(
		'153.19.208.22',
	);
}
