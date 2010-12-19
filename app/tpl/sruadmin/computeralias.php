<?
class UFtpl_SruAdmin_ComputerAlias
extends UFtpl_Common {
	public function listAliases(array $d) {
		$url = $this->url(0).'/computers/';
		foreach ($d as $c) {
			echo '<li'.($c['parentBanned'] ? ' class="ban"' : '').'><a href="'.$url.$c['computerId'].'">'.$c['host'].' ('.($c['isCname'] ? 'CNAME' : 'A').') -> '.$c['parent'].(strlen($c['parentComment']) ? ' <img src="'.UFURL_BASE.'/i/gwiazdka.png" alt="" title="'.$c['parentComment'].'" />':'').'</a></li>';
		}
	}
}