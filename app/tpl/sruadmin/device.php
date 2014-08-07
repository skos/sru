<?php
/**
 * tpl urzadzenia
 */
class UFtpl_SruAdmin_Device
extends UFtpl_Common {
	protected $errors = array(
		'dormitoryId' => 'Podaj akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'deviceModelId' => 'Wybierz model',
	);
	
	public function listDevices(array $d, $dorm) {
		$urlSw = $this->url(0).'/switches/';
		$urlDs = $this->url(0).'/dormitories/';
		$urlIp = $this->url(0).'/ips/ds/';
		$urlDev = $this->url(0).'/devices/';
		$lastDom = '-';

		foreach ($d as $c) {
			if($lastDom != $c['dormitoryId']) {
				if($lastDom != '-') echo '</ul>';
				if (is_null($dorm)) {
					echo '<h3><a href="'.$urlDs.$c['dormitoryAlias'].'">'.$c['dormitoryName'].'</a> &bull; '
						. '<a href="'.$urlIp.$c['dormitoryAlias'].'">komputery '.strtoupper($c['dormitoryAlias']).'</a> &bull; '
						. '<a href="'.$urlSw.'dorm/'.$c['dormitoryAlias'].'">switche '.strtoupper($c['dormitoryAlias']).'</a> &bull; '
						. '<a href="'.$urlDev.'dorm/'.$c['dormitoryAlias'].'">urządzenia '.strtoupper($c['dormitoryAlias']).'</a></h3>';
				}
				echo '<ul>';
			}
			
			echo '<li>';
			echo $c['inoperational'] ? '<span class="unused">' : '';
			echo '<a href="'.$urlDev.$c['id'].'">';
			echo $c['deviceModelName'];
			echo '</a>';
			echo $c['inoperational'] ? '</span>' : '';
			echo ' - <small><a href="'.$urlDev.''.$c['id'].'/:edit">Edytuj</a></small></li>';
			
			$lastDom = $c['dormitoryId'];
			
		}
		echo '</ul>';
		echo '<p class="nav"><a href="'.$urlDev.':add">Dodaj nowe urządzenie</a>';
		if (!is_null($dorm)) {
			echo ' &bull; <a href="'.$urlDev.'">Pokaż wszystkie</a>';
		}
		echo '</p>';
	}
	
	public function titleDetails(array $d) {
		echo $d['deviceModelName'].' ('.$d['dormitoryName'].')';
	}

	public function headerDetails(array $d, $left = null, $right = null) {
		echo '<h2>';
		if(!is_null($left)) {
			echo '<a href="'.$this->url(0).'/devices/'.$left['id'].'"><</a> ';
		}
		echo '<a href="'.$this->url(0).'/devices/'.$d['id'].'">'.$d['deviceModelName'].'</a>';
		if(!is_null($right)){
			echo ' <a href="'.$this->url(0).'/devices/'.$right['id'].'">></a>';
		}
		echo '</h2>';
	}

	public function details(array $d) {
		$url = $this->url(0);
		$acl = $this->_srv->get('acl');

		echo '<h3>Dane urządzenia</h3>';
		echo '<p'.($d['inoperational'] ? ' class="unused"' : '').'><em>Nieużywany:</em> '.($d['inoperational'] ? 'tak' : 'nie').'</p>';
		echo '<p><em>Lokalizacja:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small>'.(strlen($d['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['locationComment'].'" />':'').'</p>';
		echo '<p class="nav"><a href="'.$url.'/devices/dorm/'.$d['dormitoryAlias'].'">Wróć do listy</a> &bull; 
			 <a href="'.$url.'/devices/">Pokaż wszystkie</a> &bull; 
			 <a href="'.$url.'/devices/'.$d['id'].'/history">Historia</a> &bull;
			 <a href="'.$url.'/devices/'.$d['id'].'/:edit">Edytuj</a> &bull; ';
		if ($acl->sruAdmin('device', 'inventoryCardAdd')) {
			echo ' <a href="'.$url.'/devices/'.$d['id'].'/:inventorycardadd">Dodaj kartę wyposażenia</a> &bull; ';
		}
		echo ' <span id="switchMoreSwitch"></span></p>';
		echo '<div id="switchMore">';
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		echo '</div>';
		
		UFlib_Script::switchMoreChangeVisibility();
	}
	
	public function formAdd(array $d, $dormitories, $models) {
		$d['inoperational'] = false;
		$form = UFra::factory('UFlib_Form', 'deviceAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $this->INFO('Dodaj nowe urządzenie tylko gdy otrzymałeś je właśnie z przetargu.<br/>Jeśli otrzymałeś je w ramach wymiany gwarancyjnej, edytuj urządzenie już dodane do SRU.');
		$tmp = array();
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['name'];
		}
		echo $form->deviceModelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
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
		echo $form->locationAlias('Lokalizacja', array('after'=>UFlib_Helper::displayHint("Pomieszczenie w akademiku, gdzie znajduje się urządzenie.")));
		echo $form->inoperational('Nieużywany', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}
	
	public function titleEditDetails(array $d) {
		echo 'Edycja ';
		echo $d['deviceModelName'].' ('.$d['dormitoryName'].')';
	}
	
	public function formEdit(array $d, $dormitories, $models) {
		$d['dormitory'] = $d['dormitoryId'];
		$form = UFra::factory('UFlib_Form', 'deviceEdit', $d, $this->errors);
		echo $form->_fieldset();
		$tmp = array();
		foreach ($models as $model) {
			$tmp[$model['id']] = $model['name'];
		}
		echo $form->deviceModelId('Model', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
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
		echo $form->locationAlias('Lokalizacja', array('after'=>UFlib_Helper::displayHint("Pomieszczenie w akademiku, gdzie znajduje się urządzenie.")));
		echo $form->inoperational('Nieużywany', array('type'=>$form->CHECKBOX));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}
	
	public function shortList(array $d) {
		$url = $this->url(0).'/devices/';

		foreach ($d as $c) {		
			echo '<li>';
			echo $c['inoperational'] ? '<span class="unused">' : '';
			echo '<a href="'.$url.$c['id'].'">'.$c['deviceModelName'].'</a>';
			echo $c['inoperational'] ? '</span>' : '';
			echo ' - <small><a href="'.$url.''.$c['id'].'/:edit">Edytuj</a>';
			echo '</small></li>';
		}
		echo '</ul>';
	}
}
