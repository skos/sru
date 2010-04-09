<?
/**
 * szablon tpl migracji
 */
class UFtpl_SruAdmin_Migration
extends UFtpl_Common {

	public function migration(array $d, $users) {
		$url = $this->url(0);
		$hashes = array();
		$conf = UFra::shared('UFconf_Sru');
		foreach ($d as $m) {
			if (array_key_exists($m['hash'], $hashes)) {
				$hashes[$m['hash']] = $hashes[$m['hash']]."||".$m['room'].' '.$m['dorm'];
			} else {
				$hashes[$m['hash']] = $m['room'].' '.$m['dorm'];
			}
		}
		echo '<h3>Niezgodności w imieniu lub nazwisku</h3>';
		$lastDorm = '';
		foreach ($users as $u) {
			if (!array_key_exists(md5($u['surname'].' '.$u['name']), $hashes) && !in_array($u['name'], $conf->exclusions) && !in_array($u['id'], $conf->excludedWalet)) {
				if ($lastDorm != $u['dormitoryName']) {
					if ($lastDorm != '') {
						echo '</ul>';
					}
					if ($u['dormitoryId'] == 13) {
						break;
					}
					echo '<h4>'.$u['dormitoryName'].'</h4><ul>';
					$lastDorm = $u['dormitoryName'];
				}
				if ($u['wrongDataBans'] > 0) {
					echo '<li class="ban">';
				} else if ($u['wrongDataWarnings'] > 0) {
					echo '<li class="warning">';
				} else {
					echo '<li>';
				}
				echo '<a href="'.$url.'/users/'.$u['id'].'">'.$u['name'].' "'.$u['login'].'" '.$u['surname'].'</a></li>';
			}
		}
		echo '</ul>';

		echo '<h3>Niezgodności w lokalizacji</h3>';
		$lastDorm = '';
		foreach ($users as $u) {
			$hash = md5($u['surname'].' '.$u['name']);
			$currLoc = strtoupper($u['locationAlias']).' '.$u['dormitoryId'];
			if (array_key_exists($hash, $hashes)) {
				$waletLocs = explode("||", $hashes[$hash]);
				$dataOk = false;
				foreach ($waletLocs as $wl) {
					$waletLoc = explode(" ", $wl);
					if ($wl == $currLoc) {
						$dataOk = true;
						break;
					}
				}
				if ($dataOk) {
					continue;
				}
				if ($lastDorm != $u['dormitoryName']) {
					if ($lastDorm != '') {
						echo '</ul>';
					}
					echo '<h4>'.$u['dormitoryName'].'</h4><ul>';
					$lastDorm = $u['dormitoryName'];
				}
				$currLocDisp = strtoupper($u['locationAlias']).' '.strtoupper($u['dormitoryAlias']);
				if ($waletLoc[1] != null && $waletLoc[1] != "") {
					if ($waletLoc[1] == 6) {
						$waletLoc[1] = '5Ł';
					}
					if ($waletLoc[1] > 6) {
						$waletLoc[1]--;
					}
				}
				$waletLocDisp = $waletLoc[0].' DS'.$waletLoc[1];
				if ($u['wrongDataBans'] > 0) {
					echo '<li class="ban">';
				} else if ($u['wrongDataWarnings'] > 0) {
					echo '<li class="warning">';
				} else {
					echo '<li>';
				}
				echo '<a href="'.$url.'/users/'.$u['id'].'">'.$u['name'].' "'.$u['login'].'" '.$u['surname'].' ('.$currLocDisp.' / '.$waletLocDisp.')</a></li>';
			}
		}
		echo '</ul>';
	}
}
