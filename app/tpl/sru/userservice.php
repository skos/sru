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
	public function userServiceLastAdded(array $d){
		$url = $this->url(0);
		
		foreach($d as $c){
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			echo ' dodał usługę: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
			echo ' <small>dla użytkownika: <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
			echo '</small></li>';
		}
	}
	
	/*
	 * Szablon wyświetlania ostatnio modyfikowanych usług
	 * 
	 */
	public function userServiceLastModified(array $d){
		$url = $this->url(0);
		
		foreach($d as $c){
			if($c['type'] == 2){
			echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
				echo ' aktywował usługę: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
				echo ' <small> użytkownikowi: <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>.';
				echo '</small></li>';
			}
			elseif($c['type'] == 3){
			echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']); 
				echo ' poprosił o dezaktywanie usługi: <a href="'.$url.'/services/">'.$this->_escape($c['servName']).'</a>';
				echo ' <small>użytkownikowi: <a href="'.$url.'/users/'.$c['userId'].'">';
				echo $this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
				echo '</small></li>';
			}
			elseif($c['type'] == 4){
			echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
				echo ' dezaktywował usługę: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
				echo ' <small> użytkownikowi: <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>.';
				echo '</small></li>';
			}
			elseif($c['type'] != 1){
				echo '<li>';
				echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
				echo ' zmodyfikował usługę: <a href="'.$url.'/services/">'.$this->_escape($c['servName']) . '</a> ';
				echo ' <small> u użytkownika: <a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "'.$c['login'].'" '.$this->_escape($c['userSurname']).'</a>';
				echo '</small></li>';
			}
		}
	}
}
