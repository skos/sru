<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Penalty
extends UFtpl {
		
	protected $errors = array(
		'reason' => 'Wybierz powód',
		'typeId' => 'Wybierz typ kary',
		'startTime' => 'Podaj od kiedy ma obowiązywać',
		'endTime' => 'Podaj do kiedy ma obowiązywać',
		'endTime/noSense' => 'Koniec powinien być po początku',
	);	

	public function listPenalty(array $d) {
		$url = $this->url(0);

		echo '<table>
				<thead><tr>
					<th scope="col">kto</th>
					<th scope="col">komu</th>
					<th scope="col">za co</th>
					<th scope="col">od kiedy</th>
					<th scope="col">do kiedy</th>
					<th scope="col"></th>
				</tr></thead>
				
				<tbody>';
		foreach ($d as $c)
		{	
			echo '<tr>			
					<td><a href="'.$url.'/admins/'.$c['adminId'].'">'.$c['adminName'].'</a></td>
					<td><a href="'.$url.'/users/'.$c['userId'].'">'.$c['userName'].' '.$c['userSurname'].'</a></td>
					<td>'.UFconf_Sru::$reasons[$c['reasonId']].'</td>
					<td>'.date(self::TIME_YYMMDD, $c['startTime']).'</td>
					<td>'.date(self::TIME_YYMMDD, $c['endTime']).'</td>
					<td><a href="'.$url.'/penalties/'.$c['id'].'">edytuj</a></td></tr>';			
		}
		echo '</tbody></table>';		
		
	}
	public function formAdd(array $d, $computers ) {

		$form = UFra::factory('UFlib_Form', 'penaltyAdd', $d, $this->errors);
		
		$form->reasonId('Powód', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFconf_Sru::$reasons),
		));		
		
		$form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFconf_Sru::$types),
		));	
		

		if($computers) {
			echo '<div id="computersCheckBoxes">'; //@todo: fajnie gdyby ten div pojawial sie tylko jak sie karze kompy
			$tmp = array();
			foreach ($computers as $c) {
				$tmp[$c['id']] = $c['host'];
			}
			$form->computerId('Komputery', array( //@todo: jak zrobic multiple selecta? a moze lepiej jakos na check boxach? a moze nigdy nie ma potrzeby karac paru kompow na raz?
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));		
	
			echo '</div>'; }
	
		 //@todo: core chyba powinien te value obslugiwac, co? ;)
		$form->startTime('Od', array('value' => date(self::TIME_YYMMDD, NOW) ));
		$form->endTime('Do', array('value' => date(self::TIME_YYMMDD, NOW+60*60*24*7)));
		$form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}	
}
