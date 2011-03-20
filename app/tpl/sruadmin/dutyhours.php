<?
/**
 * szablon godzin dyżurów
 */
class UFtpl_SruAdmin_DutyHours
extends UFtpl_Common {

	protected $dayNames = array(
		1 => 'poniedziałek',
		2 => 'wtorek',
		3 => 'środa',
		4 => 'czwartek',
		5 => 'piątek',
		6 => 'sobota',
		7 => 'niedziela',
	);

	public function listDutyHours(array $d) {
		echo '<ul>';
		foreach ($d as $c) {
			echo '<li>'.($c['active'] ? '' : '<del>').$this->dayNames[$c['day']].': '.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).($c['active'] ? '' : '</del>').(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').'</li>';
		}
		echo '</ul>';
	}

	private function formatHour($hour) {
		return substr($hour, 0, -2).':'.substr($hour, -2);
	}
}
