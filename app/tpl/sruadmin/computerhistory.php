<?
/**
 * szablon beana historii komputera
 */
class UFtpl_SruAdmin_ComputerHistory
extends UFtpl_Common {	
	static protected $names = array(
		'host' => 'Host',
		'mac' => 'Adres MAC',
		'ip' => 'IP',
		'locationId' => 'Miejsce',
		'availableTo' => 'Rejestracja do',
		'comment' => 'Komentarz',
		'canAdmin' => 'Administrator',
		'exAdmin' => 'Ex-administrator',
		'active' => 'Aktywny',
		'typeId' => 'Typ',
		'carerId' => 'Opiekun',
		'masterHostId' => 'Serwer fizyczny/nadrzędny',
		'autoDeactivation' => 'Autodezaktywacja',
		'deviceModelId' => 'Model urządzenia',
	);

	static protected $namesEn = array(
		'host' => 'Host name',
		'mac' => 'MAC address',
		'ip' => 'IP',
		'locationId' => 'Room',
		'availableTo' => 'Available to',
		'comment' => 'Comment',
		'canAdmin' => 'Administrator',
		'exAdmin' => 'Ex-administrator',
		'active' => 'Active',
		'typeId' => 'Type',
		'carerId' => 'Carer',
		'masterHostId' => 'Physical/master server',
		'autoDeactivation' => 'Autodeactivation',
		'deviceModelId' => 'Device model',
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
					$changes[] = $names[$key].': '.(is_null($val) ? 'brak limitu' : date(self::TIME_YYMMDD, $val)).$arr.(is_null($new[$key]) ? 'brak limitu' : date(self::TIME_YYMMDD, $new[$key]));
					break;
				case 'comment':
					$changes[] = $names[$key].':<br/>'.UFlib_Diff::toHTML(UFlib_Diff::compare($this->_escape($val), $this->_escape($new[$key])));
					break;
				case 'canAdmin':
				case 'exAdmin':
				case 'active':
					$changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie');
					break;
				case 'typeId':
					$changes[] = $names[$key].': '.UFtpl_Sru_Computer::getComputerType($old['typeId']).$arr.UFtpl_Sru_Computer::getComputerType($new['typeId']);
					break;
				case 'carerId':
					$url = $this->url(0).'/admins/';
					$changes[] = $names[$key].': '.(is_null($val) ? 'nikt' : '<a href="'.$url.$val.'">'.$old['carerName']).'</a>'.$arr.(is_null($new[$key]) ? 'nikt' : '<a href="'.$url.$new[$key].'">'.$new['carerName'].'</a>');
					break;
				case 'masterHostId':
					$url = $this->url(0).'/computers/';
					$changes[] = $names[$key].': '.(is_null($val) ? 'brak' : '<a href="'.$url.$val.'">'.$old['masterHostName']).'</a>'.$arr.(is_null($new[$key]) ? 'brak' : '<a href="'.$url.$new[$key].'">'.$new['masterHostName'].'</a>');
					break;
				case 'autoDeactivation':
					$changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie');
					break;
				case 'deviceModelId':
					$changes[] = $names[$key].': '.(is_null($val) ? 'brak' : $old['deviceModelName']).$arr.(is_null($new[$key]) ? 'brak' : $new['deviceModelName']);
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
			'modifiedById' => $current->modifiedById,
			'modifiedBy' => $current->modifiedBy,
			'modifiedAt' => $current->modifiedAt,
			'comment' => $current->comment,
			'canAdmin' => $current->canAdmin,
			'exAdmin' => $current->exAdmin,
			'active' => $current->active,
			'typeId' => $current->typeId,
			'carerId' => ($current->carerId == 0) ? null : $current->carerId,
			'carerName' => $current->carerName,
			'masterHostId' => ($current->masterHostId == 0) ? null : $current->masterHostId,
			'masterHostName' => $current->masterHostName,
			'autoDeactivation' => $current->autoDeactivation,
			'deviceModelId' => $current->deviceModelId,
			'deviceModelName' => $current->deviceModelName,
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
					echo $names[$key].': '.(is_null($val) ? 'brak limitu' : date(self::TIME_YYMMDD, $val));
					if ($val!==$new[$key]) {
						echo $arr.(is_null($new[$key]) ? 'brak limitu' : date(self::TIME_YYMMDD, $new[$key]));
					}
					echo "\n";
					break;
				case 'carerId':
					if ($new['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $new['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
						$new['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE || $new['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE ||
						$new['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) {
						echo $names[$key].': '.(is_null($val) ? 'nikt' : $old['carerName']);
						if ($val!==$new[$key]) {
							echo $arr.(is_null($new[$key]) ? 'nikt' : $new['carerName']);
						}
						echo "\n";
					}
					break;
				case 'deviceModelId':
					if ($new['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $new['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE) {
						echo $names[$key].': '.(is_null($val) ? 'brak' : $old['deviceModelName']);
						if ($val!==$new[$key]) {
							echo $arr.(is_null($new[$key]) ? 'brak' : $new['deviceModelName']);
						}
						echo "\n";
					}
					break;
				case 'typeId':
					echo $names[$key].': '.(is_null($val) ? 'brak' : UFtpl_Sru_Computer::getComputerType($old['typeId']));
					if ($val!==$new[$key]) {
						echo $arr.(is_null($new[$key]) ? 'brak' : UFtpl_Sru_Computer::getComputerType($new['typeId']));
					}
					echo "\n";
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
		
		echo '<table id="computersHistoryFoundT" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>IP</th>';
		echo '<th>MAC</th>';
		echo '<th>Właściciel</th>';
		echo '<th>Używał do</th>';
		echo '</tr></thead><tbody>';
		
		foreach ($d as $c) {
			if (in_array($c['host'], $displayed)) {
				continue;
			}
			if ($c['ip'] == $c['currentIp']) {
				continue;
			}
			$owner = '<a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>';
			echo '<tr'.($c['currentBanned']?' class="ban"':'').'><td>'.(!$c['currentActive']?'<del>':'').'<a href="'.$url.'/computers/'.$c['computerId'].'">'.$c['host'].'.'.$c['domainSuffix'].(strlen($c['currentComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['currentComment'].'" />':'').(!$c['currentActive']?'</del>':'').'</td>';
			echo '<td>'.$c['currentIp'].'</td>';
			echo '<td>'.$c['mac'].'</td>';
			echo '<td>'.$owner.'</td>';
			echo '<td>'.date(self::TIME_YYMMDD, $c['modifiedAt']).'</td></tr>';
			
			$displayed[] = $c['host'];
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#computersHistoryFoundT").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
		if (count($displayed) == 0) {
			echo 'Wszystkie komputery używające wcześniej tego IP zostały wyświetlone na liście wyżej.';
		}
	}
}
