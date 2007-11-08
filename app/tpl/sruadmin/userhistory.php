<?
/**
 * szablon beana historii uzytkownika
 */
class UFtpl_SruAdmin_UserHistory
extends UFtpl {

	protected function _diff(array $old, array $new) {
		$changes = array();
		$arr = ' &rarr; ';
		foreach ($old as $key=>$val) {
			if (!array_key_exists($key, $new) || $val === $new[$key]) {
				continue;
			}
			switch ($key) {
				case 'login': $changes[] = 'Login: '.$val.$arr.$new[$key]; break;
				case 'name': $changes[] = 'Imię: '.$val.$arr.$new[$key]; break;
				case 'surname': $changes[] = 'Nazwisko: '.$val.$arr.$new[$key]; break;
				case 'email': $changes[] = 'E-mail: '.$val.$arr.$new[$key]; break;
				case 'facultyId': $changes[] = 'Wydział: '.$old['facultyAlias'].$arr.$new['facultyAlias']; break;
				case 'locationId': $changes[] = 'Miejsce: '.$old['locationAlias'].'<small>&nbsp;('.$old['dormitoryAlias'].')</small>'.$arr.$new['locationAlias'].'<small>&nbsp;('.$new['dormitoryAlias'].')</small>'; break;
				case 'studyYearId': $changes[] = 'Rok studiów: '. UFtpl_Sru_User::$studyYears[$val].$arr.UFtpl_Sru_User::$studyYears[$new[$key]]; break;
				case 'comment': $changes[] = 'Komentarz: <q>'.$val.'</q>'.$arr.'<q>'.$new[$key].'</q>'; break;
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

	public function table(array $d, $current) {
		$curr = array(
			'login' => $current->login,
			'name' => $current->name,
			'surname' => $current->surname,
			'email' => $current->email,
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
		);
		$url = $this->url(0).'/users/'.$current->id;
		$urlAdmin = $this->url(0).'/admins';
		foreach ($d as $c) {
			echo '<li>';
			if (is_null($curr['modifiedBy'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="'.$urlAdmin.'/'.$curr['modifiedById'].'">'.$curr['modifiedBy'].'</a>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
			echo $this->_diff($c, $curr);
			echo '<p><a href="'.$url.'/:edit/'.$c['id'].'">Cofnij zmiany</a></p>';
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		if (is_null($curr['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$curr['modifiedById'].'">'.$curr['modifiedBy'].'</a>';
		}
		echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';
	}
}
