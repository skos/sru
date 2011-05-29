<?
/**
 * szablon beana historii komputera
 */
class UFtpl_SruAdmin_ComputerHistory
extends UFtpl_Common {
	
	protected $computerTypes = array(
		0 => 'Niezdefiniowany staroć',
		1 => 'Student',
		2 => 'Organizacja',
		3 => 'Administracja',
		4 => 'Serwer',
	);
	
	static protected $names = array(
		'host' => 'Host',
		'mac' => 'Adres MAC',
		'ip' => 'IP',
		'locationId' => 'Miejsce',
		'availableTo' => 'Rejestracja do',
		'availableMaxTo' => 'Rejestracja max do',
		'comment' => 'Komentarz',
		'canAdmin' => 'Administrator',
		'exAdmin' => 'Ex-administrator',
		'active' => 'Aktywny',
		'typeId' => 'Typ',
	);

	static protected $namesEn = array(
		'host' => 'Host name',
		'mac' => 'MAC address',
		'ip' => 'IP',
		'locationId' => 'Room',
		'availableTo' => 'Available to',
		'availableMaxTo' => 'Available max to',
		'comment' => 'Comment',
		'canAdmin' => 'Administrator',
		'exAdmin' => 'Ex-administrator',
		'active' => 'Active',
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
				case 'host':
				case 'mac':
				case 'ip':
					$changes[] = $names[$key].': '.$val.$arr.$new[$key];
					break;
				case 'locationId':
					$changes[] = $names[$key].': '.$old['locationAlias'].'<small>&nbsp;('.$old['dormitoryAlias'].')</small>'.$arr.$new['locationAlias'].'<small>&nbsp;('.$new['dormitoryAlias'].')</small>';
					break;
				case 'availableTo':
				case 'availableMaxTo':
					$changes[] = $names[$key].': '.date(self::TIME_YYMMDD, $val).$arr.date(self::TIME_YYMMDD, $new[$key]);
					break;
				case 'comment':
					$changes[] = $names[$key].': <q>'.nl2br($val).'</q>'.$arr.'<q>'.nl2br($new[$key]).'</q>';
					break;
				case 'canAdmin':
				case 'exAdmin':
				case 'active':
					$changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie');
					break;
				case 'typeId':
					$changes[] = $names[$key].': '.$this->computerTypes[$old['typeId']].$arr.$this->computerTypes[$new['typeId']];
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
			'exAdmin' => $current->exAdmin,
			'active' => $current->active,
			'typeId' => $current->typeId,
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
			$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedBy']).'</a>';
		}
		echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';
	}

	public function mail(array $d, $new, $names=null) {
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
				case 'host':
				case 'mac':
				case 'ip':
					echo $names[$key].': '.$val
						.($val!==$new[$key] ? $arr.$new[$key] : '')
						."\n";
					break;
				case 'locationId':
					echo $names[$key].': '.$old['locationAlias'].' ('.$old['dormitoryAlias'].')'
						.($val!==$new[$key] ? $arr.$new['locationAlias'].' ('.$new['dormitoryAlias'].')' : '')
						."\n";
					break;
				case 'availableTo':
					echo $names[$key].': '.date(self::TIME_YYMMDD, $val)
						.($val!==$new[$key] ? $arr.date(self::TIME_YYMMDD, $new[$key]) : '')
						."\n";
					break;
				default: continue;
			}
		}
	}

	public function mailEn(array $d, $new, $names=null) {
		$this->mail($d, $new, self::$namesEn);
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		$displayed = array();
		foreach ($d as $c) {
			if (in_array($c['host'], $displayed)) {
				continue;
			}
			if ($c['ip'] == $c['currentIp']) {
				continue;
			}
			$owner = '(Należał do: <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>)';
			echo '<li'.($c['currentBanned'] ? ' class="ban"' : '').'>'.(!$c['currentActive']?'<del>':'').'<a href="'.$url.'/computers/'.$c['computerId'].'">'.$c['host'].(strlen($c['currentComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['currentComment'].'" />':'').' <small>'.$c['currentIp'].'/'.$c['mac'].'</small></a> <span>'.$owner.'</span>'.(!$c['currentActive']?'</del>':'').'</li>';
			$displayed[] = $c['host'];
		}
		if (count($displayed) == 0) {
			echo 'Wszystkie komputery używające wcześniej tego IP zostały wyświetlone na liście wyżej.';
		}
	}
}
