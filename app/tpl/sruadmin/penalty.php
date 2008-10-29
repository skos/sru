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

		foreach ($d as $c) {	
			echo '<li>';
			echo '<small>'.date(self::TIME_YYMMDD, $c['startAt']).' &mdash; '.date(self::TIME_YYMMDD, $c['endAt']).'</small> ';
			echo '<a href="'.$url.'/penalties/'.$c['id'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>';
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

	public function details(array $c, $computers) {
		$url = $this->url(0);
		
		echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).' ('.$c['userLogin'].')</a></p>';
		echo '<p><em>Czas trwania:</em> ';
		if (is_null($computers)) {
			echo '<strong>Ostrzeżenie</strong>';
		} else {
			echo '<strong>'.date(self::TIME_YYMMDD, $c['startAt']).'</strong> &mdash; <strong>'.date(self::TIME_YYMMDD, $c['endAt']).'</strong>';
		}
		echo '</p>';
		//@todo: lista ukaranych kompow
		if (!is_null($computers)) {
			$computers->write('computerList');
		}
		echo '<p><em>Powód:</em> '.nl2br($this->_escape($c['reason'])).'</p>';
		echo '<span id="penaltyMoreSwitch"></span><div id="penaltyMore">';
		echo '<p><em>Karzący:</em> <a href="'.$url.'/admins/'.$c['createdById'].'">'.$this->_escape($c['createdByName']).'</a><small> ('.date(self::TIME_YYMMDD_HHMM, $c['createdAt']) .')</small></p>';

		if($c['modifiedById']) {
			echo '<p><em>Modyfikacja:</em> <a href="'.$url.'/admins/'.$c['modifiedById'].'">'.$this->_escape($c['modifiedByName']). '</a> <small>('.date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']).')</small></p>';							
		}
		
		if($c['amnestyById']) {
			echo '<p><em>Amnestia udzielona przez:</em> <a href="'.$url.'/admins/'.$c['amnestyById'].'">'.$this->_escape($c['amnestyByName']).'</a> <small>('.date(self::TIME_YYMMDD_HHMM, $c['amnestyAt']).')</small></p>';							
		}	
		
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($c['comment'])).'</p>';
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
