<?
/**
* szablon beana historii lokacji
*/
class UFtpl_SruAdmin_RoomHistory
extends UFtpl_Common {

	static protected $names = array(
		'comment' => 'Komentarz',
		'typeId' => 'Typ',
		'usersMax' => 'Liczba miejsc',
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
				case 'comment':
					$changes[] = $names[$key].':<br/>'.UFlib_Diff::toHTML(UFlib_Diff::compare($this->_escape($val), $this->_escape($new[$key])));
					break;
				case 'typeId':
					$changes[] = $names[$key].': <q>'.UFtpl_SruAdmin_Room::getRoomType($val).'</q>'.$arr.'<q>'.UFtpl_SruAdmin_Room::getRoomType($new[$key]).'</q>';
					break;
				case 'usersMax':
					$changes[] = $names[$key].': '.$val.$arr.$new[$key].'</q>';
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

	public function history(array $d, $current) {
		echo '<div class="room">';
		echo '<h3>Historia zmian</h3>';
		echo '<ol class="history">';

		$curr = array(
			'comment' => $current->comment,
			'typeId' => $current->typeId,
			'usersMax' => $current->usersMax,
			'modifiedById' => $current->modifiedById,
			'modifiedByName' => $current->modifiedByName,
			'modifiedAt' => $current->modifiedAt,
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
