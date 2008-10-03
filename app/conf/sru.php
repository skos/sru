<?

class UFconf_Sru
extends UFconf {

	#protected $computerAvailableTo = '+ 30 days';
	protected $computerAvailableTo = '2009-07-01';
	protected $computerAvailableMaxTo = '2009-07-01';
	
	
	//kary:
/*	const 	OTHER   = 0,
			P2P		= 1, 
			UPLOAD	= 2;*/

	const 	ALL		= 1,
			COMPUTERS = 3,
			WARNING	= 2;
			
/*
	public static $reasons = array(
		self::UPLOAD	=> 'Przekroczenie uploadu',
		self::P2P	=> 'P2P',
		self::OTHER	=> 'Inne',
	);*/
	public static $penaltyTypes = array(
		self::ALL 	=> 'Wszystko',
		self::WARNING => 'Ostrzeżenie',
		self::COMPUTERS => 'Komputery',

	);

}
