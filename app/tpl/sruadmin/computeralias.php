<?
class UFtpl_SruAdmin_ComputerAlias
extends UFtpl_Common {
	public function listAliases(array $d) {
		$url = $this->url(0).'/computers/';
		
		echo '<table id="aliasesFoundT" class="bordered"><thead><tr>';
		echo '<th>Alias</th>';
		echo '<th>Typ</th>';
		echo '<th>Host</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr'.($c['parentBanned'] ? ' class="ban"' : '').'><td><a href="'.$url.$c['computerId'].'">'.$c['host'].'</td>';
			echo '<td>'.($c['isCname'] ? 'CNAME' : 'A').'</td>';
			echo '<td>'.$c['parentWithDomain'].(strlen($c['parentComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['parentComment'].'" />':'').'</a></td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#aliasesFoundT").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}
}