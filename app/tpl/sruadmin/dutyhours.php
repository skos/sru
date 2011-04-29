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

	public function apiAllDutyHours(array $d, $dormitories) {
		$currentDay = date('N');
		$lastDay = 0;
		$comments = array();
		$lastComment = 0;

		$admins = array();
		$lastAdmin = 0;
		foreach ($d as $c) {
			if ($c['adminId'] != $lastAdmin && $lastAdmin != '') {
				for ($i = $lastDay; $i < 7; $i++) {
					$admins[$lastAdmin] .= '<td></td>';
				}
				$admins[$lastAdmin] .= '</tr>';
			}
			if ($c['adminId'] != $lastAdmin) {
				$admins[$c['adminId']] = '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td><td><a href="mailto:'.$c['adminEmail'].'">'.$c['adminEmail'].'</a></td>';
				$lastAdmin = $c['adminId'];
				$lastDay = 0;
			}
			for ($i = $lastDay; $i < $c['day'] - 1; $i++) {
				$admins[$c['adminId']] .= '<td></td>';
			}
			if (strlen($c['comment'])) {
				$lastComment++;
				$comments[$lastComment] = $c['comment'];
			}
			$admins[$c['adminId']] .= '<td'.($c['day'] == $currentDay ? ' class="sruDutyHoursCurrentDay"' : '').'>'.($c['active'] ? '' : '<del>').$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).($c['active'] ? '' : '</del>').(strlen($c['comment']) ? ' <span title="'.$c['comment'].'" class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td>';
			$lastDay = $c['day'];
		}
		for ($i = $lastDay; $i < 7; $i++) {
			$admins[$lastAdmin] .= '<td></td>';
		}
		$admins[$lastAdmin] .= '</tr>';
		
		echo '<table class="sruDutyHours"><thead><tr><th>Administrator</th><th>Gdzie<br/>(Where)</th><th>E-mail</th><th>Poniedziałek<br/>(Monday)</th><th>Wtorek<br/>(Tuesday)</th><th>Środa<br/>(Wednesday)</th><th>Czwartek<br/>(Thursday)</th><th>Piątek<br/>(Friday)</th><th>Sobota<br/>(Saturday)</th><th>Niedziela<br/>(Sunday)</th></tr></thead><tbody>';
		foreach ($dormitories as $dorm) {
			$dormAdmins = array();
			foreach ($dorm as $admin) {
				$dormAdmins[] = $admin['admin'];
			}
			if (empty($dormAdmins)) {
				continue;
			}
			echo '<tr><td colspan="10" class="sruDutyHoursDormitoryName">'.$dorm[0]['dormitoryName'].' (<a href="mailto:admin-'.$dorm[0]['dormitoryAlias'].'@ds.pg.gda.pl">admin-'.$dorm[0]['dormitoryAlias'].'@ds.pg.gda.pl</a>)</td></tr>';
			foreach ($dormAdmins as $admin) {
					echo $admins[$admin];
			}
		}
		echo '</tr></tbody></table>';
		if ($lastComment > 0) {
			echo '<div class="sruDutyHoursComments">';
			for ($i = 1; $i <= $lastComment; $i++) {
				echo '('.$i.') '.$comments[$i].'<br/>';
			}
			echo '</div>';
		}
	}

	public function apiUpcomingDutyHours(array $d, $days, $dormitories) {
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
				$thisWeek .=  '<tr><td>'.$c['adminName'].'</td><td><a href="mailto:'.$c['adminAddress'].'">'.$c['adminAddress'].'</a></td><td>'.$this->listDorms($c['adminId'], $dormitories).'</td><td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
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
				$nextWeek .=  '<tr><td>'.$c['adminName'].'</td><td><a href="mailto:'.$c['adminAddress'].'">'.$c['adminAddress'].'</a></td><td>'.$this->listDorms($c['adminId'], $dormitories).'</td><td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
			}
		}

		if (strlen($thisWeek) || strlen($nextWeek)) {
			echo '<table class="sruDutyHoursUpcoming"><thead><tr><th>Administrator</th><th>Gdzie<br/>(Where)</th><th>Akademiki<br/>(Dorms)</th><th>Kiedy<br/>(When)</th></tr></thead><tbody>';
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

	private function listDorms($id, $dorms) {
		if(is_null($dorms[$id])){
			return '-';
		} else {
			$list = '';
			foreach($dorms[$id] as $dorm){
				$list .= strtoupper($dorm['dormitoryAlias']).', ';
			}
			return substr($list, 0, -2);
		}
	}
}
