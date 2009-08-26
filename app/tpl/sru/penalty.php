<?
/**
 * szablon beana kary
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
		echo '<h3>Masz aktywne kary / ostrzeżenia</h3>';

		foreach ($d as $c) {
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo '<div class="warning">';
			} else {
				echo '<div class="ban">';
			}
			echo '<small>Typ: ';

			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo 'Ostrzeżenie';
			} else {
				echo '<strong>Kara</strong>';
			}
			echo '<br/>Ważna do: '.date(self::TIME_YYMMDD_HHMM, $c['endAt']);
			echo '<br/>Powód: '.nl2br($this->_escape($c['reason']));
			echo '</small></div>';
			echo '<br/>';
		}
	}

	public function listAllPenalty(array $d) {
		$url = $this->url(0);
		echo '<h3>Archiwum kar i ostrzeżeń:</h3>';

		foreach ($d as $c) {
			if ($c['endAt'] > time()) {	
				if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
					echo '<div class="warning">';
				} else {
					echo '<div class="ban">';
				}
			} else {
				echo '<div>';
			}
			echo '<small>Typ: ';


			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo 'Ostrzeżenie';
			} else {
				echo '<strong>Kara</strong>';
			}
			echo '<br/>Ważna do: '.date(self::TIME_YYMMDD, $c['endAt']);
			echo '<br/>Powód: '.nl2br($this->_escape($c['reason']));
			echo '</small></div>';
			echo '<hr/>';
		}
	}	
}
