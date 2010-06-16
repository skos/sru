<?
/**
 * szablon beana historii uzytkownika
 */
class UFtpl_SruAdmin_ServiceHistory
extends UFtpl_Common {

	public function table(array $d, $current) {
		$url = $this->url(0).'/users/'.$current->id;
		$urlAdmin = $this->url(0).'/admins/';
		foreach ($d as $c) {
			echo '<li>';
			if (is_null($c['adminId'])) {
				$changed = 'UŻYTKOWNIK';
			} else {
				$changed = '<a href="'.$urlAdmin.$c['adminId'].'">'.$this->_escape($c['admin']).'</a>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']).' &mdash; '.$changed;
			echo '<ul>';
			echo '<li>'.$c['servName'].' <i>'.self::getState($c['state']).'</i></li>';
			echo '</ul>';
			echo '</li>';
		}
	}

	private function getState($state) {
		switch ($state) {
			case 1:
				return 'oczekiwało na aktywację.';
			case 2:
				return 'zostało aktywowane.';
			case 3:
				return 'oczekiwało na dezaktywację.';
			case 4:
				return 'zostało dezaktywowane.';
		}
	}
}
