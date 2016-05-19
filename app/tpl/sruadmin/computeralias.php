<?
class UFtpl_SruAdmin_ComputerAlias
extends UFtpl_Common {

	public static $recordTypes = array(
		UFbean_SruAdmin_ComputerAlias::TYPE_CNAME 	=> 'CNAME',
		UFbean_SruAdmin_ComputerAlias::TYPE_A 	=> 'A',
		UFbean_SruAdmin_ComputerAlias::TYPE_IN_TXT 	=> 'IN TXT',
	);

	public function listAliases(array $d) {
		$url = $this->url(0).'/computers/';
		
		echo '<table id="aliasesFoundT" class="bordered"><thead><tr>';
		echo '<th>Alias</th>';
		echo '<th>Typ</th>';
		echo '<th>Host</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr'.($c['parentBanned'] ? ' class="ban"' : '').'><td><a href="'.$url.$c['computerId'].'">'.$c['domainName'].'</td>';
			echo '<td>'.UFtpl_SruAdmin_ComputerAlias::$recordTypes[$c['recordType']].'</td>';
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