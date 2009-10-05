<?
/**
 * szablon tpl migracji
 */
class UFtpl_SruAdmin_Migration
extends UFtpl_Common {

	public function migration(array $d, $users) {
		$url = $this->url(0);
		$hashes = array();
		$wrongName = array();
		foreach ($d as $m) {
			$hashes[$m['hash']] = $m['room'].' '.$m['dorm'];
		}
		foreach ($users as $u) {
			if (!array_key_exists(md5($u['surname'].' '.$u['name']), $hashes)) {
				$wrongName[] =  '<a href="'.$url.'/users/'.$u['id'].'">'.$u['name'].' "'.$u['login'].'" '.$u['surname'].'</a>';
			}
		}
		echo '<h3>Niezgodności w imieniu lub nazwisku ('.count($wrongName).')</h3>';
		echo '<ul>';
		foreach ($wrongName as $wn) {
			echo '<li>'.$wn.'</li>';
		}
		echo '</ul>';

		$wrongLocation = array();
		foreach ($users as $u) {
			$hash = md5($u['surname'].' '.$u['name']);
			$currLoc = strtoupper($u['locationAlias']).' '.$u['dormitoryId'];
			if (array_key_exists($hash, $hashes) && $hashes[$hash] != $currLoc) {
				$currLocDisp = strtoupper($u['locationAlias']).' '.strtoupper($u['dormitoryAlias']);
				$waletLoc = explode(" ", $hashes[$hash]);
				if ($waletLoc[1] != null && $waletLoc[1] != "") {
					$waletLoc[1]--;
				}
				$waletLocDisp = $waletLoc[0].' DS'.$waletLoc[1];
				$wrongLocation[] =  '<a href="'.$url.'/users/'.$u['id'].'">'.$u['name'].' "'.$u['login'].'" '.$u['surname'].' ('.$currLocDisp.' / '.$waletLocDisp.')</a>';
			}
		}
		echo '<h3>Niezgodności w lokalizacji ('.count($wrongLocation).')</h3>';
		echo '<ul>';
		foreach ($wrongLocation as $wl) {
			echo '<li>'.$wl.'</li>';
		}
		echo '</ul>';
	}
}
