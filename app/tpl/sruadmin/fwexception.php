<?
/**
 * szablon wyjatkow w fw
 */
class UFtpl_SruAdmin_FwException
extends UFtpl_Common {
	
	public static $applicationTypes = array(
		1 => 'związany z przedmiotami na Uczelni',
		2 => 'nauka własna', 
	);
	
	public function listExceptions(array $d, $id = 0) {
		$hostUrl = $this->url(0).'/computers/';
		
		echo '<table id="exceptionsT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>Port</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td><a href="'.$hostUrl.$c['computerId'].'">'.$c['host'].'</a></td>';
			echo '<td>'.($c['port'] == 0 ? 'wszystkie' : $c['port']).'</td></tr>';
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
}
