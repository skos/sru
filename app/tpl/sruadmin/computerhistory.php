<?
/**
 * szablon beana historii komputera
 */
class UFtpl_SruAdmin_ComputerHistory
extends UFtpl_Common {

	protected function _diff(array $old, array $new) {
		$changes = array();
		$arr = ' &rarr; ';
		foreach ($old as $key=>$val) {
			if (!array_key_exists($key, $new) || $val === $new[$key]) {
				continue;
			}
			switch ($key) {
				case 'host': $changes[] = 'Host: '.$val.$arr.$new[$key]; break;
				case 'mac': $changes[] = 'MAC: '.$val.$arr.$new[$key]; break;
				case 'ip': $changes[] = 'IP: '.$val.$arr.$new[$key]; break;
				case 'locationId': $changes[] = 'Miejsce: '.$old['locationAlias'].'<small>&nbsp;('.$old['dormitoryAlias'].')</small>'.$arr.$new['locationAlias'].'<small>&nbsp;('.$new['dormitoryAlias'].')</small>'; break;
				case 'availableTo': $changes[] = 'Rejestracja do: '. date(self::TIME_YYMMDD, $val).$arr. date(self::TIME_YYMMDD, $new[$key]); break;
				case 'availableMaxTo': $changes[] = 'Rejestracja max do: '. date(self::TIME_YYMMDD, $val).$arr. date(self::TIME_YYMMDD, $new[$key]); break;
				case 'comment': $changes[] = 'Komentarz: <q>'.$val.'</q>'.$arr.'<q>'.$new[$key].'</q>'; break;
				case 'canAdmin': $changes[] = 'Administrator: '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie'); break;
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
			'host' => $current->host,
			'mac' => $current->mac,
			'ip' => $current->ip,
			'userId' => $current->userId,
			'locationId' => $current->locationId,
			'locationAlias' => $current->locationAlias,
			'dormitoryId' => $current->dormitoryId,
			'dormitoryAlias' => $current->dormitoryAlias,
			'dormitoryName' => $current->dormitoryName,
			'availableTo' => $current->availableTo,
			'availableMaxTo' => $current->availableMaxTo,
			'modifiedById' => $current->modifiedById,
			'modifiedBy' => $current->modifiedBy,
			'modifiedAt' => $current->modifiedAt,
			'comment' => $current->comment,
			'canAdmin' => $current->canAdmin,
		);
		$url = $this->url(0).'/computers/'.$current->id;
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
			echo '<p><a href="'.$url.'/:edit/'.$c['id'].'">Cofnij zmiany</a></p>';
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		if (is_null($curr['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedBy']).'</a>';
		}
		echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';
	}
}
