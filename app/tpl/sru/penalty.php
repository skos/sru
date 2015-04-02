<?
/**
 * szablon beana kary
 */
class UFtpl_Sru_Penalty
extends UFtpl_Common {

	protected $penaltyTypes = array(
		1 => 'Ostrzeżenie',
		2 => 'jeden komputer',
		3 => 'wszystkie komputery',
	);	

	public function listPenalty(array $d, $computers) {
		$url = $this->url(0);

		foreach ($d as $c) {
			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo '<div class="userWarning">';
			} else {
				echo '<div class="userBan">';
			}
			echo '<small>'._("Typ").': ';

			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo _('Ostrzeżenie');
			} else {
				echo '<strong>'._("Kara").'</strong>';
				if(empty($computers[$c['id']]) == false){
					echo '<br />'._("Komputery").': ';
					$url = $this->url(0).'/computers/';
					foreach($computers[$c['id']] as $computer){
						echo '<b><a href="' . $url . $computer['id'] . '">' . $computer['name'] . '</a></b>&nbsp;&nbsp;';
					}
				}
			}

			echo '<br/>'._("Ważna do").': '.date(self::TIME_YYMMDD_HHMM, $c['endAt']);
			echo '<br/>'._("Powód").': '.nl2br($this->_escape($c['reason']));
			echo '</small></div>';
			echo '<br/>';
		}
	}

	public function listAllPenalty(array $d, $computers) {
		$url = $this->url(0);
		$form = UFra::factory('UFlib_Form');
		echo $form->_start();
		echo $form->_fieldset(_("Archiwum kar i ostrzeżeń"));
		
		foreach ($d as $c) {
			if ($c['endAt'] > time()) {	
				if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
					echo '<div class="userWarning">';
				} else {
					echo '<div class="userBan">';
				}
			} else {
				echo '<div class="userWithoutBan">';
			}
			echo '<small>'._("Typ").': ';


			if (UFbean_SruAdmin_Penalty::TYPE_WARNING == $c['typeId']) {
				echo '<span class="userData">'._("Ostrzeżenie").'</span>';
			} else {
				echo '<span class="userData">'._("Kara").'</span>';
				if(empty($computers[$c['id']]) == false){
					echo '<br />'._("Komputery").': ';
					$url = $this->url(0).'/computers/';
					foreach($computers[$c['id']] as $computer){
						echo '<b><a href="' . $url . $computer['id'] . '">' . $computer['name'] . '</a></b>&nbsp;&nbsp;';
					}
				}
			}
			echo '<br/>'._("Ważna do").': <span class="userData">'.date(self::TIME_YYMMDD, $c['endAt']).'</span>';
			echo '<br/>'._("Powód").': '.nl2br($this->_escape($c['reason']));
			echo '</small></div>';
			echo '<hr/>';
		}
		echo $form->_end();
		echo $form->_end(true);
	}	
}
