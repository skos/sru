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

	public function apiAllDutyHours(array $d) {
		$currentDay = date('N');
		$lastAdmin = '';
		$lastDay = 0;
		$comments = array();
		$lastComment = 0;

		echo '<table class="sruDutyHours"><thead><tr><th>Akademik (Dormitory)</th><th>Administrator</th><th>Gdzie (Where)</th><th>Poniedziałek (Monday)</th><th>Wtorek (Tuesday)</th><th>Środa (Wednesday)</th><th>Czwartek (Thursday)</th><th>Piątek (Friday)</th><th>Sobota (Saturday)</th><th>Niedziela (Sunday)</th></tr></thead><tbody>';
		foreach ($d as $c) {
			if ($c['adminName'] != $lastAdmin && $lastAdmin != '') {
				for ($i = $lastDay; $i < 7; $i++) {
					echo '<td></td>';
				}
				echo '</tr>';
			}
			if ($c['adminName'] != $lastAdmin) {
				echo '<tr><td>'.strtoupper($c['adminDormAlias']).'</td><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td>';
				$lastAdmin = $c['adminName'];
				$lastDay = 0;
			}
			for ($i = $lastDay; $i < $c['day'] - 1; $i++) {
				echo '<td></td>';
			}
			if (strlen($c['comment'])) {
				$lastComment++;
				$comments[$lastComment] = $c['comment'];
			}
			echo '<td'.($c['day'] == $currentDay ? ' class="sruDutyHoursCurrentDay"' : '').'>'.($c['active'] ? '' : '<del>').$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).($c['active'] ? '' : '</del>').(strlen($c['comment']) ? ' ('.$lastComment.')' : '').'</td>';
			$lastDay = $c['day'];
		}
		for ($i = $lastDay; $i < 7; $i++) {
			echo '<td></td>';
		}
		echo '</tr></tbody></table>';
		if ($lastComment > 0) {
			echo '<div class="sruDutyHoursComments">';
			for ($i = 1; $i <= $lastComment; $i++) {
				echo '('.$i.') '.$comments[$i].'<br/>';
			}
			echo '<div>';
		}
	}

	public function apiUpcomingDutyHours(array $d, $days) {
		$currentDay = date('N');
		$lastDay = $currentDay + $days;
		$thisWeek = '';
		$nextWeek = '';
		$comments = array();
		$lastComment = 0;

		foreach ($d as $c) {
			if (($c['day'] == $currentDay && $c['endHour'] > date('Hi')) || ($c['day'] > $currentDay && $c['day'] <= $lastDay)) {
				if (strlen($c['comment'])) {
					$lastComment++;
					$comments[$lastComment] = $c['comment'];
				}
				$thisWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td>'.$this->dayNames[$c['day']].' '.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' ('.$lastComment.')' : '').'</td></tr>';
			}
			if ($c['day'] <= $lastDay - 7) {
				if (strlen($c['comment'])) {
					$lastComment++;
					$comments[$lastComment] = $c['comment'];
				}
				$nextWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td>'.$this->dayNames[$c['day']].' '.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' ('.$lastComment.')' : '').'</td></tr>';
			}
		}

		if (strlen($thisWeek) || strlen($nextWeek)) {
			echo '<table class="sruDutyHours"><thead><tr><th>Administrator</th><th>Gdzie (Where)</th><th>Kiedy (When)</th></thead><tbody>';
			echo $thisWeek;
			echo $nextWeek;
			echo '</tbody></table>';
			if ($lastComment > 0) {
				echo '<div class="sruDutyHoursComments">';
				for ($i = 1; $i <= $lastComment; $i++) {
					echo '('.$i.') '.$comments[$i].'<br/>';
				}
				echo '<div>';
			}
		} else {
			if ($days > 0 ) {
				echo '<div class="sruDutyHoursComments">Żaden administrator nie ma dyżurów w ciągu nadchodzących '.$days.' dni.</div>';
			} else {
				echo '<div class="sruDutyHoursComments">Żaden administrator nie ma dziś dyżurów.</div>';
			}
		}
	}

	private function formatHour($hour) {
		return substr($hour, 0, -2).':'.substr($hour, -2);
	}
}
