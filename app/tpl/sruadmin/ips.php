<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Ips
extends UFtpl_Common {

	public function ips(array $d, $dorm = null) {		
		$url = $this->url(0).'/computers/';
		$urlDorm = $this->url(0).'/dormitories/';

		echo "<table id='ips'>";
		echo "<th colspan='8'><b>Serwery: 208.</b></th><tr>";
		$i = 1;
		$actDormAlias = '';
		$actClass = 208;
		foreach ($d as $c) {
			if (substr($c['ip'], 7, 3) > $actClass) {
				$actClass = substr($c['ip'], 7, 3);
				if (substr($c['ip'], 7, 3) == '209') {
					echo "</tr><tr>";
				}
				if ($actDormAlias != $c['dormAlias']) {
					echo "</tr><th colspan='8'><a href='".$urlDorm.$c['dormAlias']."' name='".$c['dormAlias']."'><b>".$c['dormName']."</b></a></th><tr>";
					$i = 1;
					$actDormAlias = $c['dormAlias'];
				}
				echo "</tr><th colspan='8'>Klasa: ".substr($c['ip'], 7, 4)."</th><tr>";
			}
			if ($c['host'] != null) {
				if ($dorm == null || ($dorm != null && $c['factDormAlias'] == $dorm->alias)) {
					echo "<td class='".$c['factDormAlias']."' title='".$c['host']." : ".$c['userName']." ".$c['userSurname']." (".$c['userLogin'].") - ".$c['factDorm']."'><a href='".$url.$c['hostId']."'>".substr($c['ip'], 11)."</a></td>";
					$i++;
				} else if ($dorm == null){
					echo "<td>".substr($c['ip'], 11)."</td>";
					$i++;
				}
			} else if ($dorm == null) {
				echo "<td>".substr($c['ip'], 11)."</td>";
				$i++;
			}
			if ($i != 1 && ($i - 1) % 8 == 0) {
				echo "</tr><tr>";
			}
		}
		echo "</tr></table>";
	}		
}
