<?
/**
 * szablon beana usługi
 */
class UFtpl_Sru_Service
extends UFtpl_Common {	
	public function formEdit(array $d, $userServices) {
		$form = UFra::factory('UFlib_Form', 'serviceEdit', $d);
		echo $form->_fieldset();
		echo '<table width="100%">';
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
			if ($toActivate === true || $toActivate === false) echo '<a href="#" onclick="return changeVisibility('.$c['id'].');">Zmień stan</a>';
			echo '</td></tr>';
			echo '<tr><td colspan="3">';
			echo '<p class="services" id="serviceMore'.$c['id'].'" style="display: none; text-align: center;">';
			if ($toActivate) echo $form->_submit('Aktywuj usługę: '.$c['name'], array('name'=>'serviceEdit[activate]['.$c['id'].']'));
			elseif ($toActivate === false) echo $form->_submit('Deaktywuj usługę: '.$c['name'], array('name'=>'serviceEdit[deactivate]['.$servId.']'));
			echo '<br/><br/></p></td></tr>';
		}
		echo '</table>';
		echo $form->_end();
?><script type="text/javascript">
function changeVisibility(id) {
	var tr = document.getElementById('serviceMore' + id);
	if (tr.style.display == 'none') {
		tr.style.display = 'block';
	} else {
		tr.style.display = 'none';
	}
}
</script><?
	}
}