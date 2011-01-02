<?
/**
 * szablon statystyk transferu
 */
class UFtpl_SruAdmin_Transfer
extends UFtpl_Common {
	public function transferStats(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$exAdmins = $conf->exAdmins;

		echo '<ul>';
		$sumAvg = 0;
		$sum = 0;
		foreach ($d as $uploader) {
			$suffix = '';
			$class = '<li>';
			if ($uploader['isAdmin']) {
				$class = '<li class="admin">';
				$suffix = ' - admin';
			} else if ($uploader['typeId'] == UFbean_Sru_Computer::TYPE_ORGANIZATION) {
				$class = '<li class="org">';
				$suffix = ' - organizacja';
			} else if ($uploader['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) {
				$class = '<li class="adm">';
				$suffix = ' - administracja';
			} else if ($uploader['typeId'] == UFbean_Sru_Computer::TYPE_SERVER) {
				$class = '<li class="serv">';
				$suffix = ' - serwer';
			} else if (in_array($uploader['ip'], $exAdmins)) {
				$class = '<li class="exAdmin">';
				$suffix = ' - exAdmin';
			}
			// jeśli zbanowany, nadpiszmy kolor
			if ($uploader['isBanned']) {
				$class = '<li class="ban">';
			}

			if (!is_null($uploader['host'])) {
				echo $class.'<a href="'.$this->url(0).'/computers/'.$uploader['hostId'].'/stats">'.$uploader['host'].' <small>('.$uploader['ip'].')</small></a>: '.$uploader['bytes_min'].'/<b>'.$uploader['bytes_sum'].'</b>/'.$uploader['bytes_max'].' kB/s'.$suffix.'</li>';
			} else {
				echo $class.'<a href="'.$this->url(0).'/computers/search/ip:'.$uploader['ip'].'">'.$uploader['ip'].'</a>: '.$uploader['bytes_min'].'/<b>'.$uploader['bytes_sum'].'</b>/'.$uploader['bytes_max'].' kB/s'.$suffix.'</li>';
			}
			$sumAvg += $uploader['bytes_sum'];
			$sum++;
		}
		echo '</ul><hr/>';
		echo 'Uploaderów: '.$sum.', średnio: '.round($sumAvg/$sum).'kB/s<br/>';
		echo 'Format: min/avg/max. Dane pochodzą z ostatnich 30 minut, maksymalnie 20 najbardziej aktywnych IP z transferem powyżej 10kB/s.';
	}

	public function myTransferStats(array $d) {
		foreach ($d as $uploader) {
			echo $uploader['bytes_min'].'/'.$uploader['bytes_sum'].'/'.$uploader['bytes_max'];
		}
	}
}
