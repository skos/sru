<?
/**
 * szablon beana akademika
 */
class UFtpl_Sru_Dormitory
extends UFtpl {
	
	public function listDorms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		echo '<ul>';
		
		foreach ($d as $c)
		{
			echo '<li><a href="'.$url.$c['alias'].'">'.$c['name'].'</a></li>';			
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['name'];
	}
	public function details(array $d) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['name'].'<br/><small>(liczba użytkowników:'.$d['userCount'].' liczba komputerów:'.$d['computerCount'].')</small></h2>';
						
	}		
}
