<?
/**
 * szablon beana kary komputera
 */
class UFtpl_SruAdmin_ComputerBan
//extends UFtpl_Common {
extends UFtpl_SruAdmin_Penalty {

	public function computerList(array $d) {
		$url = $this->url(0).'/computers/';
		$tmp = array();
		foreach ($d as $c) {
			$tmp[] = '<a href="'.$url.$c['computerId'].'">'.$c['computerHost'].'</a>';
		}
		echo '<p><em>Komputery:</em> ';
		echo implode(', ', $tmp);
		echo '</p>';
	}
	
	public function listComputerPenalty(array $d) {
		$this->listUserPenalty($d);
	}
}
