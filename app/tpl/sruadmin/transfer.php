<?
/**
 * szablon statystyk transferu
 */
class UFtpl_SruAdmin_Transfer
extends UFtpl_Common {
	public function transferStats(array $d) {
		echo '<ul>';
		$sumAvg = 0;
		$sum = 0;
		foreach ($d as $uploader) {
			echo '<li><a href="'.$this->url(0).'/computers/search/mac:'.$uploader['mac'].'">'.$uploader['mac'].'</a>: '.$uploader['bytes_min'].'/<b>'.$uploader['bytes_sum'].'</b>/'.$uploader['bytes_max'].' kB/s</li>';
			$sumAvg += $uploader['bytes_sum'];
			$sum++;
		}
		echo '</ul><hr/>';
		echo 'Uploaderów: '.$sum.', średnio: '.round($sumAvg/$sum).'kB/s<br/>';
		echo 'Format: min/avg/max. Dane pochodzą z ostatnich 30 minut, maksymalnie 100 najbardziej aktywnych IP z transferem powyżej 10kB/s.';
	}
}
