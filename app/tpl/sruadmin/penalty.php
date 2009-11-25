<?
/**
 * szablon beana kary - admin
 */
class UFtpl_SruAdmin_Penalty
extends UFtpl_Common {

	protected $penaltyTypes = array(
		1 => 'Ostrzeżenie',
		2 => 'Jeden komputer',
		3 => 'Wszystkie komputery',
	);
		
	protected $errors = array(
		'reason' => 'Podaj opis',
		'duration' => 'Podaj długość',
		'after' => 'Podaj minimalną długość',
		'newComment/notNull' => 'Podaj komentarz modyfikacji',
		'endAt/tooShort' => 'Nie możesz skrócić kary poniżej minimalnego czasu',
	);	

	public function listPenalty(array $d) {
		$url = $this->url(0);
		echo '<h3>Wszystkich kar: '. count($d) .'</h3><ul>';

		foreach ($d as $c) {	
			echo '<li>';
			echo '<small>'.date(self::TIME_YYMMDD, $c['startAt']).' &mdash; '.date(self::TIME_YYMMDD, $c['endAt']).'</small> ';
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).' ('.$this->_escape($c['userLogin']).')</a>';
			echo ($this->_escape($c['templateTitle']) != null ? ' <small>za: '.$this->_escape($c['templateTitle']).'</small>' : '');
			echo '</li>';
		}
		echo '</ul>';
	}

	public function listUserPenalty(array $d) {
		$url = $this->url(0);
		echo '<h3>Wszystkich kar i ostrzeżeń: '. count($d) .'</h3>';

		foreach ($d as $c) {	
			if ($c['endAt'] > time()) {
				if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
					echo '<li class="warning">';
				} else {
					echo '<li class="ban">';
				}
				echo '<b>';
			} else {
				echo '<li>';
			}
			echo '<small>'.date(self::TIME_YYMMDD, $c['startAt']).' &mdash; '.date(self::TIME_YYMMDD_HHMM, $c['endAt']).'</small> ';
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">';
			echo ($this->_escape($c['templateTitle']) != null ? 'za: '.$this->_escape($c['templateTitle']) : $this->_escape($this->penaltyTypes[$c['typeId']]).' (nieznany szablon)');
			echo'</a>';
			if ($c['endAt'] > time()) {
				echo '</b>';
			}
			echo '</li>';
		}
	}

	public function formAdd(array $d, $computers, $templates, $computerId=null) {
		if (!isset($d['computerId']) && is_int($computerId)) {
			$d['computerId'] = $computerId;
		}
		if (!isset($d['after'])) {
			$d['after'] = 0;
		}

		$form = UFra::factory('UFlib_Form', 'penaltyAdd', $d, $this->errors);
		
		$computers->write('penaltyAdd', $d);

		echo $form->duration('Długość (dni)');
		echo $form->after('Min. długość (dni)');
		echo $form->reason('Opis dla użytkownika',  array('type'=>$form->TEXTAREA, 'rows'=>5));

		echo $form->comment('Opis dla administratorów', array('type'=>$form->TEXTAREA, 'rows'=>10));
	}

	public function penaltyLastAdded(array $d, $showAddedBy = true) {
		$url = $this->url(0);

		foreach ($d as $c) {	
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['startAt']);
			echo ' dla: <a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>';
			if ($showAddedBy == true) {
				echo ' <small>przez: <a href="'.$url.'/admins/'.$c['createdById'].'">'.$this->_escape($c['creatorName']).'</a>';
			} else {
				echo ' <small>';
			}
			echo ($this->_escape($c['templateTitle']) != null ? ' za: '.$this->_escape($c['templateTitle']) : '');
			echo '</small></li>';
		}
	}

	public function penaltyLastModified(array $d, $showAddedBy = true) {
		$url = $this->url(0);

		foreach ($d as $c) {	
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			echo ' dla: <a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).' </a>';
			if ($showAddedBy == true) {
				echo ' <small>przez: <a href="'.$url.'/admins/'.$c['modifiedById'].'">'.$this->_escape($c['modifierName']).'</a>';
			} else {
				echo ' <small>';
			}
			echo ($this->_escape($c['templateTitle']) != null ? ' za: '.$this->_escape($c['templateTitle']) : '');
			echo '</small></li>';
		}
	}

	public function details(array $d, $computers) {
		$d['endAt'] = date(self::TIME_YYMMDD_HHMM, $d['endAt']);
		$url = $this->url(0);
		
		echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).' ('.$d['userLogin'].')</a></p>';

		if ($d['active']) {
			$acl = $this->_srv->get('acl');
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();
			$d['admin'] = $admin;
			if ($acl->sruAdmin('penalty', 'editOne', $d['id'])) {
				$form = UFra::factory('UFlib_Form', 'penaltyEdit', $d, $this->errors);
				echo $form->_start();
				echo $form->_fieldset('Edycja kary');
				echo $form->endAt('Od '.date(self::TIME_YYMMDD, $d['startAt']).' do');
			} else {
				echo '<p><em>Kara trwa:</em> '.date(self::TIME_YYMMDD_HHMM, $d['startAt']).' &mdash; <strong>'.$d['endAt'].'</strong>'.($d['amnestyAfter']<$d['endAt']?' (amnestia będzie możliwa po '.date(self::TIME_YYMMDD_HHMM, $d['amnestyAfter']).')':'').'</p>';
			}
		} else {
			echo '<p><em>Kara trwała:</em> '.date(self::TIME_YYMMDD_HHMM, $d['startAt']).' &mdash; '.$d['endAt'].'</p>';
		}

		if (!is_null($computers)) {
			$computers->write('computerList');
		}
		if (!is_null($d['templateTitle'])) {
			echo '<p><em>Szablon:</em> '.$this->_escape($d['templateTitle']).'</p>';
		}
		if ($d['active']) {
			$amnestyDays = ($d['amnestyAfter'] - $d['startAt']) / 24 / 3600;
			if ($acl->sruAdmin('penalty', 'editOneFull', $d['id'])) {
				echo $form->after('Min. długość (dni)', array('value'=>$amnestyDays));
				echo $form->reason('Powód:', array('type'=>$form->TEXTAREA, 'rows'=>5));
				echo $form->newComment('Komentarz:', array('type'=>$form->TEXTAREA, 'rows'=>5, 'value'=>$d['comment']));
			} else {
				echo '<p><em>Min. długość:</em> '.$amnestyDays.' dni</p>';
			}
		} else {
			echo '<p><em>Powód:</em> '.nl2br($this->_escape($d['reason'])).'</p>';
		}
		if ($d['active'] && $acl->sruAdmin('penalty', 'editOne', $d['id'])) {
			echo $form->_submit('Zmień');
			echo $form->_end();
			echo $form->_end(true);	
		}
		$urlPenalty = $url.'/penalties/'.$d['id'];
		echo '<p class="nav"><a href="'.$urlPenalty.'">Dane</a> <a href="'.$url.'/penalties/'.$d['id'].'/history/">Historia kary</a> <span id="penaltyMoreSwitch"></span></p>';
		echo '<div id="penaltyMore">';
		echo '<p class="displayOnHover"><em>Karzący:</em> <span><a href="'.$url.'/admins/'.$d['createdById'].'">'.$this->_escape($d['createdByName']).'</a><small> ('.date(self::TIME_YYMMDD_HHMM, $d['createdAt']) .')</small></span></p>';

		if($d['modifiedById']) {
			echo '<p class="displayOnHover"><em>Ost. modyfikacja:</em> <span><a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedByName']). '</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).')</small></span></p>';							
		}
		
		if($d['amnestyById']) {
			echo '<p class="displayOnHover"><em>Amnestia:</em> <span><a href="'.$url.'/admins/'.$d['amnestyById'].'">'.$this->_escape($d['amnestyByName']).'</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['amnestyAt']).')</small></span></p>';							
		}	
		
		echo '<p><em>Ost. komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		echo '<p><em>Typ:</em> '.$this->_escape($this->penaltyTypes[$d['typeId']]).'</p>';
		echo '</div>';
?><script type="text/javascript">
function changeVisibility() {
	var div = document.getElementById('penaltyMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('penaltyMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Szczegóły');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
<?
		if ($d['active'] && $acl->sruAdmin('penalty', 'editOne', $d['id'])) {
?>
input = document.getElementById('penaltyEdit_endAt');
if (input) {
	button = document.createElement('input');
	button.setAttribute('value', 'Zdejmij karę');
	button.setAttribute('type', 'button');
	button.onclick = function() {
		input = document.getElementById('penaltyEdit_endAt');
		input.value = '';
		input = document.getElementById('penaltyEdit_newComment');
		input.value = '';
		input.focus();
	}
	input.parentNode.insertBefore(button, input.nextSibling);
	space = document.createTextNode(' ');
	input.parentNode.insertBefore(space, input.nextSibling);
}
<?
		}
?>
</script>
<?
	}

	public function apiPast(array $d) {
		foreach ($d as $p) {	
			echo $p['id']."\n";
		}
	}

	public function stats(array $d) {
		$activePenalties = 0;
		$banners = array();
		$bannersActive = array();
		$warningers = array();
		$warningersActive = array();
		$admins = array();
		$templates = array();
		$templatesActive = array();
		$types = array();
		$typesActive = array();
		$modified = 0;
		$amnestied = 0;
		foreach ($d as $p) {
			if ($p['active'] === true) {
				$activePenalties++;
			}
			if(!array_key_exists($p['creatorName'], $admins)) {
				$admins[$p['creatorName']] = $p['createdById'];
				$warningers[$p['creatorName']] = 0;
				$warningersActive[$p['creatorName']] = 0;
				$banners[$p['creatorName']] = 0;
				$bannersActive[$p['creatorName']] = 0;
			}
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $p['typeId']) {
				$warningers[$p['creatorName']]++;
				if ($p['active'] === true) {
					$warningersActive[$p['creatorName']]++;
				}
			} else {
				$banners[$p['creatorName']]++;
				if ($p['active'] === true) {
					$bannersActive[$p['creatorName']]++;
				}
			}

			if ($p['templateTitle'] == '') {
				$p['templateTitle'] = 'N/D';
			}
			if(!array_key_exists($p['templateTitle'], $templates)) {
				$templates[$p['templateTitle']] = 1;
				if ($p['active'] === true) {
					$templatesActive[$p['templateTitle']] = 1;
				}
			} else {
				$templates[$p['templateTitle']]++;
				if ($p['active'] === true) {
					if(!array_key_exists($p['templateTitle'], $templatesActive)) {
						$templatesActive[$p['templateTitle']] = 1;
					} else {
						$templatesActive[$p['templateTitle']]++;
					}
				}
			}

			if(!array_key_exists($p['typeId'], $types)) {
				$types[$p['typeId']] = 1;
				if ($p['active'] === true) {
					$typesActive[$p['typeId']] = 1;
				}
			} else {
				$types[$p['typeId']]++;
				if ($p['active'] === true) {
					if(!array_key_exists($p['typeId'], $typesActive)) {
						$typesActive[$p['typeId']] = 1;
					} else {
						$typesActive[$p['typeId']]++;
					}
				}
			}
			if ($p['modifiedById'] != '') {
				$modified++;
			}
			if ($p['amnestyById'] != '') {
				$amnestied++;
			}
		}

		echo '<h3>Liczba nałożonych kar przez poszczególnych adminów:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Admin</th><th>Liczba kar</th><th>Liczba ostrzeżeń</th></tr>';
		arsort($banners);
		$i = 0;
		$chartData = '';
		$chartDataW = '';
		$chartLabel = '';
		while ($b = current ($banners)) {
			$urlAdmin = $this->url(0).'/admins/'.$admins[key($banners)];
			$warningsNum = $warningers[key($banners)];
			echo '<tr><td><a href="'.$urlAdmin.'">'.key($banners).'</td></a><td>'.$b.'</td><td>'.$warningsNum.'</td></tr>';
			$chartData = $chartData.$b.',';
			$chartDataW = $chartDataW.$warningsNum.',';
			$chartLabel = str_replace('"', '\'', key($banners)).'|'.$chartLabel;
			$i++;
			next ($banners);
		}
		echo '</table>';
		reset($banners);
		$chartData = substr($chartData, 0, -1);
		$chartDataW = substr($chartDataW, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x'.($i*28+5).'&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'|'.$chartDataW.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($banners).'" alt=""/>';
		echo '</div>';

		echo '<h3>Liczba nałożonych kar przez poszczególnych adminów (aktywne kary):</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Admin</th><th>Liczba kar</th><th>Liczba ostrzeżeń</th></tr>';
		arsort($bannersActive);
		$i = 0;
		$chartData = '';
		$chartDataW = '';
		$chartLabel = '';
		while ($b = current ($bannersActive)) {
			$urlAdmin = $this->url(0).'/admins/'.$admins[key($bannersActive)];
			$warningsNum = $warningersActive[key($bannersActive)];
			echo '<tr><td><a href="'.$urlAdmin.'">'.key($bannersActive).'</td></a><td>'.$b.'</td><td>'.$warningsNum.'</td></tr>';
			$chartData = $chartData.$b.',';
			$chartDataW = $chartDataW.$warningsNum.',';
			$chartLabel = str_replace('"', '\'', key($bannersActive)).'|'.$chartLabel;
			$i++;
			next ($bannersActive);
		}
		echo '</table>';
		reset($bannersActive);
		$chartData = substr($chartData, 0, -1);
		$chartDataW = substr($chartDataW, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x'.($i * 28+5).'&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'|'.$chartDataW.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($bannersActive).'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład używanych szablonów kar:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Szablon</th><th>Kar</th></tr>';
		arsort($templates);
		$chartData = '';
		$chartLabel = '';
		while ($t = current ($templates)) {
			echo '<tr><td>'.key($templates).'</td>';
			echo '<td>'.$t.'</td></tr>';
			$chartData = (round($t/count($d)*100)).','.$chartData;
			$chartLabel = key($templates).': '.(round($t/count($d)*100)).'%|'.$chartLabel;
			next($templates);
		}
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład używanych szablonów aktywnych kar:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Szablon</th><th>Kar</th></tr>';
		arsort($templatesActive);
		$chartData = '';
		$chartLabel = '';
		while ($t = current ($templatesActive)) {
			echo '<tr><td>'.key($templatesActive).'</td>';
			echo '<td>'.$t.'</td></tr>';
			$chartData = (round($t/$activePenalties*100)).','.$chartData;
			$chartLabel = key($templatesActive).': '.(round($t/$activePenalties*100)).'%|'.$chartLabel;
			next($templatesActive);
		}
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład kar wg typu:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Typ</th><th>Kar</th></tr>';
		arsort($types);
		$chartData = '';
		$chartLabel = '';
		while ($t = current ($types)) {
			echo '<tr><td>'.$this->penaltyTypes[key($types)].'</td>';
			echo '<td>'.$t.'</td></tr>';
			$chartData = (round($t/count($d)*100)).','.$chartData;
			$chartLabel = $this->penaltyTypes[key($types)].': '.(round($t/count($d)*100)).'%|'.$chartLabel;
			next($types);
		}
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład aktywnych kar wg typu:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Typ</th><th>Kar</th></tr>';
		arsort($typesActive);
		$chartData = '';
		$chartLabel = '';
		while ($t = current ($typesActive)) {
			echo '<tr><td>'.$this->penaltyTypes[key($typesActive)].'</td>';
			echo '<td>'.$t.'</td></tr>';
			$chartData = (round($t/$activePenalties*100)).','.$chartData;
			$chartLabel = $this->penaltyTypes[key($typesActive)].': '.(round($t/$activePenalties*100)).'%|'.$chartLabel;
			next($typesActive);
		}
		echo '</table>';
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x150&chd=t:'.$chartData;
		echo '&cht=p3&chl='.$chartLabel.'" alt=""/>';
		echo '</div>';

		echo '<h3>Kary modyfikowane i zdjęte:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Kara</th><th>Liczba</th></tr>';
		echo '<tr><td>Wszystkie</td><td>'.count($d).'</td></tr>';
		echo '<tr><td>Modyfikowane</td><td>'.$modified.'</td></tr>';
		echo '<tr><td>Zdjęte</td><td>'.$amnestied.'</td></tr>';
		echo '</table>';
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x110&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo count($d).','.$modified.','.$amnestied.'&chxt=y&chxl=0:|Zdjęte|Modyfikowane|Wszystkie&chds=0,'.count($d).'" alt=""/>';
		echo '</div>';
	}
}
