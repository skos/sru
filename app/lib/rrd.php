<?
/**
 * Obsługa RRDtoola
 */
class UFlib_Rrd {
	const REFRESH = 60;

	public function generatePng($mac, $host, $hour, $date) {
		$conf = UFra::factory('UFconf_Sru');
		$file = $mac.$hour.$date;

		if ((!file_exists(UFURL_BASE.'i/stats-img/'.$file.'.png') || (filemtime(UFURL_BASE.'i/stats-img/'.$file.'.png') < time() - self::REFRESH)) && file_exists($conf->rrdDataDir.$mac.'.rrd')) {
			$cmd = UFURL_BASE.'../app/bin/rrd_graph.sh '.$mac.' '.$host.' '.$hour.' '.$date;
			exec($cmd, $out, $val);
		}
		return $file;
	}
}
