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
		
		foreach($d as $c){
				echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
				echo ' poprosił o ';
			if($c['type'] == 1){
				echo 'aktywację';
			}elseif($c['type'] == 3){ 
				echo 'dezaktywację';
			}
			echo ' usługi: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
			echo ' <small>dla użytkownika: </small><a href="'.$url.'/users/'.$c['userId'].'">';
			echo $this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
			echo '</li>';
		}
	}
	
	/*
	 * Szablon wyświetlania ostatnio modyfikowanych usług
	 * 
	 */
	public function userServiceLastModified(array $d){
		$url = $this->url(0);
		
		foreach($d as $c){
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			if($c['type'] == 2){
				echo ' aktywował';
			}
			elseif($c['type'] == 4){
				echo ' dezaktywował';
			}
			echo ' usługę: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
			echo ' <small> użytkownikowi: </small><a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "';
			echo $c['login'].'" '.$this->_escape($c['userSurname']).'</a>.';
			echo '</li>';
		}
	}
}
