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
			if ($uploader['isAdmin']) {
				echo '<li class="admin">';
				$suffix = ' - admin';
			} else if ($uploader['typeId'] == UFbean_Sru_Computer::TYPE_SERVER) {
				echo '<li class="serv">';
				$suffix = ' - serwer';
			} else if (in_array($uploader['ip'], $exAdmins)) {
				echo '<li class="exAdmin">';
				$suffix = ' - exAdmin';
			} else {
				echo '<li>';
			}
			if (!is_null($uploader['host'])) {
				echo '<a href="'.$this->url(0).'/computers/'.$uploader['hostId'].'/stats">'.$uploader['host'].' <small>('.$uploader['ip'].')</small></a>: '.$uploader['bytes_min'].'/<b>'.$uploader['bytes_sum'].'</b>/'.$uploader['bytes_max'].' kB/s'.$suffix.'</li>';
			} else {
				echo '<a href="'.$this->url(0).'/computers/search/ip:'.$uploader['ip'].'">'.$uploader['ip'].'</a>: '.$uploader['bytes_min'].'/<b>'.$uploader['bytes_sum'].'</b>/'.$uploader['bytes_max'].' kB/s'.$suffix.'</li>';
			}
			$sumAvg += $uploader['bytes_sum'];
			$sum++;
		}
		echo '</ul><hr/>';
		echo 'Uploaderów: '.$sum.', średnio: '.round($sumAvg/$sum).'kB/s<br/>';
		echo 'Format: min/avg/max. Dane pochodzą z ostatnich 30 minut, maksymalnie 20 najbardziej aktywnych IP z transferem powyżej 10kB/s.';
	}
}
