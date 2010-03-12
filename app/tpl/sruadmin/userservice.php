<?
/**
 * szablon beana usługi
 */
class UFtpl_SruAdmin_UserService
extends UFtpl_Common {	
	public function formToActivate(array $d) {
		$form = UFra::factory('UFlib_Form', 'serviceEdit', $d);
		$url = $this->url(0);
		echo $form->_fieldset();
		echo '<table style="width:95%">';
		echo '<tr><th style="width:30%">Nazwa usługi</th><th style="width:25%">Użytkownik</th><th style="width:30%">String aktywacyjny</th><thstyle="width:15%"></th></tr>';

		foreach ($d as $c) {
			$activateString = $c['userLogin'];
			//jeżeli shell lub poczta to zapodawaj stringi dla Oczątka ;)
			if ($c['servType'] == 1 || $c['servType'] == 2) {
				$activateString = $activateString.','.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).',studs';
			}
			echo '<tr><td>'.$c['servName'].'</td>';
			echo '<td><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a></td>';
			echo '<td style="text-align:center;">'.$activateString.'</td><td>';
			echo $form->_submit('Aktywuj', array('name'=>'serviceEdit[activateFull]['.$c['id'].']'));
			echo '</td></tr>';
		}
		echo '</table>';
		echo $form->_end();
	}
	public function formToDeactivate(array $d) {
		$form = UFra::factory('UFlib_Form', 'serviceEdit', $d);
		$url = $this->url(0);
		echo $form->_fieldset();
		echo '<table width="95%">';
		echo '<tr><th style="width:30%">Nazwa usługi</th><th style="width:25%">Użytkownik</th><th style="width:30%">Login</th><thstyle="width:15%"></th></tr>';

		foreach ($d as $c) {
			echo '<tr><td>'.$c['servName'].'</td>';
			echo '<td><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a></td>';
			echo '<td style="text-align:center;">'.$c['userLogin'].'</td><td>';
			echo $form->_submit('Deaktywuj', array('name'=>'serviceEdit[deactivateFull]['.$c['id'].']'));
			echo '</td></tr>';
		}
		echo '</table>';
		echo $form->_end();
	}
}