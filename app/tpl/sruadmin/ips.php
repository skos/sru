<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Ips
extends UFtpl_Common {

	protected function writeClass($all, $perLine, $url) {
		$caption = current($all);
		$caption = current($caption);
		$class = substr($caption['ip'], 7, 3);
		$caption = substr($caption['ip'], 0, 10);
		echo '<table><caption id="class'.$class.'">'.$caption.'<a href="'.$this->url().'#class'.$class.'">#</a></caption>';
		foreach ($all as $row) {
			echo '<tr>';
			for ($i=0; $i<$perLine; ++$i) {
				echo '<td>';
				if (isset($row[$i])) {
					$ip =& $row[$i];
					if (isset($ip['computerId'])) {
						if ($ip['computerDormitoryId'] === $ip['dormitoryId']) {
							// ds-y sie zgadzaja
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')">'.substr($ip['ip'], 11).'</a>';
						} elseif (!isset($ip['dormitoryAlias'])) {
							// ip nie ma przypisanego ds-u
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' (bez DS)"><em>'.substr($ip['ip'], 11).'</em></a>';
						} else {
							// ds komputera i ds ip-ka nie sa zgodne
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' ('.$ip['dormitoryAlias'].')"><strong>'.substr($ip['ip'], 11).'</strong></a>';
						}
					} else {
						echo substr($ip['ip'], 11);
					}
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	public function ips(array $d) {		
		$url = $this->url(0).'/computers/';

		$perLine = 16;

		// pobranie pierwszej klasy (moze byc roznie zaindeksowany, dlatego takie obejscie)
		$classOld = current($d);
		$classOld = substr($classOld['ip'], 7, 3);

		$tmp = array();
		foreach ($d as &$ip) {
			$class = substr($ip['ip'], 7, 3);
			if ($class != $classOld) {
				$this->writeClass($tmp, $perLine, $url);
				$tmp = array();
			}
			$pos = (int)substr($ip['ip'], 11);	// ostatni czlon ip
			$tmp[(int)floor($pos/$perLine)][$pos%$perLine] =& $ip;
			$classOld = $class;
		}
		$this->writeClass($tmp, $perLine, $url);
	}		
}
