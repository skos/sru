<?php
class UFtpl_Core_Log
extends UFtpl {


	public function full(array $d) {
		if (!count($d)) {
			return;
		}
		$start = $d[0]['time'];
		foreach ($d as $l) {
			echo str_pad(number_format(1000*($l['time'] - $start), 3, '.', ' '), 8, ' ', STR_PAD_LEFT).'ms '.$l[2].':'.$l[3]."\n";
			print_r($l[1]);
			echo "\n\n\n";
		}
	}
}
