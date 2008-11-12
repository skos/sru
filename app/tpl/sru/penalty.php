<?
/**
 * szablon beana usera
 */
class UFtpl_Sru_Penalty
extends UFtpl_Common {

	protected $penaltyTypes = array(
		1 => 'Ostrzeżenie',
		2 => 'jeden komputer',
		3 => 'wszystkie komputery',
	);	

	public function listPenalty(array $d) {
		$url = $this->url(0);
		echo '<h3>Wszystkich aktywnych kar i ostrzeżeń: '. count($d) .'</h3>';

		foreach ($d as $c) {
			echo '<small>Typ kary: ';

			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo '<strong>Ostrzeżenie</strong>';
			} else {
				echo '<strong>Kara na '.$this->_escape($this->penaltyTypes[$c['typeId']]).'</strong>';
			}
			echo '<br/>Ważna do: <strong>'.date(self::TIME_YYMMDD, $c['endAt']).'</strong>';
			echo '<br/>Powód: <strong>'.nl2br($this->_escape($c['reason'])).'</strong>';
			echo '</small>';
			echo '<br/><hr/>';
		}
	}	
}
