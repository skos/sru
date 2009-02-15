<?
/**
 * szablon kary
 */
class UFtpl_SruAdmin_PenaltyTemplate
extends UFtpl_Common {

	public function choose(array $d) {
		$url = $this->url();
		foreach ($d as $t) {
			echo '<li><h3><a href="'.$url.'/template:'.$t['id'].'">'.$t['title'].'</a></h3><p>'.$t['description'].' <em>('.$t['duration'].' dni)</em></p></li>';
		}
		echo '<li><h3><a href="'.$this->url().'/template:0">Inne</a></h3><p>Żadne z powyższych</p></li>';
	}

}
