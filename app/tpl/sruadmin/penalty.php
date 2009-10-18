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
				echo $form->after('Min. długość (dni)', array('value'=>$amnestyDays));
				echo $form->reason('Powód:', array('type'=>$form->TEXTAREA, 'rows'=>5));
				echo $form->newComment('Komentarz:', array('type'=>$form->TEXTAREA, 'rows'=>5, 'value'=>$d['comment']));
				echo $form->_submit('Zmień');
				echo $form->_end();
				echo $form->_end(true);
		} else {
			echo '<p><em>Powód:</em> '.nl2br($this->_escape($d['reason'])).'</p>';
		}
		echo '<span id="penaltyMoreSwitch"></span> <a href="'.$url.'/penalties/'.$d['id'].'/history/">Historia kary</a><div id="penaltyMore">';
		echo '<p class="displayOnHover"><em>Karzący:</em> <span><a href="'.$url.'/admins/'.$d['createdById'].'">'.$this->_escape($d['createdByName']).'</a><small> ('.date(self::TIME_YYMMDD_HHMM, $d['createdAt']) .')</small></span></p>';

		if($d['modifiedById']) {
			echo '<p class="displayOnHover"><em>Ostatnia modyfikacja:</em> <span><a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedByName']). '</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).')</small></span></p>';							
		}
		
		if($d['amnestyById']) {
			echo '<p class="displayOnHover"><em>Amnestia:</em> <span><a href="'.$url.'/admins/'.$d['amnestyById'].'">'.$this->_escape($d['amnestyByName']).'</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['amnestyAt']).')</small></span></p>';							
		}	
		
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
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
var txt = document.createTextNode('Szczegóły...');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function apiPast(array $d) {
		foreach ($d as $p) {	
			echo $p['id']."\n";
		}
	}

	public function stats(array $d) {
		$banners = array();
		$templates = array();
		$types = array();
		$modified = 0;
		$amnestied = 0;
		foreach ($d as $p) {
			if(!array_key_exists($p['creatorName'], $banners)) {
				$banners[$p['creatorName']] = 1;
			} else {
				$banners[$p['creatorName']]++;
			}

			if ($p['templateTitle'] == '') {
				$p['templateTitle'] = 'N/D';
			}
			if(!array_key_exists($p['templateTitle'], $templates)) {
				$templates[$p['templateTitle']] = 1;
			} else {
				$templates[$p['templateTitle']]++;
			}

			if(!array_key_exists($p['typeId'], $types)) {
				$types[$p['typeId']] = 1;
			} else {
				$types[$p['typeId']]++;
			}
			if ($p['modifiedById'] != '') {
				$modified++;
			}
			if ($p['amnestyById'] != '') {
				$amnestied++;
			}
		}

		echo '<h3>Top 10 karzących:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Admin</th><th>Ilość kar</th></tr>';
		arsort($banners);
		$i = 0;
		$chartData = '';
		$chartLabel = '';
		while ($b = current ($banners)) {
			echo '<tr><td>'.key($banners).'</td><td>'.$b.'</td></tr>';
			$chartData = $chartData.$b.',';
			$chartLabel = str_replace('"', '\'', key($banners)).'|'.$chartLabel;
			$i++;
			if ($i >= 10) {
				break;
			}
			next ($banners);
		}
		echo '</table>';
		reset($banners);
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x290&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($banners).'" alt=""/>';
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

		echo '<h3>Kary modyfikowane i zdjęte:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Kara</th><th>Ilość</th></tr>';
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
