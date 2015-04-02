<?php
/**
 * switcha
 */
class UFtpl_SruAdmin_Switch
extends UFtpl_Common {
	
	protected $errors = array(
		'modelId' => 'Podaj model',
		'serialNo' => 'Podaj numer seryjny',
		'serialNo/duplicated' => 'Numer seryjny jest już zajęty',
		'inventoryNo/duplicated' => 'Numer inwentarzowy jest już zajęty',
		'hierarchyNo' => 'Numer w hierarchii jest niewłaściwy',
		'hierarchyNo/duplicated' => 'Numer w hierarchii jest już zajęty',
		'ip/notFound' => 'Niedozwolony adres IP',
		'ip/duplicated' => 'Numer IP jest już zajęty',
		'ip/noHierachyNo' => 'Numer IP może być przydzielony jedynie switchowi z numerem hierarchii',
		'ip/regexp' => 'Błędny format numeru IP',
		'dormitoryId' => 'Podaj akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'mac/wrongFormat' => 'Błędny format adresu MAC',
		'received' => 'Błędny format daty',
	);

	public static function displaySwitchName($dormitoryAlias, $hierarchyNo, $lab = false) {
		if (is_null($hierarchyNo)) {
			$swName = $dormitoryAlias.($lab ? '-lab' : '').'-nieużywany';
		} else {
			$swName =  $dormitoryAlias.($lab ? '-lab' : '-hp').$hierarchyNo;
		}
		return $swName;
	}

	public function listSwitches(array $d, $dorm) {
		$url = $this->url(0).'/switches/';
		$urlds = $this->url(0).'/dormitories/';
		$urlIp = $this->url(0).'/ips/ds/';
		$urlDev = $this->url(0).'/devices/';
		$conf = UFra::shared('UFconf_Sru');
		$lastDom = '-';
		$switches = array();

		foreach ($d as $c) {
			if(!array_key_exists($c['model'], $switches)) {
				$switches[$c['model']] = 1;
			} else {
				$switches[$c['model']]++;
			}
			if($lastDom != $c['dormitoryId']) {
				if($lastDom != '-') echo '</ul>';
				if (is_null($dorm)) {
					echo '<h3><a href="'.$urlds.$c['dormitoryAlias'].'">'.$c['dormitoryName'].'</a> &bull; '
						. '<a href="'.$urlIp.$c['dormitoryAlias'].'">komputery '.strtoupper($c['dormitoryAlias']).'</a> &bull; '
						. '<a href="'.$url.'dorm/'.$c['dormitoryAlias'].'">switche '.strtoupper($c['dormitoryAlias']).'</a> &bull; '
						. '<a href="'.$urlDev.'dorm/'.$c['dormitoryAlias'].'">urządzenia '.strtoupper($c['dormitoryAlias']).'</a></h3>';
				}
				echo '<ul>';
			}
			
			echo '<li>';
			echo is_null($c['ip']) ? '<del>' : '';
			echo $c['inoperational'] ? '<span class="inoperational">' : '';
			echo '<a href="'.$url.$c['serialNo'].'">';
			echo $this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']);
			echo ' ('.$this->_escape($c['model']).')';
			echo '</a>';
			echo $c['inoperational'] ? '</span>' : '';
			echo is_null($c['ip']) ? '</del>' : '';
			echo ' - <small><a href="'.$url.''.$c['serialNo'].'/:edit">Edytuj</a>';
			$swstatsLink = str_replace($conf->swstatsSwitchRegex, UFtpl_SruAdmin_Switch::displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']), $conf->swstatsLinkSwitch);
			if (!is_null($c['ip'])) {
				echo ' &bull; <a href="'.$url.''.$c['serialNo'].'/:lockoutsedit">Lockout-MAC</a>';
				echo ' &bull; <a href="'.$url.$c['serialNo'].'/tech">Technikalia</a>';
				echo ' &bull; <a href="'.$swstatsLink.'">SWStats</a>';
			}
			echo '</small></li>';
			
			$lastDom = $c['dormitoryId'];
			
		}
		echo '</ul>';
		echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego switcha</a>';
		if (!is_null($dorm)) {
			echo ' &bull; <a href="'.$url.'">Pokaż wszystkie</a>';
		}
		echo '</p>';

		arsort($switches);
		echo '<h3>Statystyka</h3>';
		echo '<table style="border-spacing: 0px;">';
		$sum = 0;
		foreach ($switches as $model=>$count) {
			echo '<tr><td style="padding-right: 40px;">'.$model.'</td><td>'.$count.'</td></tr>';
			$sum += $count;
		}
		echo '<tr><td style="border-top: 1px solid;">SUMA:</td><td style="border-top: 1px solid;">'.$sum.'&nbsp;</td></tr>';
		echo '</table>';
	}

	public function titleDetails(array $d) {
		echo 'Switch ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo'], $d['lab']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function headerDetails(array $d, $left = null, $right = null) {
		echo '<h2>';
		if(!is_null($left)) {
			echo '<a href="'.$this->url(0).'/switches/'.$left['serialNo'].'"><</a> ';
		}
		echo 'Switch <a href="'.$this->url(0).'/switches/'.$d['serialNo'].'">'.$this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo'], $d['lab']).'</a>';
		if(!is_null($right)){
			echo ' <a href="'.$this->url(0).'/switches/'.$right['serialNo'].'">></a>';
		}
		echo '</h2>';
	}

	public function details(array $d, $info, $lockouts) {
		$url = $this->url(0);
		$conf = UFra::shared('UFconf_Sru');

		if (is_null($info)) {
			echo $this->ERR('Nie jest możliwe podłączenie się do switcha');
		} else {
			if ($info['serialNo'] != $d['serialNo']) {
				echo $this->ERR('Zapisany w bazie danych numer seryjny switcha jest inny, niż podany przez switcha: '.$info['serialNo']);
			}
			if (!stripos($info['ios'], $d['modelFirmware'])) {
				echo $this->ERR('Oprogramowanie na switchu jest nieaktualne (aktualne to: '.$d['modelFirmware'].'(...))');
			}
		}
		echo '<h3>Dane urządzenia</h3>';
		echo '<p'.($d['inoperational'] ? ' class="inoperational"' : '').'><em>Model:</em> '.$d['model'].' ('.$d['modelNo'].')</p>';
		echo '<p><em>IP:</em> '.$d['ip'].' '.($d['lab'] ? '(SKOSlab)' : '').'</p>';
		echo '<p class="nav"><a href="'.$url.'/switches/dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> &bull; 
			 <a href="'.$url.'/switches/">Pokaż wszystkie</a> &bull; 
			 <a href="'.$url.'/switches/'.$d['serialNo'].'/history">Historia</a> &bull;
			 <a href="'.$url.'/switches/'.$d['serialNo'].'/:edit">Edytuj</a> &bull; ';
		if (!is_null($info)) {
			echo '<a href="'.$url.'/switches/'.$d['serialNo'].'/:lockoutsedit">Lockout-MAC</a> &bull; ';
		}
		echo '<a href="'.$url.'/switches/'.$d['serialNo'].'/tech">Technikalia</a> &bull;';
		if (!is_null($d['ip'])) {
			$swstatsLink = str_replace($conf->swstatsSwitchRegex, UFtpl_SruAdmin_Switch::displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo'], $d['lab']), $conf->swstatsLinkSwitch);
			echo ' <a href="'.$swstatsLink.'">SWStats</a> &bull;';
		}
		echo ' <span id="switchMoreSwitch"></span>';
		if (strlen($d['comment'])) echo ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['comment'].'" />';
		echo '</p>';
		echo '<div id="switchMore">';
		echo '<p><em>Lokalizacja:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small>'.(strlen($d['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['locationComment'].'" />':'').'</p>';
		echo '<p'.($d['inoperational'] ? ' class="inoperational"' : '').'><em>Uszkodzony:</em> '.($d['inoperational'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Zablokowane adresy MAC:</em><br/>';
		if (!is_null($lockouts)) {
			foreach ($lockouts as $lockout) {
				echo '<a href="'.$this->url(0).'/computers/search/mac:'.$lockout.'">'.$lockout.'</a><br/>';
			}
		}
		echo '</p>';
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		echo '</div>';
		
		UFlib_Script::switchMoreChangeVisibility();
	}

	public function techDetails(array $d, $info, $gbics) {
		$url = $this->url(0);
		$vlanUrl = $url.'/ips/vlan/';

		echo '<h3>Dane techniczne urządzenia</h3>';
		if (!is_null($info)) {
			echo '<p><em>IOS:</em> '.$info['ios'].'</p>';
			$uptimeD = floor($info['uptime'] / (100 * 60 * 60 * 24));
			$uptimeH = floor($info['uptime'] / (100 * 60 * 60)) - $uptimeD * 24;
			$uptimeM = floor($info['uptime'] / (100 * 60)) - $uptimeD * 24 * 60 - $uptimeH * 60;
			$uptimeS = floor($info['uptime'] / (100)) - $uptimeD * 24 * 60 * 60 - $uptimeH * 60 * 60 - $uptimeM * 60;
			echo '<p><em>Uptime:</em> '.$uptimeD.' dni, '.$uptimeH.' godzin, '.$uptimeM.' minut, '.$uptimeS.' sekund</p>';
			echo '<p><em>VLANy:</em> ';
			$vlans = '';
			foreach ($info['vlans'] as $id=>$vlan) {
				$vlans .= '<a href="'.$vlanUrl.substr($id, strrpos($id, '.') + 1).'">'.$vlan.' ('.substr($id, strrpos($id, '.') + 1).')</a>, ';
			}
			if (strlen($vlans) > 0) {
				$vlans = substr($vlans, 0, -2);
			}
			echo $vlans.'</p>';
			$mem = round(($info['memAll']-$info['memFree'])/$info['memAll']*100,2);;
			
			echo '
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["gauge"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Label", "Value"],
          ["CPU", '.$info['cpu'].'],
	  ["Zużyta pamięć", '.$mem.']
        ]);

        var options = {
          width: 800, height: 200,
          redFrom: 90, redTo: 100,
          yellowFrom:75, yellowTo: 90,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById("chart_div"));
        chart.draw(data, options);
      }
</script>';

			echo '<div id="chart_div"></div>';
		} else {
			echo $this->ERR('Nie udało się pobrać informacji.');
		}
		if ($d['modelSfpPorts'] > 0) {
			echo '<h3>Dane mini-GBICków w urządzeniu</h3>';
			if (!is_null($gbics)) {
				echo '<table class="bordered"><tr><th>Port</th><th>Model</th><th>S/N '.UFlib_Helper::displayHint("W przypadku oryginalnych mini-GBICów HP należy na początku S/N dodać &quot;MY3&quot;").'</th></tr>';
				for ($i = 1; $i <= $d['modelSfpPorts']; $i++) {
					$port = $d['modelPorts'] - $d['modelSfpPorts'] + $i;
					echo '<tr><td>'.$port.'</td><td>'.(isset($gbics[$port][1]) ? $gbics[$port][1] : '-').'</td><td>'.(isset($gbics[$port][0]) ? $gbics[$port][0] : '-').'</td>';
				}
				echo '</table>';
			} else {
				echo $this->ERR('Nie udało się pobrać informacji.');
			}
		}
	}
	
	public function formAdd(array $d, $dormitories, $models) {
		$d['inoperational'] = false;
		$form = UFra::factory('UFlib_Form', 'switchAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $this->INFO('Dodaj nowego switcha tylko gdy otrzymałeś go właśnie z przetargu.<br/>Jeśli otrzymałeś go w ramach wymiany gwarancyjnej, edytuj switcha już dodanego do SRU.');
		echo '<h3>Switch</h3>';
		$tmp = array();
		echo $form->ip('IP', array('after'=>UFlib_Helper::displayHint("IP switcha. Brak IP oznacza, że switch został wyłączony (czasowo). Jeżeli switch jest nieużywany całkowicie, należy usunąć mu nr w hierarchii.", false).' <button type="button" onclick=fillData()>Pobierz dane</button><br/>'));
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['model'].' ('.$model['modelNo'].')';
		}
		echo $form->modelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->hierarchyNo('Nr w hierarchii', array('after'=>UFlib_Helper::displayHint("Nr kolejny switcha w akademiku, np. dla pierwszego switcha (dsX-hp0) wpisujemy 0. Brak nr oznacza, że switch jest nieużywany.")));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Lokalizacja', array('after'=>UFlib_Helper::displayHint("Pomieszczenie w akademiku, gdzie znajduje się switch.")));
		echo $form->inoperational('Uszkodzony', array('type'=>$form->CHECKBOX));
		echo $form->lab('SKOSlab', array('type'=>$form->CHECKBOX), array('after'=>UFlib_Helper::displayHint("Czy switch znajduje się w SKOSlabie (służy do testów).")));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo '<h3>Karta wyposażenia</h3>';
		echo $form->serialNo('Numer seryjny');
		echo $form->invCardDormitory('Na stanie', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->inventoryNo('Nr inwentarzowy');
		echo $form->received('Na stanie od', array('type' => $form->CALENDER));
?>
<script>
function fillData() {
	var ip = document.getElementById("switchAdd_ip").value;
	var models = new Array();
	<?
	$i = 0;
	foreach ($models as $model) {
		$i++;
		echo 'models['.$i.'] = "'.$model['modelNo'].'";';
	}
	echo 'var size = '.$i.';';
	?>
	if (ip == '' || ip.length < 8) return;
	var dormitory = ip.substring(ip.indexOf(".") + 1, ip.lastIndexOf("."));
	dormitory = dormitory.substring(ip.indexOf("."), dormitory.length);
	var hierarchyNo = ip.substring(ip.lastIndexOf(".")+1, ip.length);
	document.getElementById("switchAdd_dormitory").selectedIndex = dormitory -1 ;
	document.getElementById("switchAdd_hierarchyNo").value = hierarchyNo;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var response = eval("(" + xmlhttp.responseText + ")");
			var model = response.model;
			for(i = 1; i <= size; i++) {
				if (models[i] == model) {
					document.getElementById("switchAdd_modelId").selectedIndex = i;
					break;
				}
			}
			document.getElementById("switchAdd_serialNo").value = response.serialNo;
		}
	}
	xmlhttp.open("GET","<? echo $this->url(0); ?>/switches/getData/" + encodeURIComponent(ip), true);
	xmlhttp.send();
}

</script>
<?
	}

	public function titleEditDetails(array $d) {
		echo 'Edycja switcha ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo'], $d['lab']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function titlePortsEditDetails(array $d) {
		echo 'Edycja portów switcha ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo'], $d['lab']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function formEdit(array $d, $dormitories, $models) {
		$d['dormitory'] = $d['dormitoryId'];
		$form = UFra::factory('UFlib_Form', 'switchEdit', $d, $this->errors);
		echo $form->_fieldset();
		if ($this->_srv->get('msg')->get('switchEdit/errors/model/change')) {
			echo $this->INFO('Zmień model tylko gdy otrzymany z wymiany gwarancyjnej switch ma inny model niż reklamowany.<br/>Jeśli otrzymałeś nowy switch z przetargu, dodaj go jako nowe urządzenie.');
			echo $this->ERR('Zmiana modelu switcha spowoduje skasowanie wszystkich przypisanych do niego portów.<br/>'.$form->ignoreModelChange('Kontnuuj', array('type'=>$form->CHECKBOX)));
		}
		echo $form->ip('IP', array('after'=>UFlib_Helper::displayHint("IP switcha. Brak IP oznacza, że switch został wyłączony (czasowo). Jeżeli switch jest nieużywany całkowicie, należy usunąć mu nr w hierarchii.")));
		$tmp = array();
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['model'].' ('.$model['modelNo'].')';
		}
		echo $form->modelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->hierarchyNo('Nr w hierarchii', array('after'=>UFlib_Helper::displayHint("Nr kolejny switcha w akademiku, np. dla pierwszego switcha (dsX-hp0) wpisujemy 0. Brak nr oznacza, że switch jest nieużywany.")));
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Lokalizacja', array('after'=>UFlib_Helper::displayHint("Pomieszczenie w akademiku, gdzie znajduje się switch.")));
		echo $form->inoperational('Uszkodzony', array('type'=>$form->CHECKBOX));
		echo $form->lab('SKOSlab', array('type'=>$form->CHECKBOX), array('after'=>UFlib_Helper::displayHint("Czy switch znajduje się w SKOSlabie (służy do testów).")));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function formEditLockouts(array $d, $lockouts) {
		$form = UFra::factory('UFlib_Form', 'switchLockoutsEdit', $d, $this->errors);
		echo $form->_fieldset();
		if ($this->_srv->get('msg')->get('switchLockoutsEdit/errors/switch/writingError')) {
			echo $this->ERR('Nie udało się zapisać danych na switcha');
		}
		if ($this->_srv->get('msg')->get('switchLockoutsEdit/errors/switch/nothingToDo')) {
			echo $this->ERR('Nie podano żadnych ustawień do zmiany');
		}
		if (!is_null($lockouts)) {
			echo '<em>Usuń zablokowane adresy MAC:</em><br/>';
			foreach ($lockouts as $lockout) {
				echo $form->lockout($lockout, array('type'=>$form->CHECKBOX, 'name'=>'switchLockoutsEdit[lockouts]['.$lockout.']', 'id'=>'switchLockoutsEdit[lockouts]['.$lockout.']'));
			}
			echo '<br/>';
		}
		echo $form->mac('Zablokuj adres MAC');
	}

	public function shortList(array $d) {
		$url = $this->url(0).'/switches/';
		$conf = UFra::shared('UFconf_Sru');

		foreach ($d as $c) {		
			echo '<li>';
			echo is_null($c['ip']) ? '<del>' : '';
			echo $c['inoperational'] ? '<span class="inoperational">' : '';
			echo '<a href="'.$url.$c['serialNo'].'">';
			echo $this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']);
			echo ' ('.$this->_escape($c['model']).')';
			echo '</a>';
			echo $c['inoperational'] ? '</span>' : '';
			echo is_null($c['ip']) ? '</del>' : '';
			echo ' - <small><a href="'.$url.''.$c['serialNo'].'/:edit">Edytuj</a>';
			$swstatsLink = str_replace($conf->swstatsSwitchRegex, UFtpl_SruAdmin_Switch::displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']), $conf->swstatsLinkSwitch);
			if (!is_null($c['ip'])) {
				echo ' &bull; <a href="'.$url.''.$c['serialNo'].'/:lockoutsedit">Lockout-MAC</a>';
				echo ' &bull; <a href="'.$url.$c['serialNo'].'/tech">Technikalia</a>';
				echo ' &bull; <a href="'.$swstatsLink.'">SWStats</a>';
			}
			echo '</small></li>';
			
		}
		echo '</ul>';
	}
	
	public function apiList(array $d) {
		foreach ($d as $c) {
			echo $c['ip']."\n";
		}
	}
	
	public function apiModelList(array $d) {
		foreach ($d as $c) {
			echo $c['ip']."/".$c['modelNo']."\n";
		}
	}
	
	public function configDnsRev(array $d, $mask) {
		foreach ($d as $c) {
			if ($mask == 24) {
				echo substr(strrchr($c['ip'], '.'),1)."\t\tPTR\t".$this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']).'.'.$c['domainSuffix'].".\n";
			} else if ($mask == 16) {
				$parts = explode('.', $c['ip']);
				echo $parts[3].'.'.$parts[2]."\t\tPTR\t".$this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab']).'.'.$c['domainSuffix'].".\n";
			}
		}
	}
	
	public function configDns(array $d) {
		foreach ($d as $c) {
			echo $this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo'], $c['lab'])."\t\tA\t".$c['ip']."\n";
		}
	}
}
