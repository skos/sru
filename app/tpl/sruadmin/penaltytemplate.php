<?
/**
 * szablon kary
 */
class UFtpl_SruAdmin_PenaltyTemplate
extends UFtpl_Common {

	public function choose(array $d) {
		$url = $this->url();
		$lastTemplate = '-';
		foreach ($d as $t) {
			if ($lastTemplate != '-' && substr($t['title'], 0, 3) != substr($lastTemplate, 0, 3)) {
				echo '<li><hr/></li>';
			}
			echo '<li><h3><a href="'.$url.'/template:'.$t['id'].'">'.$t['title'].'</a></h3><p>'.$t['description'].' <em>('.$t['duration'].' dni / '.$t['amnesty'].' dni)</em></p></li>';
			$lastTemplate = $t['title'];
		}
		echo '<li><hr/></li>';
		echo '<li><h3><a href="'.$this->url().'/template:0">Inne</a></h3><p>Żadne z powyższych</p></li>';
	}

}
