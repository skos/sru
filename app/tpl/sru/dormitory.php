<?
/**
 * szablon beana akademika
 */
class UFtpl_Sru_Dormitory
extends UFtpl_Common {
	
	public function listDorms(array $d) {
		$url = $this->url(0).'/dormitories/';
		$urlIp = $this->url(0).'/ips/';
		$urlSw = $this->url(0).'/switches/dorm/';
		
		echo '<ul>';
		foreach ($d as $c) {
			echo '<li>'.$c['name'].': <a href="'.$url.$c['alias'].'">pokoje</a> &bull; <a href="'.$urlIp.$c['alias'].'">komputery</a> &bull; <a href="'.$urlSw.$c['alias'].'">switche</a></li>';
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
		
		echo '<table id="inhabitantsT" class="bordered"><thead><tr>';
		echo '<th>Dom studencki</th>';
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
			echo '<tr><td>';
			echo ($acl->sruWalet('dorm', 'view', $c['alias']) ? '<a href="'.$url.$c['alias'].'">' : '').$c['name'].($acl->sruWalet('dorm', 'view', $c['alias']) ? '</a>' : '');
			echo '</td>';
			echo '<td style="text-align: right;">'.$c['usersMax'].'</td>';
			echo '<td style="text-align: right;">'.$people[$c['id']].'</td>';
			echo '<td style="text-align: right;">'.$freePlaces[$c['id']].'</td>';
			echo '<td style="text-align: right;">'.$overPlaces[$c['id']].'</td></tr>';
			$usersMax += $c['usersMax'];
			$userCount += $people[$c['id']];
			$usersFree += $freePlaces[$c['id']];
			$usersOver += $overPlaces[$c['id']];
		}
		echo '</tbody>';
		echo '<tr><td><b>SUMA</b></td><td style="text-align: right;"><b>'.$usersMax.'</b></td><td style="text-align: right;"><b>'.$userCount.'</b></td><td style="text-align: right;"><b>'.$usersFree.'</b></td><td style="text-align: right;"><b>'.$usersOver.'</b></td></tr>';
		echo '</table>';
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#inhabitantsT").tablesorter({
			headers: {
				0: {
					sorter: "dslong"
				}
			}
		});
    } 
);
</script>
<?
	}
	
	public function exportPanel(array $d) {
		
		echo '<h3><span id="exportMoreSwitch"></span></h3>';
		echo '<div id="userMore">';
		$form = UFra::factory('UFlib_Form', 'docExport', $d);
		echo $form->_start();
		echo $form->_fieldset('Dane eksportu');
		$tmp = array(
			'1' => 'Lista mieszkańców wg pokoi',
			'2' => 'Lista mieszkańców alfabetycznie',
			'3' => 'Książka meldunkowa',
		);
		echo $form->docTypeId('Dokument', array(
			'type' => $form->RADIO,
			'labels' => $form->_labelize($tmp),
			'labelClass' => 'radio',
			'class' => 'radio',
			'value' => 1,
		));
		$tmp = array(
			'1' => 'MS Word&#153;',
			'2' => 'MS Excel&#153;',
		);
		echo $form->formatTypeId('Format', array(
			'type' => $form->RADIO,
			'labels' => $form->_labelize($tmp),
			'labelClass' => 'radio',
			'class' => 'radio',
			'value' => 2,
		));
		echo $form->_fieldset('Uzupełnij o');
		echo $form->addFaculty('Wydział', array(
			'type' => $form->CHECKBOX,
		));
		echo $form->addYear('Rok studiów', array(
			'type' => $form->CHECKBOX,
		));
		echo $form->_end();
		echo $form->_submit('Eksportuj');
		echo $form->_end();
		echo $form->_end(true);
		echo '</div>';
		
		?><script type="text/javascript">
(function (){
	var dormList = document.getElementById('docExport_docTypeId_1');
	var regBook = document.getElementById('docExport_docTypeId_3');
	var addFaculty = document.getElementById('docExport_addFaculty');
	var addFacultyState = document.getElementById('docExport_addFaculty').checked;
	var addYear = document.getElementById('docExport_addYear');
	var addYearState = document.getElementById('docExport_addYear').checked;
	var inputs = document.getElementsByName('docExport[docTypeId]');
	for(i=0; i<inputs.length; i++) {
		inputs[i].addEventListener('change', changeState, false);
	}
	function changeState() {
		if(dormList.checked == true) {
				addFaculty.checked = false;
				addFaculty.disabled = true;
				addYear.checked = false;
				addYear.disabled = true;
			} else if(regBook.checked == true) {
				addFaculty.checked = true;
				addFaculty.disabled = true;
				addYear.checked = addYearState;
				addYear.disabled = false;
			} else {
				addFaculty.checked = addFacultyState;
				addFaculty.disabled = false;
				addYear.checked = addYearState;
				addYear.disabled = false;
			}
	}
	changeState();
	function changeAddFaculty() { 
		addFacultyState = document.getElementById('docExport_addFaculty').checked;
	}
	addFaculty.onchange = changeAddFaculty;
	function changeAddYear() { 
		addYearState = document.getElementById('docExport_addYear').checked;
	}
	addYear.onchange = changeAddYear;
})()
function changeVisibility() {
	var div = document.getElementById('userMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('exportMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Eksportuj do pliku');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function inhabitantsAlphabetically(array $d, $users, $settings) {
		echo '<table><thead><tr>';
		echo '<th>Imię</th>';
		echo '<th>Nazwisko</th>';
		echo '<th>Pokój</th>';
		if ($settings['faculty']) {
			echo '<th>Wydział</th>';
		}
		if ($settings['year']) {
			echo '<th>Rok studiów</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ($users as $user) {
			if ($user['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			echo '<tr><td style="border: 1px solid;">'.$user['name'].'</td>';
			echo '<td style="border: 1px solid;">'.$user['surname'].'</td>';
			echo '<td style="border: 1px solid;">'.$user['locationAlias'].'</td>';
			if ($settings['faculty']) {
				echo '<td style="border: 1px solid;">'.(is_null($user['facultyId']) ? '&nbsp;' : strtoupper($user['facultyAlias'])).'</td>';
			}
			if ($settings['year']) {
				echo '<td style="border: 1px solid;">'.UFtpl_Sru_User::$studyYears[$user['studyYearId']].'</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	public function regBook(array $d, $users, $settings) {
		echo '<table><thead><tr>';
		echo '<th>L.p.</th>';
		echo '<th>Nazwisko</th>';
		echo '<th>Imię</th>';
		echo '<th>Nr pokoju</th>';
		echo '<th>Data urodzenia</th>';
		echo '<th>Adres pobytu stałego</th>';
		echo '<th>Pobyt od</th>';
		echo '<th>Pobyt do</th>';
		echo '<th>Oznaczenie dokumentu tożsamości</th>';
		echo '<th>Wydział</th>';
		if ($settings['year']) {
			echo '<th>Rok studiów</th>';
		}
		echo '<th>Nr albumu</th>';
		echo '<th>Uwagi</th>';
		echo '</tr></thead><tbody>';
		$i = 0;
		foreach ($users as $user) {
			if ($user['typeId'] > UFtpl_Sru_User::$userTypesLimit) {
				continue;
			}
			echo '<tr><td style="border: 1px solid;">'.++$i.'</td>';
			echo '<td style="border: 1px solid;">'.$user['surname'].'</td>';
			echo '<td style="border: 1px solid;">'.$user['name'].'</td>';
			echo '<td style="border: 1px solid;">'.$user['locationAlias'].'</td>';
			echo '<td style="border: 1px solid;">'.(is_null($user['birthDate']) ? '&nbsp;' : date(self::TIME_YYMMDD, $user['birthDate'])).'</td>';
			echo '<td style="border: 1px solid;">'.(is_null($user['address']) ? '&nbsp;' : $user['address']).'</td>';
			echo '<td style="border: 1px solid;">'.date(self::TIME_YYMMDD, $user['referralStart']).'</td>';
			echo '<td style="border: 1px solid;">'.((is_null($user['referralEnd']) || $user['referralEnd'] == 0) ? '' : date(self::TIME_YYMMDD, $user['referralEnd'])).'</td>';
			echo '<td style="border: 1px solid;">'.(is_null($user['documentNumber']) ? '&nbsp;' : UFtpl_Sru_User::$documentTypesShort[$user['documentType']].': '.$user['documentNumber']).'</td>';
			echo '<td style="border: 1px solid;">'.(is_null($user['facultyId']) ? '&nbsp;' : strtoupper($user['facultyAlias'])).'</td>';
			if ($settings['year']) {
				echo '<td style="border: 1px solid;">'.UFtpl_Sru_User::$studyYears[$user['studyYearId']].'</td>';
			}
			echo '<td style="border: 1px solid;">'.(is_null($user['registryNo']) ? '&nbsp;' : $user['registryNo']).'</td>';
			echo '<td style="border: 1px solid;">&nbsp;</td></tr>';
		}
		echo '</tbody></table>';
	}
}
