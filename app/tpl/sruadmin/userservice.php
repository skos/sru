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
		echo '<tr><th style="width:5%">Lp.</th><th style="width:40%">Nazwa usługi</th><th style="width:40%">Użytkownik</th><th style="width:15%"></th></tr>';

		$i = 0;
		foreach ($d as $c) {
			$i++;
			echo '<tr><td>'.$i.'.</td>';
			echo '<td>'.$c['servName'].'</td>';
			echo '<td><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a><br/>'.$c['userEmail'].'</td>';
			echo '<td>'.$form->_submit('Aktywuj', array('name'=>'serviceEdit[activateFull]['.$c['id'].']'));
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
		echo '<tr><th style="width:5%">Lp.</th><th style="width:30%">Nazwa usługi</th><th style="width:25%">Użytkownik</th><th style="width:30%">Login</th><thstyle="width:15%"></th></tr>';

		$i = 0;
		foreach ($d as $c) {
			$i++;
			echo '<tr><td>'.$i.'.</td>';
			echo '<td>'.$c['servName'].'</td>';
			echo '<td><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a></td>';
			echo '<td style="text-align:center;">'.$c['userLogin'].'</td><td>';
			echo $form->_submit('Dezaktywuj', array('name'=>'serviceEdit[deactivateFull]['.$c['id'].']'));
			echo '</td></tr>';
		}
		echo '</table>';
		echo $form->_end();
	}
}