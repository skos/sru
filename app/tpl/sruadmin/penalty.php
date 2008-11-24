<?
/**
 * szablon beana admina
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
	);	

	public function listPenalty(array $d) {
		$url = $this->url(0);
		echo '<h3>Wszystkich kar: '. count($d) .'</h3>';

		foreach ($d as $c) {	
			echo '<li>';
			echo '<small>'.date(self::TIME_YYMMDD, $c['startAt']).' &mdash; '.date(self::TIME_YYMMDD, $c['endAt']).'</small> ';
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).' ('.$this->_escape($c['userLogin']).')</a>';
			echo '</li>';
		}
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
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">Typ: '.$this->_escape($this->penaltyTypes[$c['typeId']]).'</a>';
			if ($c['endAt'] > time()) {
				echo '</b>';
			}
			echo '</li>';
		}
	}

	public function formAdd(array $d, $computers ) {
		if (!isset($d['duration'])) {
			$d['duration'] = 30;
		}

		$form = UFra::factory('UFlib_Form', 'penaltyAdd', $d, $this->errors);
		
		$computers->write('penaltyAdd', $d);

		echo $form->duration('Długość (w dobach)');
		echo $form->reason('Opis dla użytkownika',  array('type'=>$form->TEXTAREA, 'rows'=>5));

		echo $form->comment('Opis dla administratorów', array('type'=>$form->TEXTAREA, 'rows'=>10));
	}

	public function details(array $d, $computers) {
		$d['endAt'] = date(self::TIME_YYMMDD_HHMM, $d['endAt']);
		$url = $this->url(0);
		$form = UFra::factory('UFlib_Form', 'penaltyEdit', $d);
		
		echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).' ('.$d['userLogin'].')</a></p>';
		echo $form->_start();
		echo '<p><em>Czas trwania:</em> ';

		if ($d['active'] || is_null($computers)) {
			echo date(self::TIME_YYMMDD, $d['startAt']).' &mdash; ';
			echo $form->endAt(null, array('after'=>'')).' ';
			echo $form->_submit('Zmień');
		} else {
			echo date(self::TIME_YYMMDD, $d['startAt']).' &mdash; <strong>'.$d['endAt'].'</strong>';
		}
		echo '</p>';
		echo $form->_end(true);

		if (!is_null($computers)) {
			$computers->write('computerList');
		}
		echo '<p><em>Powód:</em> '.nl2br($this->_escape($d['reason'])).'</p>';
		echo '<span id="penaltyMoreSwitch"></span><div id="penaltyMore">';
		echo '<p class="displayOnHover"><em>Karzący:</em> <span><a href="'.$url.'/admins/'.$d['createdById'].'">'.$this->_escape($d['createdByName']).'</a><small> ('.date(self::TIME_YYMMDD_HHMM, $d['createdAt']) .')</small></span></p>';

		if($d['modifiedById']) {
			echo '<p><em>Modyfikacja:</em> <a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedByName']). '</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).')</small></p>';							
		}
		
		if($d['amnestyById']) {
			echo '<p><em>Amnestia:</em> <a href="'.$url.'/admins/'.$d['amnestyById'].'">'.$this->_escape($d['amnestyByName']).'</a> <small>('.date(self::TIME_YYMMDD_HHMM, $d['amnestyAt']).')</small></p>';							
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
}
