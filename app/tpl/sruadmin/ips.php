<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Ips
extends UFtpl_Common {

	public function ips(array $d) {		
		$url = $this->url(0).'/computers/';
		$urlDorm = $this->url(0).'/dormitories/';
	
		echo "<table id='ips'>";
		echo "<th colspan='8'><b>Serwery</b></th><tr>";
		$i = 1;
		foreach ($d as $c) {
			if (substr($c['ip'], -2) === '.0') {
				echo "</tr><th colspan='8'><a href='".$urlDorm.$c['dormAlias']."' name='".$c['dormAlias']."'><b>".$c['dormName']."</b></a></th><tr>";
				$i = 1;
			}
			if ($c['host'] != null) {
				echo "<td class='".$c['factDormAlias']."' title='".$c['host']." (".$c['factDorm'].")'><a href='".$url.$c['hostId']."'>".substr($c['ip'], 7)."</a></td>";
			} else {
				echo "<td>".substr($c['ip'], 7)."</td>";
			}
			if ($i % 8 == 0) {
				echo "</tr><tr>";
			}
			$i++;
		}
		echo "</tr></table>";
	}		
}
