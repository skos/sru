<?php
/**
 * szablon beana pokoju
 */
class UFtpl_SruAdmin_Room
extends UFtpl_Common {
	
	public function listRooms(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		$lastRoom = -1;
		
		$dorm = isset($d[0]['dormitoryAlias']) ? $d[0]['dormitoryAlias'] : '';
		$aliases = array();
		
		foreach ($d as $c) {
			$roomInt = (int)$c['alias'];
			if (!array_key_exists($roomInt, $aliases)) {
				$aliases[$roomInt] = new Connector();
			}
			$aliases[$roomInt]->addRoom($c['alias']);
		}
		ksort($aliases);

		while ($alias = current($aliases)) {
			$alias->sort();
			$connector = key($aliases);
			if((substr($lastRoom, 0, 1) != substr($connector, 0,1) && ($connector < 1 || $connector > 9)) || $lastRoom == 0 || ($lastRoom < 100 && $connector >= 100)) {
				if($lastRoom != -1) {
					echo '</ul><ul>';
				} else {
					echo '<ul class="first">';
				}
			}
			$rooms = $alias->getRooms();
			if ($rooms == null || count($rooms) == 0) {
				echo '<li><a href="'.$url.$dorm.'/'.$connector.'">'.$connector.'</a></li>';
			} else {
				foreach ($rooms as $room) {
					$dispRoom = $connector == 0 ? $room : $connector.$room;
					echo '<li><a href="'.$url.$dorm.'/'.$dispRoom.'">'.$dispRoom.'</a></li>';
				}
			}
			$lastRoom = $connector;
			next($aliases);
		}
		echo '</ul>';
	}

	public function titleDetails(array $d) {
		echo $d['alias'].' ('.$d['dormitoryAlias'].')';
	}
	public function details(array $d) {
		
		$url = $this->url(0);
		echo '<h2>'.$d['alias'].' ('.$d['dormitoryAlias'].')<br/><small>(liczba użytkowników: '.$d['userCount'].' &bull; liczba komputerów: '.$d['computerCount'].')</small></h2>';
		if ($d['comment']) {
			echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';		
		}
		echo '<p class="nav"><a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'/:edit">Edytuj</a></p>';
	}

	public function formEdit(array $d) {
	
		$form = UFra::factory('UFlib_Form', 'roomEdit', $d, array());
		
		echo $form->_start($this->url());
		
		echo $form->_fieldset('Komentarz');
		echo $form->comment('', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}
}

class Connector
{
	private $rooms = array();

	public function addRoom($room) {
		$roomInt = (int)$room;
		if ($roomInt == 0) {
			$this->rooms[] = $room;
		} else if (strlen($roomInt) < strlen($room)) {
			$this->rooms[] = substr($room, strlen($roomInt));
		}
	}
	
	public function sort() {
		sort($this->rooms);
	}

	public function getRooms() {
		return $this->rooms;
	}
}
