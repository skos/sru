<?
/**
 * szablon wyjatkow w fw
 */
class UFtpl_SruAdmin_FwExceptions
extends UFtpl_Common {
	public function listExceptions(array $d, $id = 0) {
		$hostUrl = $this->url(0).'/computers/';
		
		echo '<table id="exceptionsT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>Port</th>';
		echo '<th>Komentarz</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td><a href="'.$hostUrl.$c['computerId'].'">'.$c['host'].'</a></td>';
			echo '<td>'.$c['port'].'</td>';
			echo '<td>'.$c['comment'].'</td></tr>';
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
