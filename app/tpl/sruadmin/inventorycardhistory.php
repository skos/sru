<?

/**
 * historia karty wyposazenia
 */
class UFtpl_SruAdmin_InventoryCardHistory extends UFtpl_Common {

	static protected $names = array(
	    'serialNo' => 'Nr seryjny',
	    'dormitoryId' => 'Na stanie',
	    'inventoryNo' => 'Nr inwentarzowy',
	    'received' => 'Na stanie od',
	    'comment' => 'Komentarz',
	);

	protected function _diff(array $old, array $new) {
		$changes = array();
		$arr = ' &rarr; ';
		$names = self::$names;
		foreach ($old as $key => $val) {
			if (!array_key_exists($key, $new) || $val === $new[$key]) {
				continue;
			}
			switch ($key) {
				case 'serialNo':
				case 'inventoryNo':
					$changes[] = $names[$key] . ': ' . $val . $arr . $new[$key];
					break;
				case 'dormitoryId':
					$changes[] = $names[$key] . ': ' . $old['dormitoryAlias'] . $arr . $new['dormitoryAlias'];
					break;
				case 'received':
					$changes[] = $names[$key].': '.(is_null($val) ? 'brak' : date(self::TIME_YYMMDD, $val)).$arr.(is_null($new[$key]) ? 'brak' : date(self::TIME_YYMMDD, $new[$key]));
					break;
				case 'comment':
					$changes[] = $names[$key] . ':<br/>' . UFlib_Diff::toHTML(UFlib_Diff::compare($this->_escape($val), $this->_escape($new[$key])));
					break;
				default: continue;
			}
		}
		if (!count($changes)) {
			return '';
		}
		$return = '';
		foreach ($changes as $c) {
			$return .= '<li>' . $c . '</li>';
		}
		return '<ul>' . $return . '</ul>';
	}

	public function history(array $d, $current) {
		echo '<div class="admin">';
		echo '<h3>Historia zmian karty wyposażenia</h3>';
		echo '<ol class="history">';

		$curr = array(
		    'serialNo' => $current->serialNo,
		    'inventoryNo' => $current->inventoryNo,
		    'received' => $current->received,
		    'dormitoryId' => $current->dormitoryId,
		    'dormitoryAlias' => $current->dormitoryAlias,
		    'modifiedById' => $current->modifiedById,
		    'modifiedBy' => $current->modifiedBy,
		    'modifiedAt' => $current->modifiedAt,
		    'comment' => $current->comment,
		);
		$urlAdmin = $this->url(0) . '/admins/';
		foreach ($d as $c) {
			echo '<li>';
			if (is_null($curr['modifiedBy'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="' . $urlAdmin . $curr['modifiedById'] . '">' . $this->_escape($curr['modifiedBy']) . '</a>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']) . ' &mdash; ' . $changed;
			echo $this->_diff($c, $curr);
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		if (is_null($curr['modifiedBy'])) {
			$changed = 'NIEZNANY';
		} else {
			$changed = '<a href="' . $urlAdmin . $curr['modifiedById'] . '">' . $this->_escape($curr['modifiedBy']) . '</a>';
		}
		echo ((is_null($curr['modifiedAt']) || $curr['modifiedAt'] == 0) ? 'nieznana' : date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt'])) . ' &mdash; ' . $changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';

		echo '</ol>';
		echo '</div>';
	}

}
