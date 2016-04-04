<?
/**
 * szablon wyjatkow w fw
 */
class UFtpl_SruAdmin_FwException
extends UFtpl_Common {
		
	public function listExceptions(array $d, $id = 0) {
		$hostUrl = $this->url(0).'/computers/';
		$userUrl = $this->url(0).'/users/';
		$adminUrl = $this->url(0).'/admins/';
		$applicationUrl = $this->url(0).'/fwexceptions/application/';
		
		echo '<table id="exceptionsT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>Użytkownik</th>';
		echo '<th>Port</th>';
		echo '<th>Dodał</th>';
		echo '<th>Dodany</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td><a href="'.$hostUrl.$c['computerId'].'">'.$c['host'].'</a></td>';
			echo '<td><a "'.$userUrl.$c['userId'].'">'.$c['userName'].' '.$c['userSurname'].'</a></td>';
			echo '<td>'.($c['port'] == 0 ? 'wszystkie' : $c['port']).'</td>';
			echo '<td>'.(is_null($c['applicationId']) ? (is_null($c['modifiedBy']) ? 'admin (przed 2016)' : '<a href="'.$adminUrl.$c['modifiedBy'].'">'.$c['modifiedByName'].'<a/>') : '<a href="'.$applicationUrl.$c['applicationId'].'">użytkownik</a>').'</td>';
			echo '<td>'.date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']).'</td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#exceptionsT<? echo $id;?>").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}
	
	public function apiFirewallExceptions(array $d) {
		$hosts = array();
		foreach ($d as $c) {
			$hosts[] = array("host" => $c['ip'], "port" => $c['port']);
		}
		echo json_encode($hosts);
	}

	public function apiFirewallExceptionsPlain(array $d) {
		foreach ($d as $c) {
			echo $c['id']."\n";
		}
	}
}
