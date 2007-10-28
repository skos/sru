<?

class UFconf_Sru
extends UFconf {

	protected $computerAvailableTo;
	protected $computerAvailableMaxTo;

	public function __construct() {
		$this->computerAvailableTo = NOW + 3600*24*7;
		$this->computerAvailableMaxTo = NOW + 3600*24*100;
	}
}
