<?php
/**
 * szablon beana pokoju
 */
class UFtpl_SruAdmin_Room
extends UFtpl_Common {
	
	public function listRooms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		$lastFlor = '-';	
		
		foreach ($d as $c)
		{	
			if($lastFlor != $c['alias'][0])
			{
				if($lastFlor != '-') {
					echo '</ul><ul>';
				} else {
					echo '<ul class="first">';
				}				
				$lastFlor = $c['alias'][0];	
				
			}
			echo '<li><a href="'.$url.$c['dormitoryAlias'].'/'.$c['alias'].'">'.$c['alias'].'</a></li>';
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['alias'].' ('.$d['dormitoryAlias'].')';
	}
	public function details(array $d) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['alias'].' ('.$d['dormitoryAlias'].')<br/><small>(liczba użytkowników: '.$d['userCount'].' &bull; liczba komputerów: '.$d['computerCount'].')</small></h2>';
		if ($d['comment']) {
			echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';		
		}
		echo '<p class="nav"><a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'/:edit">Edytuj</a></p>';
	}

	public function formEdit(array $d) {
	
		$form = UFra::factory('UFlib_Form', 'roomEdit', $d, array());
		
		echo $form->_start($this->url());
		
		echo $form->_fieldset('Komentarz');
		echo $form->comment('', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);		
	}		
}
