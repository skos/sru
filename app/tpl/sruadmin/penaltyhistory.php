<?
/**
* szablon beana historii kary
*/
class UFtpl_SruAdmin_PenaltyHistory
extends UFtpl_Common {
protected function _diff(array $old, array $new, $current) {
	$changes = array();
	$arr = ' &rarr; ';
	foreach ($old as $key=>$val) {
			if (!array_key_exists($key, $new) || $val === $new[$key]) {
				continue;
			}
			switch ($key) {
				case 'endAt':
					$changes[] = 'Koniec: '.date(self::TIME_YYMMDD_HHMM, $val).$arr.date(self::TIME_YYMMDD_HHMM, $new[$key]);
					break;
				case 'reason':
					$changes[] = 'Powód: <q>'.$val.'</q>'.$arr.'<q>'.$new[$key].'</q>';
					break;
				case 'comment':
					$changes[] = 'Komentarz:<br/>'.UFlib_Diff::toHTML(UFlib_Diff::compare($this->_escape($val), $this->_escape($new[$key])));
					break;
				case 'amnestyAfter':
					$changes[] = 'Minimalna długość (dni): <q>'.(($val - $current->startAt) / 24 / 3600).'</q>'.$arr.'<q>'.(($new[$key] - $current->startAt) / 24 / 3600).'</q>';
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
		echo '<div class="penalty">';
		echo '<h3>Historia zmian</h3>';
		echo '<ol class="history">';

		$curr = array(
			'endAt' => $current->endAt,
			'reason' => $current->reason,
			'comment' => $current->comment,
			'amnestyAfter' => $current->amnestyAfter,
			'modifiedById' => $current->modifiedById,
			'modifiedBy' => $current->modifiedByName,
			'modifiedAt' => $current->modifiedAt,
			'createdAt' => $current->createdAt,
		);
		$urlAdmin = $this->url(0).'/admins/';
		if (!$current->active) {
			echo '<li>';
			if ($current->amnestyById) {
				$changed = '<a href="'.$urlAdmin.$current->amnestyById.'">'.$this->_escape($current->amnestyByName).'</a>';
			} else {
				$changed = 'AUTOMAT';
			}
			echo date(self::TIME_YYMMDD_HHMM, $current->endAt).' &mdash; '.$changed;
			echo '<ul><li>Zakończona</li></ul>';
			echo '</li>';
		}
		foreach ($d as $c) {
			echo '<li>';
			$changed = '<a href="'.$urlAdmin.$curr['modifiedById'].'">'.$this->_escape($curr['modifiedBy']).'</a>';
			echo date(self::TIME_YYMMDD_HHMM, $curr['modifiedAt']).' &mdash; '.$changed;
			echo $this->_diff($c, $curr, $current);
			echo '</li>';
			$curr = $c;
		}
		echo '<li>';
		$changed = '<a href="'.$urlAdmin.$current->createdById.'">'.$this->_escape($current->createdByName).'</a>';
		echo date(self::TIME_YYMMDD_HHMM, $current->createdAt).' &mdash; '.$changed;
		echo '<ul><li>Utworzono</li></ul>';
		echo '</li>';

		echo '</ol>';
		echo '</div>';
	}
}
