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
						if ($ip['admin']) {
							// admin
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')"><span class="admin">'.substr($ip['ip'], 11).'</span></a>';
						} elseif (!isset($ip['dormitoryAlias'])) {
							// ip nie ma przypisanego ds-u
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' (bez DS)"><span class="not_signed">'.substr($ip['ip'], 11).'</span></a>';
						} else if ($ip['banned'] && $ip['computerDormitoryId'] !== $ip['dormitoryId']) {
							// kara i ds komputera i ds ip-ka nie sa zgodne
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' ('.$ip['dormitoryAlias'].')"><span class="banned_wrong_dorm">'.substr($ip['ip'], 11).'</span></a>';
						} else if ($ip['banned']) {
							// kara
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')"><span class="banned">'.substr($ip['ip'], 11).'</span></a>';
						} else if ($ip['computerDormitoryId'] === $ip['dormitoryId']) {
							// ds-y sie zgadzaja
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')">'.substr($ip['ip'], 11).'</a>';
						} else {
							// ds komputera i ds ip-ka nie sa zgodne
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' ('.$ip['dormitoryAlias'].')"><span class="wrong_dorm">'.substr($ip['ip'], 11).'</span></a>';
						}
					} else {
						echo substr($ip['ip'], 11);
					}
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table><br/>';
	}
	
	protected function showLegend() {
		echo '<table><tr><td><span class="normal">OK</span></td><td><span class="admin">admin</span></td><td><span class="not_signed">brak przypisania DS</span></td></tr><tr><td><span class="wrong_dorm">brak zgodności DS</span></td><td><span class="banned">kara</span></td><td><span class="banned_wrong_dorm">kara i brak zgodności DS</span></td></tr></table><br/>';
	}

	public function ips(array $d, $dorm) {
		$url = $this->url(0).'/computers/';

		$perLine = 16;

		// pobranie pierwszej klasy (moze byc roznie zaindeksowany, dlatego takie obejscie)
		$classOld = current($d);
		$classOld = substr($classOld['ip'], 7, 3);

		$this->showLegend();

		if (!isset($dorm)) {
			$this->writeAll($d, $dorm, $perLine, $classOld, $url);
		} else {
			$this->writeAll($d, $dorm, $perLine, $classOld, $url, true);
			echo '<hr/><br/>';
			$this->writeAll($d, $dorm, $perLine, $classOld, $url, false);
		}
	}

	private function writeAll($d, $dorm, $perLine, $classOld, $url, $validDorm = null) {
		$tmp = array();
		foreach ($d as &$ip) {
			$class = substr($ip['ip'], 7, 3);
			if ($class != $classOld) {
				$dormId = current($tmp);
				$dormId = current($dormId);
				$dormId = $dormId['dormitoryId'];
				if (!isset($validDorm) || ($validDorm === true && $dormId === $dorm->id) || ($validDorm === false && $dormId !== $dorm->id)) {
					$this->writeClass($tmp, $perLine, $url, $dorm);
				}
				$tmp = array();
			}
			$pos = (int)substr($ip['ip'], 11);	// ostatni czlon ip
			$tmp[(int)floor($pos/$perLine)][$pos%$perLine] =& $ip;
			$classOld = $class;
		}
		$dormId = current($tmp);
		$dormId = current($dormId);
		$dormId = $dormId['dormitoryId'];
		if (!isset($validDorm) || ($validDorm === true && $dormId === $dorm->id) || ($validDorm === false && $dormId !== $dorm->id)) {
			$this->writeClass($tmp, $perLine, $url, $dorm);
		}
	}
}
