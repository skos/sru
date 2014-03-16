<?php
/**
 * karta wyposażenia
 */
class UFtpl_SruAdmin_InventoryCard
extends UFtpl_Common {
	
	protected $errors = array(
		'serialNo' => 'Podaj numer seryjny',
		'serialNo/duplicated' => 'Numer seryjny jest już zajęty',
		'inventoryNo/duplicated' => 'Numer inwentarzowy jest już zajęty',
	    	'received' => 'Błędny format daty',
		'dormitoryId' => 'Podaj akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
	);
	
	public static function getDeviceUrl($device, $url) {		
		if (get_class($device) == 'UFbean_Sru_Computer') {
			$urlDevice = $url.'/computers'.$device->id;
		} else if (get_class($device) == 'UFbean_SruAdmin_Switch') {
			$urlDevice = $url.'/switches/'.$device->serialNo;
		} else {
			$urlDevice = $url.'/devices/'.$device->id;
		}

		return $urlDevice;
	}

	public function details(array $d, $device) {
		$url = $this->url(0);
		
		echo '<p><em>Nr seryjny:</em> '.$d['serialNo'].'</p>';
		echo '<p><em>Na stanie:</em> <a href="'.$url.'/switches/dorm/'.$d['dormitoryAlias'].'">'.$d['dormitoryName'].'</a></p>';
		echo '<p><em>Nr inwentarzowy:</em> '.$d['inventoryNo'].'</p>';
		echo '<p><em>Na stanie od:</em> '.(is_null($d['received']) ? '' : date(self::TIME_YYMMDD, $d['received'])).'</p>';
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		
		$urlDevice = UFtpl_SruAdmin_InventoryCard::getDeviceUrl($device, $url);
		echo '<p class="nav"><a href="'.$urlDevice.'/inventorycardhistory">Historia</a> &bull;
			 <a href="'.$urlDevice.'/:inventorycardedit">Edytuj</a></p>';
	}
	
	public function formAdd(array $d, $dormitories) {
		$form = UFra::factory('UFlib_Form', 'inventoryCardAdd', $d, $this->errors);

		echo $form->_fieldset();
		echo $form->serialNo('Numer seryjny');
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Na stanie', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->inventoryNo('Nr inwentarzowy');
		echo $form->received('Na stanie od', array('type' => $form->CALENDER));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
	}

	public function titleEditDetails(array $d) {
		echo 'Edycja karty urządzenia o S/N: '.$d['serialNo'];
	}

	public function formEdit(array $d, $dormitories) {
		$d['received'] = is_null($d['received']) ? '' : date(self::TIME_YYMMDD, $d['received']);
		$d['dormitory'] = $d['dormitoryId'];

		$form = UFra::factory('UFlib_Form', 'inventoryCardEdit', $d, $this->errors);
		echo $form->_fieldset();
		echo $form->serialNo('Numer seryjny');
		$tmp = array();
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Na stanie', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->inventoryNo('Nr inwentarzowy');
		echo $form->received('Na stanie od', array('type' => $form->CALENDER));
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->inventoryCardId('', array('type'=>$form->HIDDEN, 'value'=>$d['id']));
	}
	
	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'inventoryCardSearch', $d, $this->errors);

		echo $form->serialNo('Nr seryjny');
		echo $form->name('Nr inwentarzowy');
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');                                         
		$dorms->listAll();

		$tmp = array();
		foreach ($dorms as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['alias']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp, '', ''),
		));
	}
	
	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			//TODO echo '<a href="'.$url.'/users/'.$c['id'].'">'.$this->_escape($c['name']).' "'.$this->_escape($c['login']).'" '.$this->_escape($c['surname']).'</a>'.(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').' <span><a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'/'.$c['locationAlias'].'">'.$c['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$c['dormitoryAlias'].'">'.$c['dormitoryAlias'].'</a>)</small></span>'.(!$c['active']?'</del>':'').(strlen($c['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['locationComment'].'" />':'').'</li>';
		}
	}
}
