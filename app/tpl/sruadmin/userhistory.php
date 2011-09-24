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
		'address' => 'Adres',
		'documentType' => 'Typ dokumentu',
		'documentNumber' => 'Nr dokumentu',
		'nationality' => 'Narodowość',
		'pesel' => 'PESEL',
		'birthDate'	=> 'Data urodzenia',
		'birthPlace' => 'Miejsce urodzenia',
		'userPhoneNumber' => 'Tel. mieszkańca',
		'guardianPhoneNumber' => 'Tel. opiekuna',
		'sex' => 'Płeć',
		'lastLocationChange' => 'Przemeldowanie',
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
		'address'		=> 'Address',
		'documentType'	=> 'Document type',
		'documentNumber'=> 'Document number',
		'nationality'	=> 'Nationality',
		'pesel'			=> 'PESEL',
		'birthDate'		=> 'Birth date',
		'birthPlace'	=> 'Birth place',
		'userPhoneNumber'	=> 'User phone number',
		'guardianPhoneNumber'	=> 'Guardian phone number',
		'sex'			=> 'Sex',
		'lastLocationChange' => 'Last check-in/out',
	);

	protected function _diff(array $old, array $new, $walet) {
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
					$oldF = is_null($old['facultyName'])?'':$old['facultyName'];
					$newF = $new['facultyName'];
					$changes[] = $names[$key].': '.$oldF.$arr.$newF;
					break;
				case 'locationId': $changes[] = $names[$key].': '.$old['locationAlias'].'<small>&nbsp;('.$old['dormitoryAlias'].')</small>'.$arr.$new['locationAlias'].'<small>&nbsp;('.$new['dormitoryAlias'].')</small>'; break;
				case 'studyYearId': $changes[] = $names[$key].': '. (is_null($val) ? '' : UFtpl_Sru_User::$studyYears[$val]).$arr.UFtpl_Sru_User::$studyYears[$new[$key]]; break;
				case 'comment': $changes[] = $names[$key].': <q>'.nl2br($val).'</q>'.$arr.'<q>'.nl2br($new[$key]).'</q>'; break;
				case 'active': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'referralStart': $changes[] = $names[$key].': <q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.($new[$key] == 0 ? 'brak' : date(self::TIME_YYMMDD, $new[$key])).'</q>'; break;
				case 'referralEnd': $changes[] = $names[$key].': <q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.($new[$key] == 0 ? 'brak' : date(self::TIME_YYMMDD, $new[$key])).'</q>'; break;
				case 'lastLocationChange': $changes[] = $names[$key].': <q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.($new[$key] == 0 ? 'brak' : date(self::TIME_YYMMDD, $new[$key])).'</q>'; break;
				case 'servicesAvailable': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'updateNeeded': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'changePasswordNeeded': $changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
				case 'passwordChanged': $val > 0 ? ($changes[] = $names[$key]) : ''; break;
				case 'lang': $changes[] = $names[$key].': '.$val.$arr.$new[$key]; break;
				case 'typeId':
					$changes[] = $names[$key].': '.UFtpl_Sru_User::getUserType($old['typeId']).$arr.UFtpl_Sru_User::getUserType($new['typeId']);
					break;
				case 'nationality': $changes[] = $names[$key] . ': '.$old['nationalityName'].$arr.$new['nationalityName']; break;
				case 'sex': $changes[] = $names[$key] . ': <q>'.($val == false ? 'Mężczyzna' : 'Kobieta').'</q>'.$arr.'<q>'.($new[$key] == false ? 'Mężczyzna' : 'Kobieta').'</q>'; break;
				case 'address': $changes[] = $names[$key] . ': '.($walet ? $old['address'].$arr.$new[$key] : 'zmieniono'); break;
				case 'documentType': $changes[] = $names[$key] . ': '.($walet ? UFtpl_Sru_User::$documentTypes[$old[$key]].$arr.UFtpl_Sru_User::$documentTypes[$new[$key]] : 'zmieniono'); break;
				case 'documentNumber': $changes[] = $names[$key] . ': '.($walet ? $old[$key].$arr.$new[$key] : 'zmieniono'); break;
				case 'pesel': $changes[] = $names[$key] . ': '.($walet ? ($val == '' ? 'brak' : $old[$key]).$arr.$new[$key] : 'zmieniono'); break;
				case 'birthDate': $changes[] = $names[$key] . ': '.($walet ? ('<q>'.($val == 0 ? 'brak' : date(self::TIME_YYMMDD, $val)).'</q>'.$arr.'<q>'.date(self::TIME_YYMMDD, $new[$key]).'</q>') : 'zmieniono'); break;
				case 'birthPlace': $changes[] = $names[$key] . ': '.($walet ? ('<q>'.($val =='' ? 'brak' : $old[$key]).'</q>'.$arr.'<q>'.$new[$key]) : 'zmieniono').'</q>'; break;
				case 'userPhoneNumber': $changes[] = $names[$key] . ': '.($walet ? ($val == '' ? 'brak' : $old[$key]).$arr.$new[$key] : 'zmieniono'); break;
				case 'guardianPhoneNumber': $changes[] = $names[$key] . ': '.($walet ? ($val == '' ? 'brak' : $old[$key]).$arr.$new[$key] : 'zmieniono'); break;
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
			'nationality' => $current->nationality,
			'sex' => $current->sex,
			'address' => $current->address,
			'documentType' => $current->documentType,
			'documentNumber' => $current->documentNumber,
			'nationalityName' => $current->nationalityName,
			'pesel' => $current->pesel,
			'birthDate' => $current->birthDate,
			'birthPlace' => $current->birthPlace,
			'userPhoneNumber' => $current->userPhoneNumber,
			'guardianPhoneNumber' => $current->guardianPhoneNumber,
			'lastLocationChange' => $current->lastLocationChange,
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
			echo $this->_diff($c, $curr, $walet);
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

		if ($old['active'] !== $new['active'] && !$new['active']) {
			if ($lang == self::EN) {
				echo "\n".'Your account has been DEACTIVATED by your dormitory administration. In case of problems, please contact your dormitory office.'."\n\n";
			} else {
				echo "\n".'Twoje konto zostało DEZAKTYWOWANE przez administrację Twojego DSu. W razie problemów prosimy o kontakt z administracją DSu.'."\n\n";
			}
		}

		foreach ($old as $key=>$val) {
			switch ($key) {
				case 'login':
					echo $names[$key].': '.$val
						.($val!==$new[$key] ? $arr.$new[$key] : '')
						."\n";
					break;
				case 'name':
				case 'surname':
				case 'registryNo':
					echo $names[$key].'*: '.$val
						.($val!==$new[$key] ? $arr.$new[$key] : '')
						."\n";
					break;
				case 'active':
					$newA = $new['active'] ? 'tak' : 'nie';
					if ($lang == self::EN) {
						echo $names[$key].'*: '.($old['active'] ? 'yes' : 'no')
							.($val!==$new[$key] ? $arr.($new['active'] ? 'yes' : 'no') : '')
							."\n";
					} else {
						echo $names[$key].'*: '.($old['active'] ? 'tak' : 'nie')
							.($val!==$new[$key] ? $arr.$newA : '')
							."\n";
					}
					break;
				case 'gg':
				case 'email':
					echo $names[$key].': '.$val
						.($val!==$new[$key] ? $arr.$new[$key] : '')
						."\n";
					break;
				case 'locationId':
					if ($lang == self::EN) {
						echo $names[$key].'*: '.$old['locationAlias'].' ('.$old['dormitoryNameEn'].')'
							.($val!==$new[$key] ? $arr.$new['locationAlias'].' ('.$new['dormitoryNameEn'].')' : '')
							."\n";
					} else {
						echo $names[$key].'*: '.$old['locationAlias'].' ('.$old['dormitoryName'].')'
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
				default: continue;
			}
		}
		if ($lang == self::EN) {
			echo "\n".'*) Only dormitory administration can change these data. In case of problems, please contact your dormitory office.'."\n";
		} else {
			echo "\n".'*) Te dane konta mogą zostać zmienione wyłącznie przez administrację DSu. W razie problemów prosimy o kontakt z administracją DSu.'."\n";
		}
	}

	public function mailEn(array $d, $new, $names=null) {
		$this->mail($d, $new, self::$namesEn, self::EN);
	}
}
