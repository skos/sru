<?
/**
 * szablon beana usługi
 */
class UFtpl_Sru_Service
extends UFtpl_Common {	
	public function formEdit(array $d, $userServices) {
		$form = UFra::factory('UFlib_Form', 'serviceEdit', $d);
		echo $form->_fieldset();
		echo '<table width="95%">';
		echo '<tr><th>Nazwa usługi</th><th>Stan usługi</th><th></th></tr>';

		foreach ($d as $c) {
			$active = 'BRAK USŁUGI';
			$toActivate = true;
			if ($userServices != null) {
				foreach ($userServices as $s) {
					if ($s['servType'] == $c['id']) {
						if ($s['state'] === true) {
							$active = 'AKTYWNA';
							$toActivate = false;
						}
						else if ($s['state'] === false) {
							$active = 'OCZEKUJE NA AKTYWACJĘ';
							$toActivate = null;
						}
						else {
							$active = 'OCZEKUJE NA DEZAKTYWACJĘ';
							$toActivate = null;
						}
						
						$servId = $s['id'];
					}
				}
			}
			echo '<tr><td>';
			echo $c['name'];

			echo '</td><td>'.$active.'</td><td>';
			if ($toActivate) echo $form->_submit('Aktywuj', array('name'=>'serviceEdit[activate]['.$c['id'].']'));
			elseif ($toActivate === false) echo $form->_submit('Deaktywuj', array('name'=>'serviceEdit[deactivate]['.$servId.']'));
			echo '</td></tr>';
		}
		echo '</table>';
		echo $form->_end();
	}
}