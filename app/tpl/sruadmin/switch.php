<?php
/**
 * szablon beana switcha
 */
class UFtpl_SruAdmin_Switch
extends UFtpl_Common {
	
	protected $errors = array(
		'modelId' => 'Podaj model',
		'serialNo' => 'Podaj numer seryjny',
		'serialNo/duplicated' => 'Numer seryjny jest już zajęty',
		'inventoryNo/duplicated' => 'Numer inwentarzowy jest już zajęty',
		'hierarchyNo/duplicated' => 'Numer w hierarchii jest już zajęty',
		'ip/duplicated' => 'Numer IP jest już zajęty',
		'ip/noHierachyNo' => 'Numer IP może być przydzielony jedynie switchowi z numerem hierarchii',
		'dormitoryId' => 'Podaj akademik',
		'mac/wrongFormat' => 'Błędny format adresu MAC',
	);

	public static function displaySwitchName($dormitoryAlias, $hierarchyNo) {
		if (is_null($hierarchyNo)) {
			$swName = 'nieużywany';
		} else {
			$swName =  $dormitoryAlias.'-hp'.$hierarchyNo;
		}
		return $swName;
	}

	public function listSwitches(array $d, $dorm) {
		$url = $this->url(0).'/switches/';
		$urlds = $this->url(0).'/dormitories/';
		$lastDom = '-';

		foreach ($d as $c) {
			if($lastDom != $c['dormitoryId']) {
				if($lastDom != '-') echo '</ul>';
				echo '<h3><a href="'.$urlds.$c['dormitoryAlias'].'">'.$c['dormitoryName'].'</a></h3>';
				echo '<ul>';
			}
			
			echo '<li>';
			echo is_null($c['ip']) ? '<del>' : '';
			echo $c['operational'] ? '' : '<span class="inoperational">';
			echo '<a href="'.$url.$c['id'].'">';
			echo $this->displaySwitchName($c['dormitoryAlias'], $c['hierarchyNo']);
			echo ' ('.$this->_escape($c['model']).')';
			echo '</a>';
			echo $c['operational'] ? '' : '</span>';
			echo is_null($c['ip']) ? '</del>' : '';
			echo '</li>';
			
			$lastDom = $c['dormitoryId'];
			
		}
		echo '</ul>';
		echo '<p class="nav"><a href="'.$url.':add">Dodaj nowego switcha</a>';
		if (!is_null($dorm)) {
			echo ' <a href="'.$url.'">Pokaż wszystkie</a>';
		}
		echo '</p>';
	}

	public function titleDetails(array $d) {
		echo 'Switch ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function headerDetails(array $d) {
		echo '<h2>Switch <a href="'.$this->url(0).'/switches/'.$d['id'].'">';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo']);
		echo '</a></h2>';
	}

	public function details(array $d, $info, $lockouts) {
		$url = $this->url(0);

		if (is_null($info)) {
			echo $this->ERR('Nie jest możliwe podłączenie się do switcha');
		} else {
			$conf = UFra::shared('UFconf_Sru');
			if ($info['serialNo'] != $d['serialNo']) {
				echo $this->ERR('Zapisany w bazie danych numer seryjny switcha jest inny, niż podany przez switcha: '.$info['serialNo']);
			}
			if (array_key_exists($d['modelNo'], $conf->switchFirmware) && !stripos($info['ios'], $conf->switchFirmware[$d['modelNo']])) {
				echo $this->ERR('Oprogramowanie na switchu jest nieaktualne');
			}
		}

		echo '<h3>Dane urządzenia</h3>';
		echo '<p><em>Model:</em> '.$d['model'].' ('.$d['modelNo'].')</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p class="nav"><a href="'.$url.'/switches/dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> <a href="'.$url.'/switches/">Pokaż wszystkie</a> <a href="'.$url.'/switches/'.$d['id'].'/:edit">Edytuj</a> ';
		if (!is_null($info)) {
			echo '<a href="'.$url.'/switches/'.$d['id'].'/:lockoutsedit">Zmień zablokowane adresy MAC</a> ';
		}
		echo '<a href="'.$url.'/switches/'.$d['id'].'/tech">Technikalia</a> <span id="switchMoreSwitch"></span></p>';
		echo '<div id="switchMore">';
		echo '<p><em>Nr seryjny:</em> '.$d['serialNo'].'</p>';
		echo '<p><em>Akademik:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryName'].'</a></p>';
		echo '<p><em>Lokalizacja:</em> '.$d['localization'].'</p>';
		echo '<p><em>Sprawny:</em> '.($d['operational'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Nr inwentarzowy:</em> '.$d['inventoryNo'].'</p>';
		echo '<p><em>Na stanie od:</em> '.(is_null($d['received']) ? '' : date(self::TIME_YYMMDD, $d['received'])).'</p>';
		echo '<p><em>Zablokowane adresy MAC:</em><br/>';
		if (!is_null($lockouts)) {
			foreach ($lockouts as $lockout) {
				echo '<a href="'.$this->url(0).'/computers/search/mac:'.$lockout.'">'.$lockout.'</a><br/>';
			}
		}
		echo '</p>';
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		echo '</div>';
?><script type="text/javascript">
function changeVisibility() {
	var div = document.getElementById('switchMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('switchMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Szczegóły');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function techDetails(array $d, $info) {
		$url = $this->url(0);

		echo '<h3>Dane techniczne urządzenia</h3>';
		if (!is_null($info)) {
			echo '<p><em>IOS:</em> '.$info['ios'].'</p>';
			$uptimeD = floor($info['uptime'] / (100 * 60 * 60 * 24));
			$uptimeH = floor($info['uptime'] / (100 * 60 * 60)) - $uptimeD * 24;
			$uptimeM = floor($info['uptime'] / (100 * 60)) - $uptimeD * 24 * 60 - $uptimeH * 60;
			$uptimeS = floor($info['uptime'] / (100)) - $uptimeD * 24 * 60 * 60 - $uptimeH * 60 * 60 - $uptimeM * 60;
			echo '<p><em>Uptime:</em> '.$uptimeD.' dni, '.$uptimeH.' godzin, '.$uptimeM.' minut, '.$uptimeS.' sekund</p>';
			echo '<table style="text-align: center;"><tr>';
			echo '<td><em>CPU:</em> '.$info['cpu'].'%</td>';
			$mem = round($info['memUsed']/$info['memAll']*100,2);
			echo '<td><em>Pamięć zużyta:</em> '.$mem.'%</td>';
			echo '</tr><tr>';
			echo '<td><img src="http://chart.apis.google.com/chart?chs=300x150&cht=gom&chd=t:'.$info['cpu'].'&chco=00FF00,FFFF00,FF8040,FF0000&chxt=x,y&chxl=0:||1:|0%|100%" alt=""/></td>';
			echo '<td><img src="http://chart.apis.google.com/chart?chs=300x150&cht=gom&chd=t:'.$mem.'&chco=00FF00,FFFF00,FF8040,FF0000&chxt=x,y&chxl=0:||1:|0%|100%" alt=""/></td>';
			echo '</tr></table>';
		}
	}
	
	public function formAdd(array $d, $dormitories, $models) {
		$d['operational'] = true;
		$form = UFra::factory('UFlib_Form', 'switchAdd', $d, $this->errors);

		echo $form->_fieldset();
		$tmp = array();
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['model'].' ('.$model['modelNo'].')';
		}
		echo $form->modelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->serialNo('Numer seryjny');
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->hierarchyNo('Nr w hierarchii');
		echo $form->ip('IP');
		echo $form->localization('Lokalizacja');
		echo $form->inventoryNo('Nr inwentarzowy');
		echo $form->received('Na stanie od');
		echo $form->operational('Sprawny', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function titleEditDetails(array $d) {
		echo 'Edycja switcha ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function titlePortsEditDetails(array $d) {
		echo 'Edycja portów switcha ';
		echo $this->displaySwitchName($d['dormitoryAlias'], $d['hierarchyNo']);
		echo ' ('.$d['dormitoryName'].')';
	}

	public function formEdit(array $d, $dormitories, $models) {
		$d['received'] = is_null($d['received']) ? '' : date(self::TIME_YYMMDD, $d['received']);
		$form = UFra::factory('UFlib_Form', 'switchEdit', $d, $this->errors);
		echo $form->_fieldset();
		if ($this->_srv->get('msg')->get('switchEdit/errors/model/change')) {
			echo $this->ERR('Zmiana modelu switcha spowoduje skasowanie wszystkich przypisanych do niego portów. '.$form->ignoreModelChange('Kontnuuj', array('type'=>$form->CHECKBOX)));
		}
		$tmp = array();
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['model'].' ('.$model['modelNo'].')';
		}
		echo $form->modelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->serialNo('Numer seryjny');
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitoryId('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
		echo $form->hierarchyNo('Nr w hierarchii');
		echo $form->ip('IP');
		echo $form->localization('Lokalizacja');
		echo $form->inventoryNo('Nr inwentarzowy');
		echo $form->received('Na stanie od');
		echo $form->operational('Sprawny', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function formEditLockouts(array $d, $lockouts) {
		$form = UFra::factory('UFlib_Form', 'switchLockoutsEdit', $d, $this->errors);
		echo $form->_fieldset();
		if ($this->_srv->get('msg')->get('switchLockoutsEdit/errors/switch/writingError')) {
			echo $this->ERR('Nie udało się zapisać danych na switcha');
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
}