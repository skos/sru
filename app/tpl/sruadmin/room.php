<?php
/**
 * szablon beana pokoju
 */
class UFtpl_SruAdmin_Room
extends UFtpl {
	
	public function listRooms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		$lastFlor = '-';	
		$eo = 0;
		
		foreach ($d as $c)
		{	
			if($lastFlor != $c['alias'][0])
			{
				if($lastFlor != '-')
				{
					echo '</ul>';
					echo '<br style="clear: left;" />';  //chyba sie nie da w inline dac :after :P 
				}				
				$lastFlor = $c['alias'][0];	
				$eo++;
				
				echo '<ul class="rooms" style="text-decoration: none; ">';
			}
			if($eo % 2 )
			{
				echo '<li class="odd" style="float:left; list-style-type:none; width:3em;"><a href="'.$url.$c['dormitoryAlias'].'/'.$c['alias'].'">'.$c['alias'].'</a></li>';			
			}
			else
			{
				echo '<li class="even" style="float:left; background-color: #eeeeee; list-style-type:none; width:3em;"><a href="'.$url.$c['dormitoryAlias'].'/'.$c['alias'].'">'.$c['alias'].'</a></li>';
			}
		}
		echo '</ul>';
		echo '<br style="clear: left;" />';
	}

	public function titleDetails(array $d) {
		echo $d['alias'].' ('.$d['dormitoryAlias'].')';
	}
	public function details(array $d, $users) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['alias'].' ('.$d['dormitoryAlias'].')<br/><small>(liczba użytkowników:'.$d['userCount'].' liczba komputerów:'.$d['computerCount'].')</small></h2>';
			
		if($users)
		{
			echo '<h3>Użytkownicy</h3><ul>';
			
			foreach ($users as $c)
			{
				echo '<li><a href="'.$url.'/users/'.$c['id'].'">'.$c['name'].' '.$c['surname'].'</a></li>';
			}
			echo '</ul>';
		}	
		echo '<h3>Komentarz</h3>';
		
		echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';		
			
		echo '<p class="nav"><a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'/:edit">Edytuj</a></p>';
	}
	public function formEdit(array $d) {
	
		echo '<h3>Edycja</h3>';
		$form = UFra::factory('UFlib_Form', 'roomEdit', $d, array());
		
		$form->_start($this->url());
		
		$form->_fieldset('Komentarz');
		$form->comment('', array('type'=>$form->TEXTAREA, 'rows'=>5));
		$form->_submit('Zapisz');
		$form->_end();
		$form->_end(true);		
	}		
}
