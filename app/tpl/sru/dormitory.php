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

	public function listDormsWalet(array $d) {
		$url = $this->url(0).'/dormitories/';
		$acl = $this->_srv->get('acl');
		
		echo '<ul>';
		foreach ($d as $c) {
			if ($acl->sruWalet('dorm', 'view', $c['alias'])) {
				echo '<li><a href="'.$url.$c['alias'].'">'.$c['name'].'</a></li>';
			}
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

	public function inhabitants(array $d, $rooms) {
		$url = $this->url(0).'/dormitories/';
		$acl = $this->_srv->get('acl');

		$people = array();
		$freePlaces = array();
		$overPlaces = array();
		foreach ($d as $c) {
			$people[$c['id']] = 0;
			$freePlaces[$c['id']] = 0;
			$overPlaces[$c['id']] = 0;
		}
		foreach ($rooms as $room) {
			if ((int)$room['alias'] == 0 && substr($room['alias'], 0, 1) != 'm') continue;
			$people[$room['dormitoryId']] += $room['userCount'];
			if ($room['userCount'] < $room['usersMax']) {
				$freePlaces[$room['dormitoryId']] += ($room['usersMax'] - $room['userCount']);
			} else if ($room['userCount'] > $room['usersMax']) {
				$overPlaces[$room['dormitoryId']] += ($room['userCount'] - $room['usersMax']);
			}
		}
		
		echo '<table id="inhabitantsT"><thead><tr>';
		echo '<th>Dom Studencki</th>';
		echo '<th>Limit</th>';
		echo '<th>Mieszkańców</th>';
		echo '<th>Wolne</th>';
		echo '<th>Dokwaterowani</th>';
		echo '</tr></thead><tbody>';

		$usersMax = 0;
		$userCount = 0;
		$usersFree = 0;
		$usersOver = 0;
		foreach ($d as $c) {
			echo '<tr><td style="border-top: 1px solid;">';
			echo ($acl->sruWalet('dorm', 'view', $c['alias']) ? '<a href="'.$url.$c['alias'].'">' : '').$c['name'].($acl->sruWalet('dorm', 'view', $c['alias']) ? '</a>' : '');
			echo '</td>';
			echo '<td style="text-align: right; border-top: 1px solid;">'.$c['usersMax'].'</td>';
			echo '<td style="text-align: right; border-top: 1px solid;">'.$people[$c['id']].'</td>';
			echo '<td style="text-align: right; border-top: 1px solid;">'.$freePlaces[$c['id']].'</td>';
			echo '<td style="text-align: right; border-top: 1px solid;">'.$overPlaces[$c['id']].'</td></tr>';
			$usersMax += $c['usersMax'];
			$userCount += $people[$c['id']];
			$usersFree += $freePlaces[$c['id']];
			$usersOver += $overPlaces[$c['id']];
		}
		echo '</tbody>';
		echo '<tr><td style="border-top: 1px solid;"><b>SUMA</b></td><td style="border-top: 1px solid;"><b>'.$usersMax.'</b></td><td style="text-align: right; border-top: 1px solid;"><b>'.$userCount.'</b></td><td style="text-align: right; border-top: 1px solid;"><b>'.$usersFree.'</b></td><td style="text-align: right; border-top: 1px solid;"><b>'.$usersOver.'</b></td></tr>';
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
