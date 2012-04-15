<?php
/**
 * szablon beana pokoju
 */
class UFtpl_SruAdmin_Room
extends UFtpl_Common {
	
	protected static $locationTypesForWalet = array(
		1 => 'Studencki',
		2 => 'Gościnny',
	);

	protected static $locationTypesForAdmin = array(
		11 => 'SKOS',
		12 => 'Administracja',
	);
	
	protected $errors = array(
		'usersMax' => 'Podaj poprawną liczbę osób (0-9)',
	);
	
	public static function getRoomType($typeId) {
		$locTypes = self::$locationTypesForWalet + self::$locationTypesForAdmin;
		return $locTypes[$typeId];
	}
	
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
					echo '<li><a href="'.$url.$dorm.'/'.$dispRoom.'">'.$dispRoom.(strlen($room->getComment()) ? '<img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$room->getComment().'" />':'').'</a></li>';
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
	public function details(array $d, $left = null, $right = null) {
		$url = $this->url(0);
		$urlRoom = $url.'/dormitories/'.$d['dormitoryAlias'].'/';

		echo '<h2>';
		if($left != null){
			echo '<a href="'.$urlRoom.$left['alias'].'" ><</a> ';
		}
		echo $d['alias'].' (<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.strtoupper($d['dormitoryAlias']).'</a>)';
		if($right != null){
			echo ' <a href="'.$urlRoom.$right['alias'].'" >></a>';
		}
		echo '<br/><small>(liczba użytkowników: '.$d['userCount'].' &bull; liczba komputerów: '.$d['computerCount'].')</small></h2>';
		
		echo '<p><em>Typ:</em> '.self::getRoomType($d['typeId']).'</p>'; 
		if ($d['comment']) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';		
		}
		echo '<p class="nav">';
		echo '<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'">Dane</a>';
		echo ' &bull; <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'/:edit">Edytuj</a>';
		echo ' &bull; <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'/history">Historia zmian</a>';
		echo '</p>';

		if(isset($_COOKIE['SRUDisplayUsers']) && $_COOKIE['SRUDisplayUsers'] == '1' && !isset($_COOKIE['SRUDisplayUsersChanged'])){
			echo '<p class="nav"><a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'" onClick="fullList()">Wyświetl pełną listę użytkowników i hostów</a></p>';
		}else if(isset($_COOKIE['SRUDisplayUsers']) && $_COOKIE['SRUDisplayUsers'] == '1'){
			echo '<p class="nav"><a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['alias'].'">Wyświetl skróconą listę użytkowników i hostów</a></p>';
		}
?>
<script type="text/javascript">
function fullList() {
	document.cookie = 'SRUDisplayUsersChanged=1; expires=date.getTime()+(24*60*60*3650); path=/';
}
</script>
<?
	}

	public function formEdit(array $d) {
		$form = UFra::factory('UFlib_Form', 'roomEdit', $d, $this->errors);
		echo $form->_start($this->url());
		
		echo $form->_fieldset('Dane pomieszczenia');
		if (array_key_exists($d['typeId'], self::$locationTypesForAdmin)) {
			echo $form->typeId('Typ', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(self::$locationTypesForAdmin),
			));
		}
		echo $form->comment('', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_submit('Zapisz');
		echo $form->_end();
		echo $form->_end(true);
	}
	
	public function formEditWalet(array $d) {
		$form = UFra::factory('UFlib_Form', 'roomEdit', $d, $this->errors);
		echo $form->_start($this->url());
		
		echo $form->_fieldset('Dane pokoju');
		if (array_key_exists($d['typeId'], self::$locationTypesForWalet)) {
			echo $form->typeId('Typ', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(self::$locationTypesForWalet),
			));
		}
		echo $form->usersMax('Liczba miejsc');
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
			if ((int)$c['alias'] == 0) {
				$roomInt = $c['alias'];
			}
			if (!array_key_exists($roomInt, $aliases)) {
				$aliases[$roomInt] = new Connector();
			}
			$aliases[$roomInt]->addRoom($c['alias'], $c['usersMax']);
		}
		ksort($aliases);

		foreach ($users as $user) {
			$roomInt = (int)$user['locationAlias'];
			if ((int)$user['locationAlias'] == 0) {
				$roomInt = $user['locationAlias'];
			}
			if (array_key_exists($roomInt, $aliases)) {
				$aliases[$roomInt]->addPerson($user['locationAlias'], $user);
			}
		}
		
		if (!$export) {
			echo '<label for="filter">Filtruj:</label> <input type="text" name="filter" value="" id="filter" />';
		}
		echo '<div class="legend">';
		echo '<table><tr><td class="woman">Kobieta</td><td class="man">Mężczyzna</td><td class="additional">Dokwaterowany</td></tr></table>';
		echo '</div><br/>';

		echo '<table class="bordered"><thead><tr>';
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
					$roomNumber = ($connector == 0 ? '' : $connector).$room->getExt();
					$dispRoom = '<a href="'.$this->url(2).'/'.$roomNumber.'/:edit">'.$roomNumber.'</a> <small>('.$room->getLimit().'-os)</small>';
					echo '<tr><td>'.$dispRoom.'</td>';
					$i = 0;
					foreach ($room->getUsers() as $user) {
						$i++;
						if ($user['overLimit']) {
							$class = 'additional';
							$i--;
						} else if ($i > $room->getLimit()) {
							$class = 'additional';
						} else if ($user['sex']==true) {
							$class = 'woman';
						} else {
							$class = 'man';
						}
						echo '<td class="'.$class.'"><a href="'.$this->url(0).'/users/'.$user['id'].'">'.$user['name'].' '.$user['surname'].'</a></td>';
					}
					if ($i < $room->getLimit()) {
						for (; $i < $room->getLimit(); $i++) {
							echo '<td class="free">WOLNE</td>';
						}
					}
					echo '</tr>';
				}
			}
			next($aliases);
		}
		echo '</tbody></table>';

?>
<script type="text/javascript">
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

	public function addRoom($room, $limit = 0, $comment = null) {
		$roomInt = (int)$room;
		if (substr($room, 0, 1) == 'm') {
			$this->rooms[] = new Room($room, $limit, $comment);
		} else if ($roomInt == 0) {
			$this->rooms[] = new Room($room, $limit, $comment);
		} else if (strlen($roomInt) < strlen($room)) {
			$this->rooms[] = new Room(substr($room, strlen($roomInt)), $limit, $comment);
		} else {
			$this->rooms[] = new Room(null, $limit, $comment);
		}
	}

	public function addRoomForListing($room, $comment) {
		$this->addRoom($room, 0, $comment);
	}

	public function addPerson($room, $user) {
		$roomInt = (int)$room;
		if ($roomInt == 0) {
			$roomInt = $room;
		}

		if (substr($room, 0, 1) == 'm') {
			foreach ($this->rooms as $c) {
				if ($c->getExt() == $room) {
					$c->addPerson($user);
					break;
				}
			}
		} else if (strlen($roomInt) < strlen($room)) {
			foreach ($this->rooms as $c) {
				if ($c->getExt() == substr($room, strlen($roomInt))) {
					$c->addPerson($user);
					break;
				}
			}
		} else if (!empty($this->rooms)) {
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
