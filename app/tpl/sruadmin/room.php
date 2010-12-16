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
			$aliases[$roomInt]->addRoomForListing($c['alias'], $c['comment']);
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
					$dispRoom = ($connector == 0 ? $room->getExt() : $connector.$room->getExt());
					echo '<li><a href="'.$url.$dorm.'/'.$dispRoom.'">'.$dispRoom.(strlen($room->getComment()) ? '<img src="'.UFURL_BASE.'/i/gwiazdka.png" alt="" title="'.$room->getComment().'" />':'').'</a></li>';
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

	public function dormInhabitants(array $d, $dorm, $users, $export = false) {
		$url = $this->url(0).'/dormitories/';
		
		$dorm = isset($d[0]['dormitoryAlias']) ? $d[0]['dormitoryAlias'] : '';
		$aliases = array();
		
		foreach ($d as $c) {
			$roomInt = (int)$c['alias'];
			if (!array_key_exists($roomInt, $aliases)) {
				$aliases[$roomInt] = new Connector();
			}
			$aliases[$roomInt]->addRoom($c['alias'], $c['usersMax']);
		}
		ksort($aliases);

		foreach ($users as $user) {
			$roomInt = (int)$user['locationAlias'];
			$aliases[$roomInt]->addPerson($user['locationAlias'], $user);
		}
		
		if (!$export) {
			echo '<label for="filter">Szukaj:</label> <input type="text" name="filter" value="" id="filter" />';
		}
		echo '<div class="ips">';
		echo '<table><tr><td style="background: #cff; color: #000;">Kobieta</td><td style="background: #ccf; color: #000;">Mężczyzna</td><td style="background: #f22; color: #000;">Dokwaterowany</td></tr></table>';
		echo '</div><br/>';

		echo '<table style="width: 100%;"><thead><tr>';
		echo '<th>Pokój</th>';
		echo '<th>Mieszkańcy</th>';
		echo '</tr></thead><tbody>';

		while ($alias = current($aliases)) {
			$alias->sort();
			$connector = key($aliases);
			$rooms = $alias->getRooms();
			if ($rooms == null || count($rooms) == 0) {
			} else {
				foreach ($rooms as $room) {
					$dispRoom = ($connector == 0 ? '' : $connector).$room->getExt().' <small>('.$room->getLimit().'-os)</small>';
					echo '<tr><td style="border-top: 1px solid;">'.$dispRoom.'</td>';
					$i = 0;
					foreach ($room->getUsers() as $user) {
						$i++;
						if ($i > $room->getLimit()) {
							$bg = '#f22';
						} else if (substr($user['name'], -1) == 'a') {
							$bg = '#cff';
						} else {
							$bg = '#ccf';
						}
						echo '<td style="background: '.$bg.';"><a href="'.$this->url(0).'/users/'.$user['id'].'">'.$user['name'].' '.$user['surname'].'</a></td>';
					}
					if ($i < $room->getLimit()) {
						for (; $i < $room->getLimit(); $i++) {
							echo '<td style="background: #00f; color: #ff0;">WOLNE</td>';
						}
					}
					echo '</tr>';
				}
			}
			next($aliases);
		}
		echo '</tbody></table>';

?>
<script>
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
</script>
<?
	}
}

class Connector
{
	private $rooms = array();

	public function addRoom($room, $limit = 0, $addEmpty = false, $comment = null) {
		$roomInt = (int)$room;
		if (substr($room, 0, 1) == 'm') {
			$this->rooms[] = new Room($room, $limit, $comment);
		} else if (!$addEmpty && ($roomInt == 0 || $limit == 0)) {
			// nie dodajemy do zestawienia
		} else if ($addEmpty && $roomInt == 0) {
			$this->rooms[] = new Room($room, $limit, $comment);
		} else if (strlen($roomInt) < strlen($room)) {
			$this->rooms[] = new Room(substr($room, strlen($roomInt)), $limit, $comment);
		} else {
			$this->rooms[] = new Room(null, $limit, $comment);
		}
	}

	public function addRoomForListing($room, $comment) {
		$this->addRoom($room, 0, true, $comment);
	}

	public function addPerson($room, $user) {
		$roomInt = (int)$room;

		if (substr($room, 0, 1) == 'm') {
			foreach ($this->rooms as $c) {
				if ($c->getExt() == $room) {
					$c->addPerson($user);
					break;
				}
			}
		} else if ($roomInt == 0) {
			// nie dodajemy do zestawienia
		} else if (strlen($roomInt) < strlen($room)) {
			foreach ($this->rooms as $c) {
				if ($c->getExt() == substr($room, strlen($roomInt))) {
					$c->addPerson($user);
					break;
				}
			}
		} else {
			$this->rooms[0]->addPerson($user);
		}
	}
	
	public function sort() {
		sort($this->rooms);
	}

	public function getRooms() {
		return $this->rooms;
	}
}

class Room
{
	private $roomExt;
	private $limit;
	private $users = array();
	private $comment;

	function __construct($ext, $limit, $comment = null) {
		$this->roomExt = $ext;
		$this->limit = $limit;
		$this->comment = $comment;
	}

	function addPerson($user) {
		$this->users[] = $user;
	}

	public function getExt() {
		return $this->roomExt;
	}

	public function getLimit() {
		return $this->limit;
	}

	public function getUsers() {
		return $this->users;
	}

	public function getComment() {
		return $this->comment;
	}
}
