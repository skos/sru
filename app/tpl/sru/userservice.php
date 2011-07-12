<?
/**
 * szablon beana usługi
 */
class UFtpl_Sru_UserService
extends UFtpl_Common {	

	/*
	 * Szablon wyświetlania ostatnio dodanych usług
	 * 
	 */
	public function userServiceLastRequested(array $d){
		$url = $this->url(0);

		echo '<ul>';
		foreach($d as $c){
				echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
				echo '<small> poprosił o ';
			if($c['type'] == 1){
				echo 'aktywację';
			}elseif($c['type'] == 3){ 
				echo 'dezaktywację';
			}
			echo ' usługi: </small><a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
			echo ' <small> użytkownika: </small>';
			if($c['userActive'] == true){
				echo '<a href="'.$url.'/users/'.$c['userId'].'">';
				echo $this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
			}else{
				echo '<del><a href="'.$url.'/users/'.$c['userId'].'">';
				echo $this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a></del>';
			}
			echo '</li>';
		}
		echo '<ul>';
	}
	
	/*
	 * Szablon wyświetlania ostatnio modyfikowanych usług
	 * 
	 */
	public function userServiceLastModified(array $d){
		$url = $this->url(0);

		echo '<ul>';
		foreach($d as $c){
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']) . '<small>';
			if($c['type'] == 2){
				echo ' aktywował';
			}
			elseif($c['type'] == 4){
				echo ' dezaktywował';
			}
			echo ' usługę: </small><a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
			echo ' <small> użytkownika: </small>';
			if($c['userActive'] == true){
				echo '<a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "';
				echo $c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
			}else{
				echo '<del><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "';
				echo $c['login'].'" '.$this->_escape($c['userSurname']).'</a></del>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}
}
