<?
/**
 * szablon wniosku o wyjatki w fw
 */
class UFtpl_Sru_FwExceptionApplication
extends UFtpl_Common {
	
	public static $applicationTypes = array(
		1 => 'związany z przedmiotami na Uczelni',
		2 => 'nauka własna', 
	);
	
	public function listOwn(array $d) {
		foreach ($d as $c) {
			echo '<li>'.$this->getStatus($c);
			echo ' - <small>złożony '.date(self::TIME_YYMMDD_HHMM, $c['createdAt']);
			echo ', ważny do '.date(self::TIME_YYMMDD, $c['validTo']).'</small></li>';
		}
	}
	
	public function listApplications(array $d, $id = 0) {
		$userUrl = $this->url(0).'/users/';
		
		echo '<table id="applicationsT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Użytkownik</th>';
		echo '<th>Dodany</th>';
		echo '<th>Ważny do</th>';
		echo '<th>Status</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td><a "'.$userUrl.$c['userId'].'">'.$c['userName'].' '.$c['userSurname'].'</a></td>';
			echo '<td>'.date(self::TIME_YYMMDD_HHMM, $c['createdAt']).'</td>';
			echo '<td>'.date(self::TIME_YYMMDD, $c['validTo']).'</td>';
			echo '<td>'.$this->getStatus($c).'</td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#applicationsT<? echo $id;?>").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}
	
	private function getStatus($application) {
		if ($application['validTo'] < NOW) {
			return '<span class="archived">ARCHIWALNY</span>';
		}
		if ((!is_null($application['skosOpinion']) && $application['skosOpinion'] == false) ||
			(!is_null($application['sspgOpinion']) && $application['sspgOpinion'] == false)) {
			return '<span class="rejected">ODRZUCONY</span>';
		} else if ((!is_null($application['skosOpinion']) && $application['skosOpinion'] == true) ||
			(!is_null($application['sspgOpinion']) && $application['sspgOpinion'] == true)) {
			return '<span class="active">ZAAKCEPTOWANY</span>';
		} else {
			return '<span class="waiting">OCZEKUJE</span>';
		}
	}
}
