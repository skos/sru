<?
/**
* szablon beana historii admina
*/
class UFtpl_SruAdmin_AdminHistory
extends UFtpl_Common {

	static protected $names = array(
		'login' => 'Login',
		'name' => 'Nazwa',
		'typeId' => 'Typ',
		'phone' => 'Telefon',
		'jid' => 'JID',
		'email' => 'E-mail',
		'address' => 'Adres',
		'active' => 'Aktywny',
		'activeTo' => 'Aktywny do',
		'dormitoryId' => 'Akademik',
		'passwordChanged' => 'Zmieniono hasło do SRU',
		'passwordInnerChanged' => 'Zmieniono hasło wewnętrzne',
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
				case 'login':
				case 'name':
				case 'phone':
				case 'jid':
				case 'email':
				case 'address':
					$changes[] = $names[$key].': '.$val.$arr.$new[$key];
					break;
				case 'active':
					$changes[] = $names[$key].': '.($val?'tak':'nie').$arr.($new[$key]?'tak':'nie');
					break;
				case 'activeTo':
					$changes[] = $names[$key].': '.(is_null($val) ? 'brak limitu' : date(self::TIME_YYMMDD, $val)).$arr.(is_null($new[$key]) ? 'brak limitu' : date(self::TIME_YYMMDD, $new[$key]));
					break;
				case 'typeId':
					$changes[] = $names[$key].': '.(array_key_exists($old['typeId'], UFtpl_SruAdmin_Admin::$adminTypes) ? UFtpl_SruAdmin_Admin::$adminTypes[$old['typeId']] : UFtpl_SruWalet_Admin::$adminTypes[$old['typeId']]).$arr.(array_key_exists($old['typeId'], UFtpl_SruAdmin_Admin::$adminTypes) ? UFtpl_SruAdmin_Admin::$adminTypes[$new['typeId']] : UFtpl_SruWalet_Admin::$adminTypes[$new['typeId']]);
					break;
				case 'dormitoryId':
					$changes[] = $names[$key].': '.$old['dormitoryName'].$arr.$new['dormitoryName'];
					break;
				case 'passwordChanged': $val > 0 ? ($changes[] = $names[$key]) : ''; break;
				case 'passwordInnerChanged': $val > 0 ? ($changes[] = $names[$key]) : ''; break;
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

	public function history(array $d, $current) {
		echo '<div class="admin">';
		echo '<h3>Historia zmian</h3>';
		echo '<ol class="history">';

		$curr = array(
			'login' => $current->login,
			'name' => $current->name,
			'typeId' => $current->typeId,
			'phone' => $current->phone,
			'jid' => $current->jid,
			'email' => $current->email,
			'address' => $current->address,
			'active' => $current->active,
			'activeTo' => $current->activeTo,
			'dormitoryId' => $current->dormitoryId,
			'dormitoryName' => $current->dormitoryName,
			'modifiedById' => $current->modifiedById,
			'modifiedByName' => $current->modifiedByName,
			'modifiedAt' => $current->modifiedAt,
			'passwordChanged' => '0',
			'passwordInnerChanged' => '0',
		);
		$urlAdmin = $this->url(0).'/admins/';
		foreach ($d as $c) {
			echo '<li>';
			if (is_null($curr['modifiedByName'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedByName']).'</a>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
			echo $this->_diff($c, $curr);
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		if (is_null($curr['modifiedByName'])) {
			$changed = 'NIEZNANY';
		} else {
			$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedByName']).'</a>';
		}
		echo ((is_null($curr['modifiedAt']) || $curr['modifiedAt'] == 0) ? 'nieznana' : date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt'])).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';

		echo '</ol>';
		echo '</div>';
	}
}
