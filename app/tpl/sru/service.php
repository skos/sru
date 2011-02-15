<?
/**
 * szablon beana usługi
 */
class UFtpl_Sru_Service
extends UFtpl_Common {	
	public function formEdit(array $d, $userServices, $user, $adminEdit = false) {
		if (!$user->servicesAvailable) {
			if ($adminEdit) {
				echo $this->ERR('Zablokowane');
			} else {
				echo $this->ERR('Możliwość edycji usług została zablokowana dla Twojego konta. Zwróć się do swojego administratora lokalnego w celu wyjaśnienia.');
			}
		} else if ($user->banned && !$adminEdit) {
			echo $this->ERR('Z powodu aktywnej kary nie jest możliwa edycja usług na Twoim koncie.');
		}
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
			if (($toActivate === true || $toActivate === false) && $user->servicesAvailable && (!$user->banned || $adminEdit)) echo '<a href="#" onclick="return changeConfirmationVisibility('.$c['id'].');">Zmień stan</a>';
			echo '</td></tr>';
			if ($user->servicesAvailable && (!$user->banned || $adminEdit)) {
				echo '<tr><td colspan="3">';
				echo '<p class="services" id="serviceMore'.$c['id'].'" style="display: none; text-align: center;">';
				if ($toActivate) {
					echo 'Klikając poniższy guzik zgłaszasz chęć posiadania usługi:<br/><b>'.$c['name'].'</b>.<br/>Usługa zostanie aktywowana po zatwierdzeniu przez administratora, potwierdzenie otrzymasz na adres e-mail podany w SRU.<br/><br/>';
					echo $form->_submit('Aktywuj usługę: '.$c['name'], array('name'=>'serviceEdit[activate]['.$c['id'].']'));
				}
				elseif ($toActivate === false) {
					echo 'Klikając poniższy guzik zgłaszasz chęć usunięcia usługi: <br/><b>'.$c['name'].'</b>.<br/> Usługa zostanie dezaktywowana po zatwierdzeniu przez administratora, potwierdzenie otrzymasz na adres e-mail podany w SRU.<br/><br/>';
					echo $form->_submit('Dezaktywuj usługę: '.$c['name'], array('name'=>'serviceEdit[deactivate]['.$servId.']'));
				}
				echo '<br/><br/></p></td></tr>';
			}
		}
		echo '</table>';
		echo $form->_end();
?><script type="text/javascript">
function changeConfirmationVisibility(id) {
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