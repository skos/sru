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
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId'] && $c['endAt'] > time()) {
				echo '<li class="warning"><b>';
			} else if (UFbean_SruAdmin_Penalty::TYPE_WARNING != $c['typeId'] && $c['active']) {
				echo '<li class="ban"><b>';
			} else {
				echo '<li>';
			}
			echo '<small>'.date(self::TIME_YYMMDD, $c['startAt']).' &mdash; '.date(self::TIME_YYMMDD_HHMM, $c['endAt']).'</small> ';
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">';
			echo ($this->_escape($c['templateTitle']) != null ? 'za: '.$this->_escape($c['templateTitle']) : $this->_escape($this->penaltyTypes[$c['typeId']]).' (nieznany szablon)');
			echo'</a>';
			if ((UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId'] && $c['endAt'] > time())
				||(UFbean_SruAdmin_Penalty::TYPE_WARNING != $c['typeId'] && $c['active'])) {
				echo '</b>';
			}
			echo '</li>';
		}
	}

	public function formAdd(array $d, $computers, $templates, $user, $computerId = null) {
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
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId'] && $c['endAt'] > time()) {
				echo '<li class="warning">';
			} else if (UFbean_SruAdmin_Penalty::TYPE_WARNING != $c['typeId'] && $c['active']) {
				echo '<li class="ban">';
			} else {
				echo '<li>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $c['startAt']);
			echo ' dla: <a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' "'.$c['userLogin'].'" '.$this->_escape($c['userSurname']).'</a>';
			if ($showAddedBy == true) {
				echo ' <small>przez: <a href="'.$url.'/admins/'.$c['createdById'].'">'.$this->_escape($c['creatorName']).'</a>';
			} else {
				echo ' <small>';
			}
			echo ($this->_escape($c['templateTitle']) != null ? ' za: '.$this->_escape($c['templateTitle']) : '');
			echo '</small></li>';
		}
	}

	public function penaltyLastModified(array $d) {
		$url = $this->url(0);

		foreach ($d as $c) {	
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId'] && $c['endAt'] > time()) {
				echo '<li class="warning">';
			} else if (UFbean_SruAdmin_Penalty::TYPE_WARNING != $c['typeId'] && $c['active']) {
				echo '<li class="ban">';
			} else {
				echo '<li>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			echo ' dla: <a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' "'.$c['userLogin'].'" '.$this->_escape($c['userSurname']).'</a> ';
			echo ' <small>modyfikowana '.$c['modificationCount'].' raz(y)</small>';
			echo '<small>, ostatnio przez: <a href="'.$url.'/admins/'.$c['modifiedById'].'">'.$this->_escape($c['modifierName']).'</a>';
			echo ($this->_escape($c['templateTitle']) != null ? ', za: '.$this->_escape($c['templateTitle']) : '');
			echo '</small></li>';
		}
	}

	public function details(array $d, $computers) {
		$endAtTimestamp = $d['endAt'];
		$d['endAt'] = date(self::TIME_YYMMDD_HHMM, $d['endAt']);
		$url = $this->url(0);
		
		echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).' ('.$d['userLogin'].')</a></p>';

		if ($d['active']) {
			echo '<p><em>Kara trwa:</em> '.date(self::TIME_YYMMDD_HHMM, $d['startAt']).' &mdash; <strong>'.$d['endAt'].'</strong>'.($d['amnestyAfter']<$endAtTimestamp?' (modyfikacja możliwa po '.date(self::TIME_YYMMDD_HHMM, $d['amnestyAfter']).')':'').'</p>';
		} else {
			echo '<p><em>Kara trwała:</em> '.date(self::TIME_YYMMDD_HHMM, $d['startAt']).' &mdash; '.$d['endAt'].'</p>';
		}

		if (!is_null($computers)) {
			$computers->write('computerList');
		}
		if (!is_null($d['templateTitle'])) {
			echo '<p><em>Szablon:</em> '.$this->_escape($d['templateTitle']).'</p>';
		}

		$amnestyDays = ($d['amnestyAfter'] - $d['startAt']) / 24 / 3600;
		echo '<p><em>Min. długość:</em> '.$amnestyDays.' dni</p>';
		echo '<p><em>Powód:</em> '.nl2br($this->_escape($d['reason'])).'</p>';

		$urlPenalty = $url.'/penalties/'.$d['id'];
		echo '<p class="nav"><a href="'.$urlPenalty.'">Dane</a> &bull; 
			<a href="'.$url.'/penalties/'.$d['id'].'/history/">Historia kary</a> &bull; ';
		$acl = $this->_srv->get('acl');
		if ($acl->sruAdmin('penalty', 'editOne', $d['id'])) {
			echo '<a href="'.$urlPenalty.'/:edit">Edycja</a> &bull; ';
		}
		echo '<span id="penaltyMoreSwitch"></span></p>';
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
</script><?
	}

	public function formEdit(array $d, $computers, $templateTitle = null) {
		$d['endAt'] = date(self::TIME_YYMMDD_HHMM, $d['endAt']);
		$url = $this->url(0);
		echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).' ('.$d['userLogin'].')</a></p>';

		$acl = $this->_srv->get('acl');
		$admin = UFra::factory('UFbean_SruAdmin_Admin');
		$admin->getFromSession();
		$d['admin'] = $admin;
		$form = UFra::factory('UFlib_Form', 'penaltyEdit', $d, $this->errors);
		echo $form->_start();
		echo $form->_fieldset('Edycja kary');
		echo $form->endAt('Od '.date(self::TIME_YYMMDD, $d['startAt']).' do');
		echo '<p><em>Typ:</em> '.$this->_escape($this->penaltyTypes[$d['typeId']]).'</p>';

		if (!is_null($computers)) {
			$computers->write('computerList');
		}

		$amnestyDays = (!isset($_POST['penaltyEdit']['after'])) ? (($d['amnestyAfter'] - $d['startAt']) / 24 / 3600) : (intval($_POST['penaltyEdit']['after']));
		$urlPenalty = $url.'/penalties/'.$d['id'];
		echo '<p><em>Szablon:</em> ';
		if (isset($templateTitle)) {
			echo $this->_escape($templateTitle);
		} else if (!is_null($d['templateTitle'])) {
			echo $this->_escape($d['templateTitle']);
		} else {
			echo 'brak';
		}
		if ($acl->sruAdmin('penalty', 'editOneFull', $d['id'])) {
			echo ' <a href="'.$urlPenalty.'/:edit/changeTemplate">zmień</a></p>';
			echo $form->after('Min. długość (dni)', array('value'=>$amnestyDays));
			echo $form->reason('Powód:', array('type'=>$form->TEXTAREA, 'rows'=>5));
		} else {
			echo '</p>';
			echo '<p><em>Min. długość:</em> '.$amnestyDays.' dni</p>';
		}
		echo $form->newComment('Komentarz:', array('type'=>$form->TEXTAREA, 'rows'=>10, 'value'=>$d['comment']));
		echo $form->_submit('Zmień');
		echo $form->_end();
		echo $form->_end(true);
		echo '<p class="nav"><a href="'.$urlPenalty.'">Dane</a> <a href="'.$url.'/penalties/'.$d['id'].'/history/">Historia kary</a></p>';
                                                                                                                 
?><script type="text/javascript">
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
</script><?
	}

	public function apiPast(array $d) {
		foreach ($d as $p) {	
			echo $p['id']."\n";
		}
	}

	public function penaltyAddMailTitlePolish(array $d) {
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Otrzymał(a/e)ś ostrzeżenie';
		} else {
			echo 'Otrzymał(a/e)ś karę';
		}
	}

	public function penaltyAddMailTitleEnglish(array $d) {
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'You got a new warning';
		} else {
			echo 'You got a new penalty';
		}
	}

	public function penaltyAddMailBodyPolish(array $d, $user, $computers) {
		echo 'Informujemy, że otrzymał(a/e)ś ';
		if ($d['penalty']-> typeId == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'OSTRZEŻENIE';
		} else {
			echo 'KARĘ';
		}
		echo ' w SKOS PG.'."\n";
		echo 'Trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['endAt'])."\n";
		if ($d['typeId'] != UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Kara nałożona na host(y): ';
			foreach ($computers as $computer) {
				if (is_array($computer)) {
					echo $computer['host'].' ';
				} else {
					echo $computer->host.' ';
				}
			}
		}
		echo "\n".'Powód: '.$d['reason']."\n";
		echo "\n".'Szczegółowe informacje znajdziesz w Systemie Rejestracji Użytkownika: http://sru.ds.pg.gda.pl';
		echo ' (Twój login to: '.$user->login.')';
		echo "\n\n";
	}

	public function penaltyAddMailBodyEnglish(array $d, $user, $computers) {
		echo 'We inform, that you got the ';
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'WARNING';
		} else {
			echo 'PENALTY';
		}
		echo ' in SKOS PG network.'."\n";
		echo 'Valid till: '.date(self::TIME_YYMMDD_HHMM, $d['endAt'])."\n";
		if ($d['typeId'] != UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Banned host(s): ';
			foreach ($computers as $computer) {
				if (is_array($computer)) {
					echo $computer['host'].' ';
				} else {
					echo $computer->host.' ';
				}
			}
		}
		echo "\n".'Reason: '.$d['reason']."\n";
		echo "\n".'You can find more information in User Register System: http://sru.ds.pg.gda.pl';
		echo ' (your login: '.$user->login.')';
		echo "\n";
	}

	public function penaltyEditMailTitlePolish(array $d) {
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Zmodyfikowano Twoje ostrzeżenie';
		} else {
			echo 'Zmodyfikowano Twoją karę';
		}
	}

	public function penaltyEditMailTitleEnglish(array $d) {
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Your warning was modified';
		} else {
			echo 'Your penalty was modified';
		}
	}
	
	public function penaltyEditMailBodyPolish(array $d, $user) {
		echo 'Informujemy, że zmodyfikowano ';
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'Twoje OSTRZEŻENIE';
		} else {
			echo 'Twoją KARĘ';
		}
		echo ' w SKOS PG.'."\n";
		echo 'Teraz trwa do: '.date(self::TIME_YYMMDD_HHMM, $d['endAt'])."\n";
		echo 'Powód: '.$d['reason']."\n";
		echo "\n".'Szczegółowe informacje znajdziesz w Systemie Rejestracji Użytkownika: http://sru.ds.pg.gda.pl';
		echo ' (Twój login to: '.$user->login.')';
		echo "\n\n";
	}

	public function penaltyEditMailBodyEnglish(array $d, $user) {
		echo 'We inform, that your ';
		if ($d['typeId'] == UFbean_SruAdmin_Penalty::TYPE_WARNING) {
			echo 'WARNING';
		} else {
			echo 'PENALTY';
		}
		echo ' was modified in SKOS PG network.'."\n";
		echo 'Now valid till: '.date(self::TIME_YYMMDD_HHMM, $d['endAt'])."\n";
		echo 'Reason: '.$d['reason']."\n";
		echo "\n".'You can find more information in User Register System: http://sru.ds.pg.gda.pl';
		echo ' (your login: '.$user->login.')';
		echo "\n";
	}

	public function stats(array $d) {
		$activePenalties = 0;
		$banners = array();
		$bannersActive = array();
		$warningers = array();
		$admins = array();
		$templates = array();
		$templatesActive = array();
		$types = array();
		$typesActive = array();
		$modified = 0;
		$amnestied = 0;
		$banSum = 0;
		$activeBanSum = 0;
		$warningSum = 0;
		foreach ($d as $p) {
			if ($p['active'] === true) {
				$activePenalties++;
			}
			if(!array_key_exists($p['creatorName'], $admins)) {
				$admins[$p['creatorName']] = $p['createdById'];
				$warningers[$p['creatorName']] = 0;
				$banners[$p['creatorName']] = 0;
				$bannersActive[$p['creatorName']] = 0;
			}
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $p['typeId']) {
				$warningers[$p['creatorName']]++;
				$warningSum++;
			} else {
				$banners[$p['creatorName']]++;
				$banSum++;
				if ($p['active'] === true) {
					$bannersActive[$p['creatorName']]++;
					$activeBanSum++;
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

		echo '<div class="stats">';
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
			if ($i < 10) {
				$chartData = $chartData.$b.',';
				$chartDataW = $chartDataW.$warningsNum.',';
				$chartLabel = str_replace('"', '\'', key($banners)).'|'.$chartLabel;
				$i++;
			}
			next ($banners);
		}
		echo '<td>ŚREDNIO:</td><td>'.round(($banSum/$i),1).'</td><td>'.round(($warningSum/$i),1).'</td>';
		echo '</table>';
		reset($banners);
		$chartData = substr($chartData, 0, -1);
		$chartDataW = substr($chartDataW, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x'.($i * 28+5).'&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'|'.$chartDataW.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($banners).'" alt=""/>';
		echo '</div>';

		echo '<h3>Liczba nałożonych aktywnych kar przez poszczególnych adminów:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Admin</th><th>Liczba kar</th></tr>';
		arsort($bannersActive);
		$i = 0;
		$chartData = '';
		$chartLabel = '';
		while ($b = current ($bannersActive)) {
			$urlAdmin = $this->url(0).'/admins/'.$admins[key($bannersActive)];
			echo '<tr><td><a href="'.$urlAdmin.'">'.key($bannersActive).'</td></a><td>'.$b.'</td></tr>';
			if ($i < 10) {
				$chartData = $chartData.$b.',';
				$chartLabel = str_replace('"', '\'', key($bannersActive)).'|'.$chartLabel;
				$i++;
			}
			next ($bannersActive);
		}
		echo '<td>ŚREDNIO:</td><td>'.round(($activeBanSum/$i),1).'</td>';
		echo '</table>';
		reset($bannersActive);
		$chartData = substr($chartData, 0, -1);
		echo '<div style="text-align: center;">';
		echo '<img src="http://chart.apis.google.com/chart?chs=600x'.($i * 28+5).'&cht=bhs&chco=ff9900,ffebcc&chd=t:';
		echo $chartData.'&chxt=y&chxl=0:|'.$chartLabel.'&chds=0,'.current($bannersActive).'" alt=""/>';
		echo '</div>';

		echo '<h3>Rozkład używanych szablonów kar:</h3>';
		echo '<table style="text-align: center; width: 100%;">';
		echo '<tr><th>Szablon</th><th>Kar</th></tr>';
		arsort($templates);
		$chartData = '';
		$chartLabel = '';
		$other = 0;
		while ($t = current ($templates)) {
			echo '<tr><td>'.key($templates).'</td>';
			echo '<td>'.$t.'</td></tr>';
			if ($t/count($d)*100 < 1) {
				$other = $other + $t;
			} else {
				$chartData = (round($t/count($d)*100)).','.$chartData;
				$chartLabel = key($templates).': '.(round($t/count($d)*100)).'%|'.$chartLabel;
			}
			next($templates);
		}
		echo '</table>';
		$chartData = (round($other/count($d)*100)).','.$chartData;
		$chartLabel = 'inne: '.(round($other/count($d)*100)).'%|'.$chartLabel;
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
		echo '</div>';
	}
}
