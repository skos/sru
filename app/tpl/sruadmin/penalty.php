<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Penalty
extends UFtpl_Common {
		
	protected $errors = array(
		'reason' => 'Podaj powód',
		'typeId' => 'Wybierz typ kary',
		'startAt' => 'Podaj od kiedy ma obowiązywać',
		'endAt' => 'Podaj do kiedy ma obowiązywać',
		'endAt/noSense' => 'Koniec powinien być po początku',
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
					<td>'.nl2br($this->_escape($c['reason'])).'</td>' //@todo: ograniczyc do ilus znakow
					.'<td>'.date(self::TIME_YYMMDD, $c['startAt']).'</td>
					<td>'.date(self::TIME_YYMMDD, $c['endAt']).'</td>
					<td><a href="'.$url.'/penalties/'.$c['id'].'">edytuj</a></td></tr>'; 		
		}
		echo '</tbody></table>';		
		
	}
	public function formAdd(array $d, $computers ) {

		$form = UFra::factory('UFlib_Form', 'penaltyAdd', $d, $this->errors);
		
/*		echo $form->reasonId('Powód', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFconf_Sru::$reasons),
		));		*/
		
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(UFconf_Sru::$penaltyTypes),
		));	
		

		if($computers) {
			echo '<div id="computersCheckBoxes">'; //@todo: fajnie gdyby ten div pojawial sie tylko jak sie karze kompy
			$tmp = array();
			foreach ($computers as $c) {
				$tmp[$c['id']] = $c['host'];
			}
			echo $form->computerId('Komputery', array( //@todo: jak zrobic multiple selecta? a moze lepiej jakos na check boxach? a moze nigdy nie ma potrzeby karac paru kompow na raz?
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));		
	
			echo '</div>'; }
		echo $form->reason('Powód(dla użytkownika)',  array('type'=>$form->TEXTAREA, 'rows'=>3));

		//@todo: core chyba powinien te value obslugiwac, co? ;)
		echo $form->startAt('Od', array('value' => date(self::TIME_YYMMDD, NOW) ));
		echo $form->endAt('Do', array('value' => date(self::TIME_YYMMDD, NOW+60*60*24*7)));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}
	public function details(array $c) {//@todo: do czau modyfikacji dodac godziny
		$url = $this->url(0);
		echo '<h2>Kara</h2>';
		
		if($c['userId'])
		{
			echo '<p><em>Ukarany:</em> <a href="'.$url.'/users/'.$c['userId'].'">'.$c['userName'].' '.$c['userSurname'].' ('.$c['userLogin'].')</a></p>';
		}
		echo '<p><em>Przez:</em> <a href="'.$url.'/admins/'.$c['adminId'].'">'.$c['adminName'].'</a><small> ('.date(self::TIME_YYMMDD, $c['createdAt']) .')</small></p>';
		
		if($c['modifiedBy'])
		{
			echo '<p><em>Ostatnio modyfikowana przez:</em> <a href="'.$url.'/admins/'.$c['modifiedBy'].'">'.$c['modifyAdminName']. '</a> <small>('.date(self::TIME_YYMMDD, $c['modifiedAt']).')</small></p>';							
		}
		
		echo '<p><em>Powód:</em></p><p class="comment">'.nl2br($this->_escape($c['reason'])).'</p>';
		echo '<p><em>Typ:</em> '.UFconf_Sru::$penaltyTypes[$c['typeId']].'</p>';
		//@todo: lista ukaranych kompow
		
		echo '<p><em>Czas trwania:</em> od <strong>'.date(self::TIME_YYMMDD, $c['startAt']).'</strong> do <strong>'.date(self::TIME_YYMMDD, $c['endAt']).'</strong></p>';							
		
	//	echo '<p><em>Możliwość anulowania:</em> '.date(self::TIME_YYMMDD, $c['amnestyAfter']).'</p>'; w to sie narazie nie bawimy

		if($c['amnestyBy'])
		{
			echo '<p><em>Amnestia udzielona przez:</em> <a href="'.$url.'/admins/'.$c['amnestyBy'].'">'.$c['amnestyAdminName'].'</a> <small>('.date(self::TIME_YYMMDD, $c['amnestyAt']).')</small></p>';							
		}	
		
		
		echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($c['comment'])).'</p>';
		

	}		
}
