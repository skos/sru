<?
/**
 * szablon beana historii uzytkownika
 */
class UFtpl_SruAdmin_UserHistory
extends UFtpl_Common {

	const PL = 'pl';
	const EN = 'en';

	static protected $names = array(
		'login' => 'Login',
		'name' => 'Imię',
		'surname' => 'Nazwisko',
		'email' => 'E-mail',
		'gg' => 'Gadu-Gadu',
		'facultyId' => 'Wydział',
		'locationId' => 'Miejsce',
		'studyYearId' => 'Rok studiów',
		'comment' => 'Komentarz',
		'active' => 'Aktywny',
		'referralStart' => 'Początek skierowania',
		'referralEnd' => 'Koniec skierowania',
		'registryNo' => 'Nr indeksu',
		'servicesAvailable' => 'Dostępność PUU',
		'updateNeeded' => 'Konieczna aktualizacja profilu',
		'changePasswordNeeded' => 'Konieczna zmiana hasła',
		'passwordChanged' => 'Zmieniono hasło',
		'lang' => 'Język',
		'typeId' => 'Typ',
	);

	static protected $namesEn = array(
		'login' => 'Login',
		'name' => 'Name',
		'surname' => 'Surname',
		'email' => 'E-mail',
		'gg' => 'Gadu-Gadu',
		'facultyId' => 'Faculty',
		'locationId' => 'Room',
		'studyYearId' => 'Year of study',
		'comment' => 'Comment',
		'active' => 'Active',
		'referralStart' => 'Referral start',
		'referralEnd' => 'Refferal end',
		'registryNo' => 'Registry No.',
		'servicesAvailable' => 'PUU availability',
		'updateNeeded' => 'Profile update needed',
		'changePasswordNeeded' => 'Password change needed',
		'passwordChanged' => 'Password changed',
		'lang' => 'Language',
		'typeId' => 'Type',
	);

	protected function _diff(array $old, array $new) {
		$changes = array();
		$arr = ' &rarr; ';
		$names = self::$names;
		foreach ($old as $key=>$val) {
			if (!array_key_exists($key, $new) || $val === $new[$key]) {
				continue;
			}
			switch ($key) {
				case 'login': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'name': $changes[] = $names[$key].': '.$this->_escape($val).$arr.$this->_escape($new[$key]); break;
				case 'surname': $changes[] = $names[$key].': '.$this->_escape($val).$arr.$this->_escape($new[$key]); break;
				case 'registryNo': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'email': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'gg': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'facultyId':
					$oldF = is_null($old['facultyName'])?'N/D':$old['facultyName'];
					$newF = is_null($new['facultyName'])?'N/D':$new['facultyName'];
					$changes[] = $names[$key].': '.$oldF.$arr.$newF;
					break;
				case 'locationId': $changes[] = $names[$key].': '.$old['locationAlias'].'<small>&nbsp;('.$old['dormitoryAlias'].')</small>'.$arr.$new['locationAlias'].'<small>&nbsp;('.$new['dormitoryAlias'].')</small>'; break;
				case 'studyYearId': $changes[] = $names[$key].': '. UFtpl_Sru_User::$studyYears[$val].$arr.UFtpl_Sru_User::$studyYears[$new[$key]]; break;
				case 'comment': $changes[] = $names[$key].': <q>'.nl2br($val).'</q>'.$arr.'<q>'.nl2br($new[$key]).'</q>'; break;
				case 'active': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'referralStart': $changes[] = $names[$key].': <q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.($new[$key] == 0 ? 'brak' : date(self::TIME_YYMMDD, $new[$key])).'</q>'; break;
				case 'referralEnd': $changes[] = $names[$key].': <q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.($new[$key] == 0 ? 'brak' : date(self::TIME_YYMMDD, $new[$key])).'</q>'; break;
				case 'servicesAvailable': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'updateNeeded': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'changePasswordNeeded': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'passwordChanged': $val > 0 ? ($changes[] = $names[$key]) : ''; break;
				case 'lang': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'typeId':
					$changes[] = $names[$key].': '.UFtpl_Sru_User::getUserType($old['typeId']).$arr.UFtpl_Sru_User::getUserType($new['typeId']);
					break;
				default: continue;
			}
		}
		if (!count($changes)) {
			return '';
		}
		$return = '';
		foreach ($changes as $c) {
			$return .= '<li>'.$c.'</li>';
		}
		return '<ul>'.$return.'</ul>';
	}

	public function table(array $d, $current, $walet = false) {
		$curr = array(
			'login' => $current->login,
			'name' => $current->name,
			'surname' => $current->surname,
			'email' => $current->email,
			'gg' => $current->gg,
			'facultyId' => $current->facultyId,
			'facultyName' => $current->facultyName,
			'facultyAlias' => $current->facultyAlias,
			'studyYearId' => $current->studyYearId,
			'locationId' => $current->locationId,
			'locationAlias' => $current->locationAlias,
			'dormitoryId' => $current->dormitoryId,
			'dormitoryAlias' => $current->dormitoryAlias,
			'dormitoryName' => $current->dormitoryName,
			'modifiedById' => $current->modifiedById,
			'modifiedBy' => $current->modifiedBy,
			'modifiedAt' => $current->modifiedAt,
			'comment' => $current->comment,
			'active' => $current->active,
			'referralStart' => $current->referralStart,
			'referralEnd' => $current->referralEnd,
			'registryNo' => $current->registryNo,
			'servicesAvailable' => $current->servicesAvailable,
			'updateNeeded' => $current->updateNeeded,
			'changePasswordNeeded' => $current->changePasswordNeeded,
			'passwordChanged' => '0',
			'lang' => $current->lang,
			'typeId' => $current->typeId,
		);
		$url = $this->url(0).'/users/'.$current->id;
		$urlAdmin = $this->url(0).'/admins/';
		foreach ($d as $c) {
			echo '<li>';
			if (is_null($curr['modifiedBy'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedBy']).'</a>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
			echo $this->_diff($c, $curr);
			if (!$walet) {
				echo '<p><a href="'.$url.'/:edit/'.$c['id'].'">Cofnij zmiany</a></p>';
			}
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		if (is_null($curr['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedBy']).'</a>';
		}
		echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';
	}

	public function mail(array $d, $new, $names=null, $lang = self::PL) {
		foreach ($d as $old) {
			break;
		}
		if (is_null($names)) {
			$names = self::$names;
		}
		$changes = array();
		$arr = ' => ';
		foreach ($old as $key=>$val) {
			switch ($key) {
				case 'login':
				case 'name':
				case 'surname':
				case 'registryNo':
				case 'gg':
				case 'email':
					echo $names[$key].': '.$val
						.($val!==$new[$key] ? $arr.$new[$key] : '')
						."\n";
					break;
				case 'locationId':
					if ($lang == self::EN) {
						echo $names[$key].': '.$old['locationAlias'].' ('.$old['dormitoryNameEn'].')'
							.($val!==$new[$key] ? $arr.$new['locationAlias'].' ('.$new['dormitoryNameEn'].')' : '')
							."\n";
					} else {
						echo $names[$key].': '.$old['locationAlias'].' ('.$old['dormitoryName'].')'
							.($val!==$new[$key] ? $arr.$new['locationAlias'].' ('.$new['dormitoryName'].')' : '')
							."\n";
					}
					break;
				case 'facultyId':
					if ($lang == self::EN) {
						echo $names[$key].': '.$old['facultyNameEn']
							.($val!==$new[$key] ? $arr.$new['facultyNameEn'] : '')
							."\n";
					} else {
						echo $names[$key].': '.$old['facultyName']
							.($val!==$new[$key] ? $arr.$new['facultyName'] : '')
							."\n";
					}
					break;
				case 'studyYearId':
					echo $names[$key].': '. UFtpl_Sru_User::$studyYears[$val]
						.($val!==$new[$key] ? $arr.UFtpl_Sru_User::$studyYears[$new[$key]] : '')
						."\n";
						break;
				case 'active':
					$newA = $new['active'] ? 'tak' : 'nie';
					if ($lang == self::EN) {
						echo $names[$key].': '.($old['active'] ? 'yes' : 'no')
							.($val!==$new[$key] ? $arr.($new['active'] ? 'yes' : 'no') : '')
							."\n"
							.'Only dormitory administration can change activity of your account! In case of problems, please contact your dormitory office.'."\n";
					} else {
						echo $names[$key].': '.($old['active'] ? 'tak' : 'nie')
							.($val!==$new[$key] ? $arr.$newA : '')
							."\n"
							.'Tylko administracja DSu może zmienić aktywność konta! W razie problemów prosimy o kontakt z kierownictwem DSu.'."\n";
					}
					break;
				default: continue;
			}
		}
	}

	public function mailEn(array $d, $new, $names=null) {
		$this->mail($d, $new, self::$namesEn, self::EN);
	}
}
