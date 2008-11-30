<?
/**
 * szablon beana akademika
 */
class UFtpl_Sru_Dormitory
extends UFtpl_Common {
	
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
		print_r($d);
		echo '<h2>'.$d['name'].'<br/><small>(liczba użytkowników:'.$d['userCount'].' liczba komputerów:'.$d['computerCount'].')</small></h2>';
		echo '<h3>Zajętość IP: '.round(100*$d['computerCount']/$d['computersMax']).'%</h3><img src="http://chart.apis.google.com/chart?chs=300x150&chd=t:'.$d['computerCount'].'&chds=0,'.$d['computersMax'].'&cht=gom&chco=007700,009900,00bb00,00dd00,00ff00,ff0000" width=300" height=150" alt="Zajętość: '.round(100*$d['computerCount']/$d['computersMax']).'%" />';
	}		
}
