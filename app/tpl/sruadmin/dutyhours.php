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
	
	protected static $dayNamesEn = array(
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thursday',
		5 => 'Friday',
		6 => 'Saturday',
		7 => 'Sunday',
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
		$allDormAdmins = array();
		foreach ($dormitories as $dorm) {
			if (is_null($dorm)) continue;
			foreach ($dorm as $admin) {
				$allDormAdmins[$admin['admin']] = 1;
			}
		}
		
		foreach ($d as $c) {
			if ($c['adminId'] != $lastAdmin && $lastAdmin != '') {
				for ($i = $lastDay; $i < 7; $i++) {
					$admins[$lastAdmin] .= '<td></td>';
				}
				$admins[$lastAdmin] .= '</tr>';
			}
			if ($c['adminId'] != $lastAdmin) {
				$admins[$c['adminId']] = '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td>';
				$lastAdmin = $c['adminId'];
				$lastDay = 0;
			}
			for ($i = $lastDay; $i < $c['day'] - 1; $i++) {
				$admins[$c['adminId']] .= '<td></td>';
			}
			if (strlen($c['comment']) && array_key_exists($c['adminId'], $allDormAdmins)) {
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
		
		echo '<table class="sruDutyHours"><thead><tr><th>Administrator</th><th>Gdzie<br/>(Where)</th><th>Poniedziałek<br/>(Monday)</th><th>Wtorek<br/>(Tuesday)</th><th>Środa<br/>(Wednesday)</th><th>Czwartek<br/>(Thursday)</th><th>Piątek<br/>(Friday)</th><th>Sobota<br/>(Saturday)</th><th>Niedziela<br/>(Sunday)</th></tr></thead><tbody>';
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
				if (array_key_exists($admin, $admins)) {
					echo $admins[$admin];
				}
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

	public function apiUpcomingDutyHours(array $d, $days, $dormitories, $html = true) {
		$currentDay = date('N');
		$lastDay = $currentDay + $days;
		$thisWeek = '';
		$nextWeek = '';
		$comments = array();
		$lastComment = 0;
		$allDormAdmins = array();
                if($d['user']->lang == 'en')
                        $en=true;
                else
                        $en=false;

		if (!is_null($dormitories)) {
			foreach ($dormitories as $dorm) {
				if (is_null($dorm)) continue;
				foreach ($dorm as $admin) {
					$allDormAdmins[$admin['admin']] = 1;
				}
			}
		}

		foreach ($d as $c) {
			if (!array_key_exists($c['adminId'], $allDormAdmins) && !is_null($dormitories)) {
				continue;
			}
			if (($c['day'] == $currentDay && $c['endHour'] > date('Hi')) || ($c['day'] > $currentDay && $c['day'] <= $lastDay)) {
				if (strlen($c['comment'])) {
					$lastComment++;
					$comments[$lastComment] = $c['comment'];
				}
				if ($c['day'] == $currentDay && $days == 0) {
					$dayName = '';
				} else if ($c['day'] == $currentDay) {
					if ($en) {
						$dayName = 'today ';
					} else {
						$dayName = 'dziś ';
					}
				} else if ($c['day'] == $currentDay + 1) {
					if ($en) {
						$dayName = 'tomorrow ';
					} else {
						$dayName = 'jutro ';
					}
				} else {
					if ($en) {
						$dayName = self::$dayNamesEn[$c['day']].' ';
					} else {
						$dayName = self::$dayNames[$c['day']].' ';
					}
				}
				if ($html) {
					$thisWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td>';
					if (!is_null($dormitories)) {
						$thisWeek .= '<td>'.$this->listDorms($c['adminId'], $dormitories).'</td>';
					}
					$thisWeek .= '<td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
				} else {
					$thisWeek .= $c['adminName'].' ('.$c['adminAddress'].'): '.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' ('.$lastComment.')' : '')."\n";
				}
			}
			if ($c['day'] <= $lastDay - 7) {
				if (strlen($c['comment'])) {
					$lastComment++;
					$comments[$lastComment] = $c['comment'];
				}
				if ($c['day'] == $currentDay - 7 + 1) { // minus tydzień plus jeden dzień
					if ($en) {
						$dayName = 'tomorrow ';
					} else {
						$dayName = 'jutro ';
					}
				} else {
					if ($en) {
						$dayName = self::$dayNamesEn[$c['day']].' ';
					} else {
						$dayName = self::$dayNames[$c['day']].' ';
					}
				}
				if ($html) {
					$nextWeek .=  '<tr><td>'.$c['adminName'].'</td><td>'.$c['adminAddress'].'</td>';
					if (!is_null($dormitories)) {
						$nextWeek .= '<td>'.$this->listDorms($c['adminId'], $dormitories).'</td>';
					}
					$nextWeek .= '<td>'.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' <span class="sruDutyHoursCommentIndex">('.$lastComment.')</span>' : '').'</td></tr>';
				} else {
					$nextWeek .= $c['adminName'].' ('.$c['adminAddress'].'): '.$dayName.$this->formatHour($c['startHour']).'-'.$this->formatHour($c['endHour']).(strlen($c['comment']) ? ' ('.$lastComment.')' : '')."\n";
				}
			}
		}

		if ($html) {
			if (strlen($thisWeek) || strlen($nextWeek)) {
				echo '<table class="sruDutyHoursUpcoming"><thead><tr><th>Administrator</th><th>'._("Gdzie").'</th>';
				if (!is_null($dormitories)) {
					echo '<th>'.("Akademiki").'</th>';
				}
				echo '<th>'._("Kiedy").'</th></tr></thead><tbody>';
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
					if ($en) {
						echo '<div class="sruDutyHoursNoHours">No administrator has duty hours in next '.$days.' days.</div>';
					} else {
						echo '<div class="sruDutyHoursNoHours">Żaden administrator nie ma dyżurów w ciągu nadchodzących '.$days.' dni.</div>';
					}
				} else {
					if ($en) {
						echo '<div class="sruDutyHoursNoHours">No administrator has duty hours today.</div>';
					} else {
						echo '<div class="sruDutyHoursNoHours">Żaden administrator nie ma dziś dyżurów.</div>';
					}
				}
			}
		} else {
			if (strlen($thisWeek) || strlen($nextWeek)) {
				echo $thisWeek;
				echo $nextWeek;
				if ($lastComment > 0) {
					for ($i = 1; $i <= $lastComment; $i++) {
						echo '('.$i.') '.$comments[$i]."\n";
					}
				}
			} else {
				if ($days > 0 ) {
					if ($en) {
						echo 'No administrator has duty hours in next '.$days.' days.';
					} else {
						echo 'Żaden administrator nie ma dyżurów w ciągu nadchodzących '.$days.' dni.';
					}
				} else {
					if ($en) {
						echo 'Żaden administrator nie ma dziś dyżurów.';
					} else {
						echo 'Żaden administrator nie ma dziś dyżurów.';
					}
				}
			}
		}
	}

	public function upcomingDutyHours(array $d, $user, $days) {
		echo '<h3>Adres e-mail do wszystkich administratorów w DSie:<br/><a href="mailto:admin-'.$user->dormitoryAlias.'@ds.pg.gda.pl">admin-'.$user->dormitoryAlias.'@ds.pg.gda.pl</a>.</h3>';
		$this->apiUpcomingDutyHours($d, $days, null);
	}

	public function upcomingDutyHoursToEmailPolish(array $d, $user, $days) {
		echo 'Adres e-mail do wszystkich administratorów SKOS opiekujących się Twoim DSem: admin-'.(is_array($user) ? $user['dormitoryAlias'] : $user->dormitoryAlias).'@ds.pg.gda.pl'."\n";
		echo 'Najbliższe dyżury Twoich administratorów:'."\n";
		$this->apiUpcomingDutyHours($d, $days, null, false);
	}

	public function upcomingDutyHoursToEmailEnglish(array $d, $user, $days) {
		echo 'The e-mail address to all administrators in your dormitory: admin-'.(is_array($user) ? $user['dormitoryAlias'] : $user->dormitoryAlias).'@ds.pg.gda.pl'."\n";
		echo 'The next duty hours of your SKOS administrators:'."\n";
		$this->apiUpcomingDutyHours($d, $days, null, false, true);
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
