<?
/**
 * szablon godzin dyżurów
 */
class UFtpl_SruAdmin_DutyHours
extends UFtpl_Common {

	protected static $dayNames = array(
		1 => 'Poniedziałek',
		2 => 'Wtorek',
		3 => 'Środa',
		4 => 'Czwartek',
		5 => 'Piątek',
		6 => 'Sobota',
		7 => 'Niedziela',
	);

	public function listDutyHours(array $d) {
		echo '<ul>';
		foreach ($d as $c) {
			echo '<li>'.($c['active'] ? '' : '<del>').self::$dayNames[$c['day']].': '.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).($c['active'] ? '' : '</del>').(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').'</li>';
		}
		echo '</ul>';
	}

	public function apiAllDutyHours(array $d) {
		$currentDay = date('N');
		$lastAdmin = '';
		$lastDay = 0;
		$comments = array();
		$lastComment = 0;

		echo '<table class="sruDutyHours"><thead><tr><th>Akademik<br/>(Dormitory)</th><th>Administrator</th><th>Gdzie<br/>(Where)</th><th>E-mail</th><th>Poniedziałek<br/>(Monday)</th><th>Wtorek<br/>(Tuesday)</th><th>Środa<br/>(Wednesday)</th><th>Czwartek<br/>(Thursday)</th><th>Piątek<br/>(Friday)</th><th>Sobota<br/>(Saturday)</th><th>Niedziela<br/>(Sunday)</th></tr></thead><tbody>';
		foreach ($d as $c) {
			if ($c['adminName'] != $lastAdmin && $lastAdmin != '') {
				for ($i = $lastDay; $i < 7; $i++) {
					echo '<td></td>';
				}
				echo '</tr>';
			}
			if ($c['adminName'] != $lastAdmin) {
				echo '<tr><td>'.strtoupper($c['adminDormAlias']).'</td><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td>'.$c['adminEmail'].'</td>';
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
			echo '<td'.($c['day'] == $currentDay ? ' class="sruDutyHoursCurrentDay"' : '').'>'.($c['active'] ? '' : '<del>').$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).($c['active'] ? '' : '</del>').(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td>';
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
			echo '/<div>';
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
				if ($c['day'] == $currentDay && $days == 0) {
					$dayName = '';
				} else if ($c['day'] == $currentDay) {
					$dayName = 'dziś ';
				} else if ($c['day'] == $currentDay + 1) {
					$dayName = 'jutro ';
				} else {
					$dayName = self::$dayNames[$c['day']].' ';
				}
				$thisWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
			}
			if ($c['day'] <= $lastDay - 7) {
				if (strlen($c['comment'])) {
					$lastComment++;
					$comments[$lastComment] = $c['comment'];
				}
				if ($c['day'] == $currentDay - 7 + 1) { // minus tydzień plus jeden dzień
					$dayName = 'jutro ';
				} else {
					$dayName = self::$dayNames[$c['day']].' ';
				}
				$nextWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
			}
		}

		if (strlen($thisWeek) || strlen($nextWeek)) {
			echo '<table class="sruDutyHours"><thead><tr><th>Administrator</th><th>Gdzie<br/>(Where)</th><th>Kiedy<br/>(When)</th></tr></thead><tbody>';
			echo $thisWeek;
			echo $nextWeek;
			echo '</tbody></table>';
			if ($lastComment > 0) {
				echo '<div class="sruDutyHoursComments">';
				for ($i = 1; $i <= $lastComment; $i++) {
					echo '('.$i.') '.$comments[$i].'<br/>';
				}
				echo '</div>';
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

	public static function getDayName($id) {
		return self::$dayNames[$id];
	}
}
