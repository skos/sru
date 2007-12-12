<?php
/**
 * szablon beana pokoju
 */
class UFtpl_SruAdmin_Room
extends UFtpl {
	
	public function listRooms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		echo '<ul>';
		
		foreach ($d as $c)
		{
			echo '<li><a href="'.$url.$c['dormitoryAlias'].'/'.$c['alias'].'">'.$c['alias'].'</a></li>';			
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['alias'];
	}
	public function details(array $d, $users) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['alias'].'<br/><small>(liczba użytkowników:'.$d['userCount'].' liczba komputerów:'.$d['computerCount'].')</small></h2>';
		
		if($users)
		{
			echo '<h3>Użytkownicy</h3><ul>';
			
			foreach ($users as $c)
			{
				echo '<li><a href="'.$url.'/users/'.$c['id'].'">'.$c['name'].' '.$c['surname'].'</a></li>';
			}
			echo '</ul>';
		}		
	}		
}
