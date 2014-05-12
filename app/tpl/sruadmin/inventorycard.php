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
			$urlDevice = $url.'/computers/'.$device->id;
		} else if (get_class($device) == 'UFbean_SruAdmin_Switch') {
			$urlDevice = $url.'/switches/'.$device->serialNo;
		} else {
			$urlDevice = $url.'/devices/'.$device->id;
		}

		return $urlDevice;
	}
	
	public static function getDeviceUrlFromArray($device, $url) {
		if ($device['deviceTableId'] == UFbean_SruAdmin_InventoryCard::TABLE_SWITCH) {
			$url = $url.'/switches/'.$device['serialNo'];
		} else if ($device['deviceTableId'] == UFbean_SruAdmin_InventoryCard::TABLE_COMPUTER) {
			$url = $url.'/computers/'.$device['deviceId'];
		} else if ($device['deviceTableId'] == UFbean_SruAdmin_InventoryCard::TABLE_DEVICE) {
			$url = $url.'/devices/'.$device['deviceId'];
		}
		
		return $url;
	}

	public function details(array $d, $device) {
		$url = $this->url(0);
		
		echo '<p><em>Nr seryjny:</em> '.$d['serialNo'].'</p>';
		echo '<p><em>Na stanie:</em> <a href="'.$url.'/switches/dorm/'.$d['dormitoryAlias'].'">'.$d['dormitoryName'].'</a></p>';
		echo '<p><em>Nr inwentarzowy:</em> '.$d['inventoryNo'].'</p>';
		echo '<p><em>Na stanie od:</em> '.(is_null($d['received']) ? '' : date(self::TIME_YYMMDD, $d['received'])).'</p>';
		echo '<p><em>Komentarz:</em> '.nl2br($this->_escape($d['comment'])).'</p>';
		
		$urlDevice = UFtpl_SruAdmin_InventoryCard::getDeviceUrl($device, $url);
		echo '<p class="nav"><a href="'.$urlDevice.'/inventorycardhistory">Historia zmian</a> &bull;
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
		echo $form->dormitoryId('Na stanie', array(
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
		echo $form->dormitoryId('Na stanie', array(
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
		echo $form->inventoryNo('Nr inwentarzowy');
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
		$this->displayInventoryList($d, false, true);
	}
	
	public function inventoryList(array $d, $adminView = true) {
		$this->displayInventoryList($d, true, $adminView);
	}
	
	private function displayInventoryList(array $d, $filter, $adminView) {
		$url = $this->url(0);
		if ($filter) {
			echo '<label for="filter">Filtruj:</label> <input type="text" name="filter" value="" id="filter" />';
		}
		echo '<div class="legend">';
		echo '<table><tr><td class="noInventoryCard">Brak karty wyposażenia</td><td class="wrongInvCardData">Niezgodność lokalizacji urządzenia</td></tr></table>';
		echo '</div><br/>';
		
		echo '<table id="inventoryT" class="bordered"><thead><tr>';
		echo '<th>Urządzenie</th>';
		echo '<th>Na stanie DS</th>';
		echo '<th>Lokalizacja</th>';
		echo '<th>S/N</th>';
		echo '<th>Nr inw.</th>';
		echo '<th>Na stanie od</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr'.((is_null($c['cardId']) || $c['cardId'] == '') ? ' class="noInventoryCard"' : '').'>';
			echo '<td>';
			echo ($adminView ? '<a href="'.self::getDeviceUrlFromArray($c, $url).'">' : '');
			echo ($c['deviceTableId'] == UFbean_SruAdmin_InventoryCard::TABLE_SWITCH ? 'Switch ' : '').$c['deviceModelName'];
			echo ($adminView ? '</a>' : '').'</td>';
			echo '<td>'.strtoupper($c['cardDormitoryAlias']).'</td>';
			echo '<td'.((!is_null($c['cardId']) && $c['cardId'] != '' && $c['cardDormitoryId'] != $c['dormitoryId']) ? ' class="wrongInvCardData"' : '').'>'.strtoupper($c['dormitoryAlias']).', '.$c['locationAlias'].'</td>';
			echo '<td>'.$c['serialNo'].'</td>';
			echo '<td>'.$c['inventoryNo'].'</td>';
			echo '<td>'.($c['received'] == null ? '' : date(self::TIME_YYMMDD, $c['received'])).is_null($c['cardId']).'</td></tr>';
		}
		echo '</tbody>';
		echo '</table>';
?>
<script type="text/javascript">
<? if ($filter) { ?>
$(document).ready(function() {
	//default each row to visible
	$('tbody tr').addClass('visible');
	
	$('#filter').keyup(function(event) {
		//if esc is pressed or nothing is entered
		if (event.keyCode == 27 || $(this).val() == '') {
			//if esc is pressed we want to clear the value of search box
			$(this).val('');
			
			//we want each row to be visible because if nothing
			//is entered then all rows are matched.
			$('tbody tr').removeClass('visible').show().addClass('visible');
		} else { //if there is text, lets filter
			filter('tbody tr', $(this).val());
		}
	});
});

//filter results based on query
function filter(selector, query) {
	query = $.trim(query); //trim white space
	query = query.replace(/ /gi, '|'); //add OR for regex
  
	$(selector).each(function() {
		($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');
	});
}
<? } ?>

$(document).ready(function() 
    { 
        $("#inventoryT").tablesorter();
    } 
);
</script>
<?
	}
}
