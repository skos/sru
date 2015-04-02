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
	
	protected $errors = array(
		'sspgOpinion/empty' => 'Podaj decyzję',
	);
	
	public function listOwn(array $d) {
		foreach ($d as $c) {
			echo '<li>'.$this->getStatus($c);
			echo ' - <small>złożony '.date(self::TIME_YYMMDD_HHMM, $c['createdAt']);
			echo ', ważny do '.date(self::TIME_YYMMDD, $c['validTo']).'</small></li>';
		}
	}
	
	public function listApplications(array $d, $id = 0, $isAdmin = false, $isChairman = false) {
		$userUrl = $this->url(0).'/users/';
		$acl = $this->_srv->get('acl');
		
		echo '<table id="applicationsT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Użytkownik</th>';
		echo '<th>Dodany</th>';
		echo '<th>Ważny do</th>';
		echo '<th>Status</th>';
		if ($isAdmin || $isChairman) {
			echo '<th>Operacje</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td>';
			if ($isAdmin) {
				echo '<a href="'.$userUrl.$c['userId'].'">';
			}
			echo $c['userName'].' '.$c['userSurname'];
			if ($isAdmin) {
				echo '</a>';
			}
			echo '</td>';
			echo '<td>'.date(self::TIME_YYMMDD_HHMM, $c['createdAt']).'</td>';
			echo '<td>'.date(self::TIME_YYMMDD, $c['validTo']).'</td>';
			echo '<td>'.$this->getStatus($c, true).'</td>';
			if ($isChairman && $acl->sru('fwexception', 'editApp', $c['id'])) {
				echo '<td><a href="'.$this->url(2).'/'.$c['id'].'">Edytuj</a></td>';
			} else if ($isAdmin && $acl->sruAdmin('fwexceptionapplication', 'edit', $c['id'])) {
				echo '<td><a href="'.$this->url(0).'/fwexceptions/application/'.$c['id'].'">Edytuj</a></td>';
			} else {
				echo '<td></td>';
			}
			echo '</tr>';
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
	
	public function sspgFormEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'fwExceptionApplicationEdit', $d, $this->errors);
		
		echo $form->_fieldset();
		echo '<p><em>Użytkownik:</em> '.$d['userName'].' '.$d['userSurname'].'</p>';
		echo '<p><em>Komentarz:</em> '.$d['comment'].'</p>';
		echo '<p><em>Edukacja własna:</em> '.($d['selfEducation'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Edukacja PG:</em> '.($d['universityEducation'] ? 'tak' : 'nie').'</p>';
		echo $form->sspgComment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>2));
		$tmp = array(
				'0' => 'Nie',
				'1' => 'Tak',
		);
		echo $form->sspgOpinion('Zgoda', array(
				'type' => $form->RADIO,
				'labels' => $form->_labelize($tmp),
				'labelClass' => 'radio',
				'class' => 'radio',
		));
		echo 'W przypadku odrzucenia wniosku komentarz będzie widoczny dla składającego wniosek.';
	}
	
	public function skosFormEdit(array $d, $ports) {
		$form = UFra::factory('UFlib_Form', 'fwExceptionApplicationEdit', $d, $this->errors);
	
		echo $form->_fieldset();
		echo '<p><em>Użytkownik:</em> '.$d['userName'].' '.$d['userSurname'].'</p>';
		$portsArr = array();
		foreach ($ports as $port) {
			$portsArr[] = $port['port'];
		}
		$portsStr = implode(', ', $portsArr);
		echo '<p><em>Porty:</em> '.$portsStr.'</p>';
		echo '<p><em>Komentarz:</em> '.$d['comment'].'</p>';
		echo '<p><em>Edukacja własna:</em> '.($d['selfEducation'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Edukacja PG:</em> '.($d['universityEducation'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Opinia SSPG:</em> '.$d['sspgComment'].'</p>';
		echo $form->skosComment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>2));
		$tmp = array(
				'0' => 'Nie',
				'1' => 'Tak',
		);
		echo $form->skosOpinion('Zgoda', array(
				'type' => $form->RADIO,
				'labels' => $form->_labelize($tmp),
				'labelClass' => 'radio',
				'class' => 'radio',
		));
		echo 'W przypadku odrzucenia wniosku komentarz będzie widoczny dla składającego wniosek.';
	}
	
	private function getStatus($application, $details = false) {
		if ($application['validTo'] < NOW) {
			return '<span class="archived">ARCHIWALNY</span>';
		}
		if ((!is_null($application['skosOpinion']) && $application['skosOpinion'] == false) ||
			(!is_null($application['sspgOpinion']) && $application['sspgOpinion'] == false)) {
			return '<span class="rejected">ODRZUCONY</span>';
		} else if ((!is_null($application['skosOpinion']) && $application['skosOpinion'] == true) &&
			(!is_null($application['sspgOpinion']) && $application['sspgOpinion'] == true)) {
			return '<span class="active">ZAAKCEPTOWANY</span>';
		} else if ($details) {
			if (is_null($application['sspgOpinion'])) {
				return '<span class="waiting">OCZEKUJE NA SSPG</span>';
			} else {
				return '<span class="waiting">OCZEKUJE NA SKOS</span>';
			}
		} else {
			return '<span class="waiting">OCZEKUJE</span>';
		}
	}
	
	public function newFwExceptionApplicationMailBody(array $d) {
		$conf = UFra::shared('UFconf_Sru');
		$host = $conf->sruUrl;
		if (is_null($d['sspgOpinion'])) {
			echo 'W SRU znajduje się nowy wniosek o usługi serwerowe do zatwierdzenia: '.$host.'/sru/applications/fwexceptions/'.$d['id']."\n";
		} else {
			echo 'W SRU znajduje się nowy wniosek o usługi serwerowe do zatwierdzenia.';
		}
	}
	
	public function rejectedFwExceptionApplicationMailBodyEnglish(array $d) {
		echo 'Your server services application was rejected with the following comment: ';
		if (!is_null($d['sspgOpinion']) && $d['sspgOpinion'] == false) {
			echo $d['sspgComment'];
		} else if (!is_null($d['skosOpinion']) && $d['skosOpinion'] == false) {
			echo $d['skosComment'];
		}
	}
	
	public function rejectedFwExceptionApplicationMailBodyPolish(array $d) {
		echo 'Twój wniosek o usługi serwerowe został odrzucony z następującym komentarzem: ';
		if (!is_null($d['sspgOpinion']) && $d['sspgOpinion'] == false) {
			echo $d['sspgComment'];
		} else if (!is_null($d['skosOpinion']) && $d['skosOpinion'] == false) {
			echo $d['skosComment'];
		}
	}
	
	public function approvedFwExceptionApplicationMailBodyEnglish(array $d) {
		echo 'Your server services application was approved. The server services will work in max 24h.';
	}
	
	public function approvedFwExceptionApplicationMailBodyPolish(array $d) {
		echo 'Twój wniosek o usługi serwerowe został zaopiniowany pozytywnie. Usługi serwerowe będą działać maksymalnie za 24h.';
	}
}
