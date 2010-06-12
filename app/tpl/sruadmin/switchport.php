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
	);

	public function details(array $d, $switch, $alias) {
		$url = $this->url(0).'/switches/';

		echo '<h3>Port '.$d['ordinalNo'].'</h3>';
		if(is_null($d['connectedSwitchId'])) {
			echo '<p><em>Lokalizacja:</em> <a href="'.$this->url(0).'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a></p>';
		} else {
			echo '<p><em>Podłączony switch:</em> <a href="'.$url.$d['connectedSwitchId'].'">'.$d['connectedSwitchDorm'].'-hp'.$d['connectedSwitchNo'].'</a></p>';
		}
		if (!is_null($alias) && $alias != '') {
			echo '<p><em>Alias portu: </em>'.$alias.'</p>';
		}
		echo '<p><em>Port admina:</em> '.($d['admin'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Komentarz:</em> '.$d['comment'].'</p>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> &bull; 
			 <a href="'.$url.'">Pokaż wszystkie</a> &bull; 
			<a href="'.$url.$switch->id.'/port/'.$d['id'].'/macs">Pokaż adresy MAC</a> &bull; 
			<a href="'.$url.$switch->id.'/port/'.$d['id'].'/:edit">Edytuj port</a></p>';
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
		echo '<p class="nav"><a href="'.$url.'dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> <a href="'.$url.'">Pokaż wszystkie</a> ';
		echo '<a href="'.$url.$switch->id.'/port/'.$d['id'].'/:edit">Edytuj port</a></p>';
	}

	public function formEditOne(array $d, $switch, $enabledSwitches, $status) {
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
		if ($this->_srv->get('msg')->get('switchPortEdit/errors/switch/noWriting')) {
			echo $this->ERR('Usiłujesz zmienić status portu nie zapisując go na switcha');
		}

		$tmp = array();
		foreach ($enabledSwitches as $sw) {
			if ($sw['id'] == $switch->id) continue;
			$tmp[$sw['id']] = $sw['dormitoryAlias'].'-hp'.$sw['hierarchyNo'];
		}

		echo '<h3>Edycja portu '.$d['ordinalNo'].'</h3>';
		echo $form->locationAlias('Przypisany pokój');
		echo $form->connectedSwitchId('Podłączony switch', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		if (!is_null($status)) {
			echo $form->portStatus('', array('type'=>$form->HIDDEN, 'value'=>($status == UFlib_Snmp_Hp::DISABLED ? 0 : 1)));
			echo $form->portEnabled('Port włączony', array('type'=>$form->CHECKBOX, 'value'=>$portEnabled));
		}
		echo $form->admin('Port admina', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz');
		if (!is_null($status)) {
			echo $form->copyToSwitch('Zapisz dane na switcha', array('type'=>$form->CHECKBOX));
		}
	}

	public function listPorts(array $d, $switch, $portStatuses) {
		$url = $this->url(0).'/switches/';

		echo '<div class="switchports">';
		echo '<h3>Lista portów</h3>';
		echo '<table>';
		for ($i = 0; $i < count($d); $i++) {
			if ($i % 8 == 0) {
				echo '<tr>';
			}
			echo '<td title="'.$this->_escape($d[$i]['comment']).'" class="';
			if ($portStatuses == null || !isset($portStatuses[$i])) {
				echo "unknown";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DISABLED) {
				echo "disabled";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DOWN) {
				echo "down";
			} else {
				echo "up";
			}
			echo '">';
			echo '<a href="'.$url.$switch->id.'/port/'.$d[$i]['id'].'">';
			echo $d[$i]['admin'] ?'<strong>' : '';
			echo $d[$i]['ordinalNo'];
			echo $d[$i]['admin'] ?'</strong>' : '';
			echo '</a>';
			echo ($d[$i]['comment'] == '') ? '' : ' <img src="'.UFURL_BASE.'/i/gwiazdka.png" />';
			echo '<br/><small>(';
			echo is_null($d[$i]['connectedSwitchId']) ? ('<a href="'.$this->url(0).'/dormitories/'.$d[$i]['dormitoryAlias'].'/'.$d[$i]['locationAlias'].'">'.
				$d[$i]['locationAlias'].'</a>') : ('<a href="'.$url.$d[$i]['connectedSwitchId'].'">'.$d[$i]['connectedSwitchDorm'].'-hp'.$d[$i]['connectedSwitchNo'].'</a>');
			echo ')</small>';
			echo '</td>';
			if (($i + 1) % 8 == 0) {
				echo '</tr>';
			}
		}
		echo '</table>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$switch->dormitoryAlias.'">Wróć do listy</a> &bull; 
			 <a href="'.$url.'">Pokaż wszystkie</a> &bull; 
			<a href="'.$url.$switch->id.'/:portsedit">Edytuj porty</a>
			</p>
			</div>';
	}

	public function listRoomPorts(array $d, $room, $portStatuses) {
		$url = $this->url(0).'/switches/';
		$i = 0;
		$j = 0;
		$switch = 0;

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
				echo '<h4>Switch <a href="'.$url.$port['switchId'].'">'.UFtpl_SruAdmin_Switch::displaySwitchName($port['dormitoryAlias'], $port['switchNo']).'</a></h4>';
				$switch = $port['switchId'];
				echo '<table>';
			}
			if ($j % 8 == 0) {
				echo '<tr>';
			}
			echo '<td title="'.$this->_escape($port['comment']).'" class="';
			if ($portStatuses == null) {
				echo "unknown";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DISABLED) {
				echo "disabled";
			} else if ($portStatuses[$i] == UFlib_Snmp_Hp::DOWN) {
				echo "down";
			} else {
				echo "up";
			}
			echo '">';
			echo '<a href="'.$url.$port['switchId'].'/port/'.$port['id'].'">';
			echo $port['admin'] ?'<strong>' : '';
			echo $port['ordinalNo'];
			echo $port['admin'] ?'</strong>' : '';
			echo '</a>';
			echo ($port['comment'] == '') ? '' : ' <img src="'.UFURL_BASE.'/i/gwiazdka.png" />';
			echo '</td>';
			if (($j + 1) % 8 == 0) {
				echo '</tr>';
			}
			$i++;
			$j++;
		}
		if (($j + 1) % 8 != 0) {
			while (($j + 1) % 8 != 0) {
				echo '<td></td>';
				$j++;
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<p class="nav"><a href="'.$url.'dorm/'.$room->dormitoryAlias.'">Pokaż switche akademika</a> </p>';
		echo '</div>';
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

		if ($this->_srv->get('msg')->get('switchPortsEdit/errors/switch/writingError')) {
			echo $this->ERR('Nie udało się zapisać danych na switcha');
		}
		echo $form->_fieldset();
		echo '<div class="switchPortsEdit">';
		if (is_null($portAliases)) {
			echo $this->ERR('Nie jest możliwe podłączenie się do switcha. <a href="'.$url.'/switches/'.$d['switch']->id.'">Powrót</a>');
		} else {
			echo '<br/><strong>Zapisanie danych spowoduje zapisanie danych także na switch.</strong>';
			echo '<table style="margin-left:auto; margin-right:auto;"><tr><td>';
			echo $form->_submit('Zapisz');
			echo '</td><td>';
			echo $form->_submit('Skopiuj aliasy ze switcha', array('name'=>'copyAliasesFromSwitch', 'id'=>'copyAliasesFromSwitch'));
			echo '</td><td><a href="'.$url.'/switches/'.$switch->id.'">Powrót</a></td></tr></table>';
		}
		$copyAliases = (isset($this->_srv->get('msg')->info['copyAliasesFromSwitch']) && !is_null($portAliases));

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
				if ($copyAliases && preg_match('/^[0-9]+/', $portAliases[$c['ordinalNo'] - 1]) > 0) {
					$locationAlias = $portAliases[$c['ordinalNo'] - 1];
					$copied = true;
				} else if (isset($post->switchPortsEdit[$c['id']]['locationAlias'])) {
					$locationAlias = $post->switchPortsEdit[$c['id']]['locationAlias'];
				}
				if ($copyAliases && preg_match('/^ds/', $portAliases[$c['ordinalNo'] - 1]) > 0) {
					$connectedSwitchId = array_search($portAliases[$c['ordinalNo'] - 1], $tmp);
					$copied = true;
				} else if (isset($post->switchPortsEdit[$c['id']]['connectedSwitchId'])) {
					$connectedSwitchId = $post->switchPortsEdit[$c['id']]['connectedSwitchId'];
				}
				if ($copyAliases && !$copied) {
					$comment = $portAliases[$c['ordinalNo'] - 1];
				} else if (isset($post->switchPortsEdit[$c['id']]['comment'])) {
					$comment = $post->switchPortsEdit[$c['id']]['comment'];
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
			echo '</td><td><a href="'.$url.'/switches/'.$switch->id.'">Powrót</a></td></tr></table>';
		}
		$copyAliases = (isset($this->_srv->get('msg')->info['copyAliasesFromSwitch']) && !is_null($portAliases));
		echo '</div>';
	}
}