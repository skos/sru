<?
/**
 * szablon beana admina
 */
class UFtpl_SruAdmin_Ips
extends UFtpl_Common {

	protected function writeClass($all, $perLine) {
		$url = $this->url(0).'/computers/';
		$urlSw = $this->url(0).'/switches/';
		$caption = current($all);
		$caption = current($caption);
		$caption = explode('.', $caption['ip']);
		$class = $caption[1].$caption[2];
		$caption = $caption[0].'.'.$caption[1].'.'.$caption[2];
		echo '<table><caption id="class'.$class.'">'.$caption.'</caption>';
		foreach ($all as $row) {
			echo '<tr>';
			for ($i=0; $i<$perLine; ++$i) {
				echo '<td>';
				if (isset($row[$i])) {
					$ip =& $row[$i];
					$lastPart = substr($ip['ip'], strrpos($ip['ip'], '.') + 1);
					if (isset($ip['computerId'])) {
						if ($ip['admin']) {
							// admin
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')"><span class="admin">'.$lastPart.'</span></a>';
						} else if ($ip['exAdmin']) {
							// ex-admin
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')"><span class="exadmin">'.$lastPart.'</span></a>';
						} elseif (!isset($ip['dormitoryAlias'])) {
							// ip nie ma przypisanego ds-u
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' (bez DS)"><span class="not_signed">'.$lastPart.'</span></a>';
						} else if ($ip['banned'] && $ip['computerDormitoryId'] !== $ip['dormitoryId']) {
							// kara i ds komputera i ds ip-ka nie sa zgodne
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' ('.$ip['dormitoryAlias'].')"><span class="banned_wrong_dorm">'.$lastPart.'</span></a>';
						} else if ($ip['banned']) {
							// kara
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')"><span class="banned">'.$lastPart.'</span></a>';
						} else if ($ip['computerDormitoryId'] === $ip['dormitoryId']) {
							// ds-y sie zgadzaja
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].')">'.$lastPart.'</a>';
						} else {
							// ds komputera i ds ip-ka nie sa zgodne
							echo '<a href="'.$url.$ip['computerId'].'" title="'.$ip['computerHost'].' ('.$ip['computerDormitoryAlias'].') / '.$ip['ip'].' ('.$ip['dormitoryAlias'].')"><span class="wrong_dorm">'.$lastPart.'</span></a>';
						}
					} else if (isset($ip['switchId'])) {
						if ($ip['inoperational']) {
							echo '<a href="'.$urlSw.$ip['switchSerialNo'].'" title="'.$ip['switchModel'].' ('.$ip['switchDormitoryAlias'].')"><span class="wrong_dorm">'.$lastPart.'</span></a>';
						} else {
							echo '<a href="'.$urlSw.$ip['switchSerialNo'].'" title="'.$ip['switchModel'].' ('.$ip['switchDormitoryAlias'].')">'.$lastPart.'</a>';
						}
					} else {
						echo $lastPart;
					}
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table><br/>';
	}
	
	protected function showLegend() {
		echo '<table><tr><td colspan="3"><span class="normal">OK</span></td></tr><tr><td><span class="admin">admin</span></td><td><span class="exadmin">ex-admin</span></td><td><span class="not_signed">brak przypisania DS</span></td></tr><tr><td><span class="wrong_dorm">brak zgodności DS / uszkodzony</span></td><td><span class="banned">kara</span></td><td><span class="banned_wrong_dorm">kara i brak zgodności DS</span></td></tr></table><br/>';
	}

	public function ips(array $d, $dorm, $vlan) {
		$perLine = 16;

		// pobranie pierwszej klasy (moze byc roznie zaindeksowany, dlatego takie obejscie)
		$classOld = current($d);
		$classOld = explode('.', $classOld['ip']);
		$classOld = $classOld[1].$classOld[2];

		$this->showLegend();
		
		$vlans = array();
		$vlanNames = array();
		foreach ($d as &$ip) {
			if (!array_key_exists($ip['vlan'], $vlans)) {
				$vlans[$ip['vlan']] = 'VLAN '.$ip['vlan'];
				$vlanNames[$ip['vlan']] = array();
				$vlanNames[$ip['vlan']][0] = $ip['vlanName'];
				$vlanNames[$ip['vlan']][1] = $ip['vlanDesc'];
				$vlanNames[$ip['vlan']][2] = $ip['vlanDomainSuffix'];
			}
		}
		if (!isset($dorm) && !isset($vlan)) {
			$vlans[0] = 'Brak VLANu';
			$vlanNames[0][0] = 'Żaden VLAN nie został przydzielony';
			$vlanNames[0][1] = '';
			$vlanNames[0][2] = '';
		}
		if (isset($dorm)) {
			$vlans[9999] = 'Błędny przydział IP';
			$vlanNames[9999][0] = 'Przydzielony IP z błędnej puli';
			$vlanNames[9999][1] = '';
			$vlanNames[9999][2] = '';
		}
		
		echo '<div id="tabs"><ul>';
		foreach ($vlans as $vlanId => $vlanNo) {
			$vlanDesc = $vlanNames[$vlanId][0];
			$vlanName = $vlanDesc;
			if (!is_null($vlanNames[$vlanId][1]) && $vlanNames[$vlanId][1] != '') {
				$vlanDesc = $vlanDesc.': '.$vlanNames[$vlanId][1];
			}
			if (!is_null($vlanNames[$vlanId][2]) && $vlanNames[$vlanId][2] != '') {
				$vlanDesc = $vlanDesc.', domena: '.$vlanNames[$vlanId][2];
			}
			echo '<li><a href="#vlan'.$vlanId.'" title="'.$vlanDesc.'">'.$vlanNo.(($vlanId == 0) ? '' : ': '.$vlanName).'</a></li>';
		}
		echo '</ul>';

		if (!isset($dorm)) {
			$this->writeAll($d, $dorm, $perLine, $classOld);
		} else {
			$this->writeAll($d, $dorm, $perLine, $classOld, true);
			$this->writeAll($d, $dorm, $perLine, $classOld, false);
		}
		
		echo '</div>';
		echo '
<script>
$(function() {
$( "#tabs" ).tabs();
});
</script>';	
	}

	private function writeAll($d, $dorm, $perLine, $classOld, $validDorm = null) {
		$tmp = array();
		$curVlan = -1;
		foreach ($d as &$ip) {
			$ipEx = explode('.', $ip['ip']);
			$class = $ipEx[1].$ipEx[2];
			$vlan = $ip['vlan'];
			if ($validDorm === false) {
				$vlan = 9999;
			}
			if ($vlan != $curVlan) {
				if (!empty($tmp)) {
					$dormId = current($tmp);
					$dormId = current($dormId);
					$dormId = $dormId['dormitoryId'];
					if (!isset($validDorm) || ($validDorm === true && $dormId === $dorm->id) || ($validDorm === false && $dormId !== $dorm->id)) {
						$this->writeClass($tmp, $perLine, $dorm);
					}
				}
				if ($curVlan != -1) {
					echo '</div>';
				}
				$curVlan = $vlan;
				echo '<div id="vlan'.$curVlan.'">';
				$tmp = array();
			} else if ($class != $classOld) {
				$dormId = current($tmp);
				$dormId = current($dormId);
				$dormId = $dormId['dormitoryId'];
				if (!isset($validDorm) || ($validDorm === true && $dormId === $dorm->id) || ($validDorm === false && $dormId !== $dorm->id)) {
					$this->writeClass($tmp, $perLine, $dorm);
				}
				$tmp = array();
			}
			$pos = (int)$ipEx[3];	// ostatni czlon ip
			$tmp[(int)floor($pos/$perLine)][$pos%$perLine] =& $ip;
			$classOld = $class;
		}
		$dormId = current($tmp);
		$dormId = current($dormId);
		$vlan = $dormId['vlan'];
		if ($validDorm === false) {
			$vlan = 9999;
		}
		if ($vlan != $curVlan) {
			if ($curVlan != 0) {
				echo '</div>';
			}
			$curVlan = $vlan;
			echo '<div id="vlan'.$curVlan.'">';
		}
		$dormId = $dormId['dormitoryId'];
		if (!isset($validDorm) || ($validDorm === true && $dormId === $dorm->id) || ($validDorm === false && $dormId !== $dorm->id)) {
			$this->writeClass($tmp, $perLine, $dorm);
		}
		echo '</div>';
	}
}
