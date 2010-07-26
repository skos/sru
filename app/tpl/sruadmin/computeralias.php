<?
class UFtpl_SruAdmin_ComputerAlias
extends UFtpl_Common {
	public function listAliases(array $d) {
		$url = $this->url(0).'/computers/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['parentId'].'">'.$c['host'].' ('.($c['isCname'] ? 'CNAME' : 'A').') -> '.$c['parent'].'</a></li>';
		}
	}
}