<?php
/**
 * szablon portu switcha
 */
class UFtpl_SruAdmin_SwitchPort
extends UFtpl_Common {

	protected $errors = array(
		'locationAlias/noDormitory' => 'Błędny akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje w akademiku przypisanym do switcha',
		'locationAlias/roomAndSwitch' => 'Nie można jednocześnie podać lokalizacji i podłączonego switcha',
		'connectedSwitchId/switchAndAdmin' => 'Switch nie może być podłączony do portu admina',
		'portEnabled/enabledAndPenalty' => 'Nie można ustawić kary dla włączonego portu',
	);

	public function details(array $d, $switch, $alias, $speed, $vlan, $flag, $learnMode, $addrLimit, $alarmState, $loopProtect, $trunk) {
		$url = $this->url(0).'/switches/';
		$conf = UFra::shared('UFconf_Sru');
		$swstatsLink = str_replace($conf->swstatsSwitchRegex, UFtpl_SruAdmin_Switch::displaySwitchName($switch->dormitoryAlias, $switch->hierarchyNo, $switch->lab), $conf->swstatsLinkPort);
		$swstatsLink = str_replace($conf->swstatsPortRegex, $d['ordinalNo'], $swstatsLink);

		echo '<h3>Port '.$d['ordinalNo'].'</h3>';
		echo '<div id="tabs"><ul>';
		echo '<li><a href="#data">Dane</a></li>';
		echo '<li><a href="#security">Bezpieczeństwo</a></li>';
		echo '</ul>';
		echo '<div id="data">';
		if(is_null($d['connectedSwitchId'])) {
			echo '<p><em>Lokalizacja:</em> <a href="'.$this->url(0).'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a></p>';
		} else {
			echo '<p><em>Podłączony switch:</em> <a href="'.$url.$d['connectedSwitchSn'].'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d['connectedSwitchDorm'], $d['connectedSwitchNo'], $d['connectedSwitchLab']).'</a></p>';
		}
		if (!is_null($alias) && $alias != '') {
			echo '<p><em>Alias portu: </em>'.$alias.'</p>';
		}
		if (!is_null($speed)) {
			echo '<p><em>Przepustowość:</em> '.$speed.' Mb/s</p>';
		}
		echo '<p><em>Nietagowany VLAN:</em> '.(is_null($vlan) ? 'brak' : $vlan).'</p>';
		echo '<p><em>Port admina:</em> '.($d['admin'] ? 'tak' : 'nie').'</p>';
		if (!is_null($d['penaltyId'])) {
			echo '<p><em>Przypisana kara: </em><a href="'.$this->url(0).'/penalties/'.$d['penaltyId'].'">'.$d['userName'].' "'.$d['userLogin'].'" '.$d['userSurname'].': '.$d['templateTitle'].' ('.$d['penaltyId'].')</a></p>';
		}
		echo '<p><em>Komentarz:</em> '.$d['comment'].'</p>';
		echo '</div><div id="security">';
		if ($trunk == UFlib_Snmp_Hp::DISABLED) { // jeśli nie jest trunkiem
			echo '<p><em>Tryb nauki:</em> '.(is_null($learnMode) ? 'brak' : UFlib_Snmp_Hp::$learnModes[$learnMode]).' <a href="http://tinyurl.com/bprhaqf">'.UFlib_Helper::displayHint('Learning mode</br>Kliknij po więcej informacji (zewnętrzna strona)', false).'</a></p>';
			echo '<p><em>Limit adresów MAC:</em> '.(is_null($addrLimit) ? 'brak' : $addrLimit).'</p>';
			echo '<p><em>Akcja:</em> '.(is_null($alarmState) ? 'brak' : UFlib_Snmp_Hp::$alarmStates[$alarmState]).' '.UFlib_Helper::displayHint('Akcja po przekroczeniu liczby adresów', false).'</p>';
			echo '<p><em>Flaga wtargnięcia:</em> '.(is_null($flag) ? 'brak' : ($flag == UFlib_Snmp_Hp::UP ? 'podniesiona' : 'opuszczona')).' '.UFlib_Helper::displayHint('Intrusion flag', false).'</p>';
			echo '<p><em>Zabezpieczenie przet pętlą:</em> '.(is_null($loopProtect) ? 'brak' : ($loopProtect == UFlib_Snmp_Hp::ENABLED ? 'aktywne' : 'nieaktywne')).' '.UFlib_Helper::displayHint('Loop protect', false).'</p>';
		} else {
			echo '<p><img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" /> Dane dot. bezpieczeństwa nie są dostępne dla trunków.</p>';
		}
		echo '</div></div>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> &bull; 
			 <a href="'.$url.'">Pokaż wszystkie</a> &bull; 
			<a href="'.$url.$switch->serialNo.'/port/'.$d['ordinalNo'].'/macs">Pokaż adresy MAC</a> &bull; 
			<a href="'.$url.$switch->serialNo.'/port/'.$d['ordinalNo'].'/:edit">Edytuj port</a> &bull;
			<a href="'.$swstatsLink.'">SWStats</a></p>';
		echo '
<script>
$(function() {
$( "#tabs" ).tabs();
});
</script>';	
	}

	public function portMacs(array $d, $switch, $macs) {
		$url = $this->url(0).'/switches/';

		echo '<h3>Adresy MAC na porcie '.$d['ordinalNo'].'</h3>';
		echo '<p><em>Adresy MAC na porcie:</em><br/>';
		if (!is_null($macs)) {
			foreach ($macs as $mac) {
				echo '<a href="'.$this->url(0).'/computers/search/mac:'.$mac.'">'.$mac.'</a><br/>';
			}
		}
		echo '</p>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> &bull; <a href="'.$url.'">Pokaż wszystkie</a> &bull; ';
		echo '<a href="'.$url.$switch->serialNo.'/port/'.$d['ordinalNo'].'/:edit">Edytuj port</a></p>';
	}

	public function formEditOne(array $d, $switch, $enabledSwitches, $status, $penalties) {
		$post = $this->_srv->get('req')->post;
		
		try {
			$portEnabled = $post->switchPortEdit['portEnabled'];

		} catch (UFex_Core_DataNotFound  $e) {
			$portEnabled = ($status == UFlib_Snmp_Hp::DISABLED ? 0 : 1);
		}

		$form = UFra::factory('UFlib_Form', 'switchPortEdit', $d, $this->errors);
		echo $form->_fieldset();
		$url = $this->url(0).'/switches/';

		if ($this->_srv->get('msg')->get('switchPortEdit/errors/switch/writingError')) {
			echo $this->ERR('Nie udało się zapisać danych na switcha');
		}

		$tmp = array();
		foreach ($enabledSwitches as $sw) {
			if ($sw['id'] == $switch->id) continue;
			$tmp[$sw['id']] = UFtpl_SruAdmin_Switch::displaySwitchName($sw['dormitoryAlias'], $sw['hierarchyNo'], $sw['lab']);
		}

		echo '<h3>Edycja portu '.$d['ordinalNo'].'</h3>';
		echo $form->locationAlias('Przypisany pokój');
		echo $form->connectedSwitchId('Podłączony switch', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		if (!is_null($penalties)) {
			$tmp = array();
			foreach ($penalties as $penalty) {
				$tmp[$penalty['id']] = $penalty['userName'].' "'.$penalty['userLogin'].'" '.$penalty['userSurname'].': '.$penalty['templateTitle'].' ('.$penalty['id'].')';
			}
			echo $form->penaltyId('Kara', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp, '', ''),
			));
		} else if (!is_null($d['penaltyId'])) {
			$tmp = array();
			$tmp[$d['penaltyId']] = $d['userName'].' "'.$d['userLogin'].'" '.$d['userSurname'].': '.$d['templateTitle'].' ('.$d['penaltyId'].')';
			echo $form->penaltyId('Kara', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp, '', ''),
			));
		}
		if (!is_null($status)) {
			echo $form->portStatus('', array('type'=>$form->HIDDEN, 'value'=>($status == UFlib_Snmp_Hp::DISABLED ? 0 : 1)));
			echo $form->portEnabled('Port włączony', array('type'=>$form->CHECKBOX, 'value'=>$portEnabled));
		}
		echo $form->admin('Port admina', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz');
		if (!is_null($status)) {
			echo $form->copyToSwitch('Zapisz opis na switcha '.UFlib_Helper::displayHint("Zapis spowoduje nadpisanie aliasu portu na switchu. Będzie to nr pokoju, podłaczony switch (o ile podano) i komentarz."), array('type'=>$form->CHECKBOX, 'value'=>true));
		}
	}

	private function showLegend() {
		echo '<div class="legend"><table class="switchports"><tr><td class="dis">Wyłączony</td><td class="down">Nieaktywny</td><td class="up">Aktywny</td><td class="unknown">Status nieznany</td></tr></table><br/></div>';
	}

	public function listPorts(array $d, $switch, $portStatuses, $trunks, $flags, $port = null) {
		$url = $this->url(0).'/switches/';
		$hpLib = UFra::shared('UFlib_Snmp_Hp');
		if (in_array($switch->modelNo, $hpLib->biggerTrunkNumbers)) {
			$biggerTrunkNumbers = true;
		} else {
			$biggerTrunkNumbers = false;
		}
		$selectedPort = 0;
		if ($port != null) {
			$selectedPort = $port->ordinalNo;
		}
		echo '<div class="switchports">';
		echo '<h3>Lista portów</h3>';
		$this->showLegend();
		echo '<table id="switchPortsT">';
		for ($i = 0; $i < count($d); $i++) {
			if ($i % 8 == 0) {
				echo '<tr>';
			}
			echo '<td id="'.$d[$i]['ordinalNo'].'" title="'.$this->_escape($d[$i]['comment']).'" class="';
			if ($portStatuses == null || !isset($portStatuses[$i])) {
				echo "unknown";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DISABLED) {
				echo "dis";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DOWN) {
				echo "down";
			} else {
				echo "up";
			}
			if ($i == $selectedPort - 1) {
				echo ' selectedPort">';
			} else {
				echo '">';
			}
			echo '<a href="'.$url.$switch->serialNo.'/port/'.$d[$i]['ordinalNo'].'">';
			echo $d[$i]['admin'] ?'<strong>' : '';
			echo $d[$i]['ordinalNo'];
			if ($trunks[$i] != 0) {
				if ($biggerTrunkNumbers) {
					$trunks[$i]--;
				}
				echo ' <small>(Trk'.$trunks[$i].')</small>';
			}
			echo $d[$i]['admin'] ?'</strong>' : '';
			echo '</a>';
			echo (isset($flags[$i]) && $flags[$i] == UFlib_Snmp_Hp::UP) ? ' <img src="'.UFURL_BASE.'/i/img/flaga.png" alt="" title="Podniesiona flaga wtargnięcia (intrusion flag)" />' : '';
			echo ($d[$i]['penaltyId'] == '') ? '' : ' <a href="'.$this->url(0).'/penalties/'.$d[$i]['penaltyId'].'"><img src="'.UFURL_BASE.'/i/img/czaszka.png" alt="" title="'.$d[$i]['userName'].' &quot;'.$d[$i]['userLogin'].'&quot; '.$d[$i]['userSurname'].': '.$d[$i]['templateTitle'].' ('.$d[$i]['penaltyId'].')" /></a>';
			echo ($d[$i]['comment'] == '') ? '' : ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d[$i]['comment'].'" />';
			echo '<br/><small>(';
			echo is_null($d[$i]['connectedSwitchId']) ? ('<a href="'.$this->url(0).'/dormitories/'.$d[$i]['dormitoryAlias'].'/'.$d[$i]['locationAlias'].'">'.
				$d[$i]['locationAlias'].'</a>') : ('<a href="'.$url.$d[$i]['connectedSwitchSn'].'">'.UFtpl_SruAdmin_Switch::displaySwitchName($d[$i]['connectedSwitchDorm'], $d[$i]['connectedSwitchNo'], $d[$i]['connectedSwitchLab']).'</a>');
			echo ')</small>';
			echo '</td>';
			if (($i + 1) % 8 == 0) {
				echo '</tr>';
			}
		}
		if (count($d) % 8 != 0) {
			while ($i % 8 != 0) {
				echo '<td class="contexMenuDisabled"></td>';
				$i++;
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$switch->dormitoryAlias.'">Wróć do listy</a> &bull; 
			<a href="'.$url.'">Pokaż wszystkie</a> &bull; 
			<a href="'.$url.$switch->serialNo.'/:portsedit">Edytuj porty</a>
			</p>
			</div>';
		
		UFlib_Script::displaySwitchPortMenu(array('' => $url.$switch->serialNo));
	}

	public function listRoomPorts(array $d, $room, $portStatuses, $portFlags) {
		$url = $this->url(0).'/switches/';
		$i = 0;
		$j = 0;
		$switch = 0;
		$switches = array();

		echo '<div class="switchports">';
		foreach ($d as $port) {
			if ($switch != $port['switchId']) {
				if ($switch != 0) {
					if (($j + 1) % 8 != 0) {
						while (($j + 1) % 8 != 0) {
							echo '<td></td>';
							$j++;
						}
						echo '</tr>';
					}
					echo '</table>';
				}
				echo '<h4>Switch <a href="'.$url.$port['switchSn'].'">'.UFtpl_SruAdmin_Switch::displaySwitchName($port['dormitoryAlias'], $port['switchNo'], $port['switchLab']).'</a></h4>';
				$switch = $port['switchId'];
				echo '<table id="switchPortsT'.$port['switchId'].'">';
				$j = 0;
				$switches[$port['switchId']] = $url.$port['switchSn'];
			}
			if ($j % 8 == 0) {
				echo '<tr>';
			}
			echo '<td id="'.$port['ordinalNo'].'" class="';
			if ($portStatuses == null || !isset($portStatuses[$i])) {
				echo "unknown";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DISABLED) {
				echo "dis";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DOWN) {
				echo "down";
			} else {
				echo "up";
			}
			echo '">';
			echo '<a href="'.$url.$port['switchSn'].'/port/'.$port['ordinalNo'].'">';
			echo $port['admin'] ?'<strong>' : '';
			echo $port['ordinalNo'];
			echo $port['admin'] ?'</strong>' : '';
			echo '</a>';
			echo (isset($portFlags[$i]) && $portFlags[$i] == UFlib_Snmp_Hp::UP) ? ' <img src="'.UFURL_BASE.'/i/img/flaga.png" alt="" title="Podniesiona flaga wtargnięcia (intrusion flag)" />' : '';
			echo ($d[$i]['penaltyId'] == '') ? '' : ' <a href="'.$this->url(0).'/penalties/'.$d[$i]['penaltyId'].'"><img src="'.UFURL_BASE.'/i/img/czaszka.png" alt="" title="'.$d[$i]['userName'].' &quot;'.$d[$i]['userLogin'].'&quot; '.$d[$i]['userSurname'].': '.$d[$i]['templateTitle'].' ('.$d[$i]['penaltyId'].')" /></a>';
			echo ($port['comment'] == '') ? '' : ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$port['comment'].'" />';
			echo '</td>';
			if (($j + 1) % 8 == 0) {
				echo '</tr>';
			}
			$i++;
			$j++;
		}
		if (($j + 1) % 8 != 0) {
			while (($j + 1) % 8 != 0) {
				echo '<td class="contexMenuDisabled"></td>';
				$j++;
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$room->dormitoryAlias.'">Pokaż switche akademika</a> </p>';
		echo '</div>';
		
		UFlib_Script::displaySwitchPortMenu($switches);
	}

	public function formEdit(array $d, $switch, $enabledSwitches, $portAliases) {
		$post = $this->_srv->get('req')->post;
		$url = $this->url(0);

		$form = UFra::factory('UFlib_Form', 'switchPortsEdit', $d, $this->errors);
		$tmp = array();
		foreach ($enabledSwitches as $sw) {
			if ($sw['id'] == $switch->id) continue;
			$tmp[$sw['id']] = $sw['dormitoryAlias'].'-hp'.$sw['hierarchyNo'];
		}

		if ($this->_srv->get('msg')->get('switchPortsEdit/errors/locationAlias')) {
			echo $this->ERR('W formularzu znalazł się błąd uniemożliwiający zapis danych.<br/>Szczegóły błędu znajdują się przy odpowiednim porcie.');
		}
		if ($this->_srv->get('msg')->get('switchPortsEdit/errors/switch/writingError')) {
			echo $this->ERR('Nie udało się zapisać danych na switcha');
		}
		echo $form->_fieldset();
		echo '<div class="switchPortsEdit">';
		if (is_null($portAliases)) {
			echo $this->ERR('Nie jest możliwe podłączenie się do switcha. <a href="'.$url.'/switches/'.$d['switch']->serialNo.'">Powrót</a>');
		} else {
			echo '<br/><strong>Zapisanie danych spowoduje zapisanie danych także na switch.</strong>';
			echo '<table style="margin-left:auto; margin-right:auto;"><tr><td>';
			echo $form->_submit('Zapisz');
			echo '</td><td>';
			echo $form->_submit('Skopiuj aliasy ze switcha', array('name'=>'copyAliasesFromSwitch', 'id'=>'copyAliasesFromSwitch'));
			echo '</td><td><a href="'.$url.'/switches/'.$switch->serialNo.'">Powrót</a></td></tr></table>';
		}
		$copyAliases = (isset($this->_srv->get('msg')->info['copyAliasesFromSwitch']) && !is_null($portAliases));

		$conf = UFra::shared('UFconf_Sru');
		$switchRegex = $conf->switchRegex;
		$roomRegex = $conf->roomRegex;
		$i = 0;
		echo '<table style="width: 100%;">';
		foreach ($d as $c) {
			if ($i % 2 == 0) {
				echo '<tr><td>';
			} else {
				echo '<td>';
			}
			echo '<p><em>Port '.$c['ordinalNo'].':</em>';
			if ($portAliases != null) {
				echo ' '.$portAliases[$c['ordinalNo'] - 1];
			}
			echo '</p>';
			$locationAlias = $c['locationAlias'];
			$connectedSwitchId = $c['connectedSwitchId'];
			$comment = $c['comment'];
			try {
				$copied = false;
				if ($copyAliases && preg_match($roomRegex, $portAliases[$c['ordinalNo'] - 1]) > 0) {
					$locationAlias = $portAliases[$c['ordinalNo'] - 1];
					$copied = true;
				} else if (isset($post->switchPortsEdit[$c['id']]['locationAlias'])) {
					$locationAlias = $post->switchPortsEdit[$c['id']]['locationAlias'];
				}
				if ($copyAliases && preg_match($switchRegex, $portAliases[$c['ordinalNo'] - 1]) > 0) {
					$connectedSwitchId = array_search($portAliases[$c['ordinalNo'] - 1], $tmp);
					$copied = true;
				} else if (isset($post->switchPortsEdit[$c['id']]['connectedSwitchId'])) {
					$connectedSwitchId = $post->switchPortsEdit[$c['id']]['connectedSwitchId'];
				}
				if ($copyAliases && !$copied) {
					if (isset($post->switchPortsEdit[$c['id']]['locationAlias'])) {
						$comment = trim(str_replace($conf->penaltyPrefix, '', str_replace($post->switchPortsEdit[$c['id']]['locationAlias'].':', '', $portAliases[$c['ordinalNo'] - 1])));
					} else {
						$comment = $portAliases[$c['ordinalNo'] - 1];
					}
				} else if (isset($post->switchPortsEdit[$c['id']]['comment'])) {
					$comment = trim(str_replace($conf->penaltyPrefix, '', $post->switchPortsEdit[$c['id']]['comment']));
				}
			} catch (UFex_Core_DataNotFound $e) {
			}
			echo $form->locationAlias('Pokój', array('name'=>'switchPortsEdit['.$c['id'].'][locationAlias]', 'id'=>'switchPortsEdit['.$c['id'].'][locationAlias]', 'value'=>$locationAlias));
			if ($this->_srv->get('msg')->get('switchPortsEdit/errors/locationAlias/noRoom_'.$c['ordinalNo'])) {
				echo '<strong>Pokój nie istnieje w akademiku przypisanym do switcha</strong><br/>';
			}
			if ($this->_srv->get('msg')->get('switchPortsEdit/errors/locationAlias/roomAndSwitch_'.$c['ordinalNo'])) {
				echo '<strong>Nie można jednocześnie podać lokalizacji i podłączonego switcha</strong><br/>';
			}
			echo $form->connectedSwitchId('Switch', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp, '', ''),
				'name'=>'switchPortsEdit['.$c['id'].'][connectedSwitchId]',
				'id'=>'switchPortsEdit['.$c['id'].'][connectedSwitchId]',
				'value'=>$connectedSwitchId
			));
			if ($this->_srv->get('msg')->get('switchPortsEdit/errors/locationAlias/switchAndAdmin_'.$c['ordinalNo'])) {
				echo '<strong>Switch nie może być podłączony do portu admina</strong><br/>';
			}
			echo $form->comment('Komentarz', array('name'=>'switchPortsEdit['.$c['id'].'][comment]', 'id'=>'switchPortsEdit['.$c['id'].'][comment]', 'value'=>$comment));
			echo $form->ordinalNo('', array('type'=>$form->HIDDEN, 'name'=>'switchPortsEdit['.$c['id'].'][ordinalNo]', 'id'=>'switchPortsEdit['.$c['id'].'][ordinalNo]', 'value'=>$c['ordinalNo']));
			if ($i % 2 == 0) {
				echo '</td>';
			} else {
				echo '</td></tr>';
			}
			$i++;
		}
		echo '</table>';
		if (!is_null($portAliases)) {
			echo '<br/><strong>Zapisanie danych spowoduje zapisanie danych także na switch.</strong>';
			echo '<table style="margin-left:auto; margin-right:auto;"><tr><td>';
			echo $form->_submit('Zapisz');
			echo '</td><td>';
			echo $form->_submit('Skopiuj aliasy ze switcha', array('name'=>'copyAliasesFromSwitch', 'id'=>'copyAliasesFromSwitch'));
			echo '</td><td><a href="'.$url.'/switches/'.$switch->serialNo.'">Powrót</a></td></tr></table>';
		}
		$copyAliases = (isset($this->_srv->get('msg')->info['copyAliasesFromSwitch']) && !is_null($portAliases));
		echo '</div>';
	}

	/**
	 * Wyświetla dane portu w API
	 */
	public function apiInfo(array $d) {
		echo $d['switchIp'].'/'.$d['ordinalNo'];
	}

	/**
	 * Wyświetla strukturę switchy (switch, port, podłączony switch, port na podłączonym sw.)
	 */
	public function apiStructure(array $d, $dormitory = null) {
		$ports = array();
		foreach ($d as $c) {
			$exist = false;
			foreach ($ports as $port) {
				$exist = $port->exist($c['switchIp'], $c['connectedSwitchIp']);
				if (!is_null($exist) && $exist) {
					$port->setConnectedPort($c['ordinalNo'], $c['dormitoryAlias']);
					break;
				}
			}
			if (!is_null($exist) && !$exist) {
				$ports[] = new SwitchStructure($c['switchIp'], $c['ordinalNo'], $c['dormitoryAlias'], $c['connectedSwitchIp']);
			}
		}
		foreach ($ports as $port) {
			$dorm = null;
			if (!is_null($dormitory)) {
				$dorm = $dormitory->alias;
			}
			$port->display($dorm);
		}
	}
}

class SwitchStructure
{
	private $ip;
	private $port;
	private $dorm;
	private $connectedIp;
	private $connectedPort;
	private $connectedDorm;

	public function __construct($ip, $port, $dorm, $connectedIp) {
		$this->ip = $ip;
		$this->port = $port;
		$this->dorm = $dorm;
		$this->connectedIp = $connectedIp;
	}

	public function setConnectedPort($port, $dorm) {
		$this->connectedPort = $port;
		$this->connectedDorm = $dorm;
	}

	public function exist($ip, $connectedIp) {
		if ($this->ip == $connectedIp && $this->connectedIp == $ip) {
			if (!is_null($this->connectedPort)) {
				return null;
			}
			return true;
		}
		return false;
	}

	public function display($dorm) {
		if (!is_null($this->connectedPort)) {
			if (!is_null($dorm) && $dorm != $this->dorm && $dorm != $this->connectedDorm) {
				return;
			}
			echo $this->ip.'/'.$this->port.':'.$this->connectedIp.'/'.$this->connectedPort."\n";
		}
	}
}