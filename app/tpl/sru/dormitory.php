<?
/**
 * szablon beana akademika
 */
class UFtpl_Sru_Dormitory
extends UFtpl_Common {
	
	public function listDorms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		echo '<ul>';
		
		foreach ($d as $c)
		{
			echo '<li><a href="'.$url.$c['alias'].'">'.$c['name'].'</a></li>';			
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['name'];
	}
	public function details(array $d) {
		$url = $this->url(0).'/';
		
		echo '<h2>'.$d['name'].'<br/><small>(liczba użytkowników: '.$d['userCount'].' &bull; <a href="'.$url.'ips/'.$d['alias'].'">liczba komputerów: '.$d['computerCount'].'</a> &bull; <a href="'.$url.'switches/dorm/'.$d['alias'].'">switche</a>)</small></h2>';
	}

	public function inhabitants(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		echo '<table id="inhabitantsT"><thead><tr>';
		echo '<th>Dom Studencki</th>';
		echo '<th>Suma</th>';
		echo '<th>Mieszkańców</th>';
		echo '<th>Wolne</th>';
		echo '<th>Dokwaterowani</th>';
		echo '</tr></thead><tbody>';

		$usersMax = 0;
		$userCount = 0;
		$usersFree = 0;
		foreach ($d as $c) {
			echo '<tr><td><a href="'.$url.$c['alias'].'">'.$c['name'].'</a></td>';
			echo '<td style="text-align: right;">'.$c['usersMax'].'</td>';
			echo '<td style="text-align: right;">'.$c['userCount'].'</td>';
			echo '<td style="text-align: right;">'.($c['usersMax'] - $c['userCount']).'</td>';
			echo '<td style="text-align: right;">???</td></tr>';
			$usersMax += $c['usersMax'];
			$userCount += $c['userCount'];
			$usersFree += ($c['usersMax'] - $c['userCount']);
		}
		echo '</tbody>';
		echo '<tr><td><b>SUMA</b></td><td><b>'.$usersMax.'</b></td><td style="text-align: right;"><b>'.$userCount.'</b></td><td style="text-align: right;"><b>'.$usersFree.'</b></td><td style="text-align: right;"><b>???</b></td></tr>';
		echo '</table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#inhabitantsT").tablesorter(); 
    } 
);
</script>
<?
	}
}
