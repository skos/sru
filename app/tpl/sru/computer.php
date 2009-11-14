<?
/**
 * szablon beana komputera
 */
class UFtpl_Sru_Computer
extends UFtpl_Common {

	protected $computerTypes = array(
		1 => 'Student',
		2 => 'Organizacja',
		3 => 'Administracja',
		4 => 'Serwer',
	);
	
	protected $errors = array(
		'host' => 'Nieprawidłowa nazwa',
		'host/duplicated' => 'Nazwa jest już zajęta',
		'host/textMin' => 'Nazwa jest za krótka',
		'host/textMax' => 'Nazwa jest zbyt długa',
		'host/regexp' => 'Zawiera niedowzolone znaki',
		'mac' => 'Nieprawidłowy format',
		'mac/duplicated' => 'MAC jest już zajęty',
		'ip' => 'Nieprawidłowy format',
		'ip/noFree' => 'Nie ma wolnych IP w tym DS-ie - skontaktuj się ze swoim administratorem lokalnym w godzinach dyżurów',
		'ip/noFreeAdmin' => 'Nie ma wolnych IP w tym DS-ie',
		'ip/notFound' => 'Niedozwolony adres IP',
		'ip/duplicated' => 'IP zajęte',
		'availableTo' => 'Nieprawidłowy format',
		'availableTo/tooNew' => 'Data nie może być większa od maksymalnej daty',
		'availableMaxTo' => 'Nieprawidłowy format',
		'dormitory' => 'Wybierz akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
	);
	
	public function listOwn(array $d) {
		$url = $this->url(1).'/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
		}
	}

	public function titleDetails(array $d) {
		echo 'Komputer "'.$d['host'].'"';
	}

	public function titleEdit(array $d) {
		echo 'Edycja komputera "'.$d['host'].'"';
	}

	public function detailsOwn(array $d) {
		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> <a href="http://stats.ds.pg.gda.pl/?ip='.substr ($d['ip'], 7, 7).'">'.$d['ip'].'</a></p>';
		echo '<p><em>Rejestracja do:</em> '.date(self::TIME_YYMMDD, $d['availableTo']).'</p>';
		echo '<p><em>Miejsce:</em> '.$d['locationAlias'].' ('.$d['dormitoryName'].')</p>';
		echo '<p><em>Liczba kar:</em> '.$d['bans'].'</p>';
		$ip = explode('.', $d['ip']);
		$tag = substr(md5('haha'.$ip[2].$ip[3]), 0, 5);
		//echo '<p><a href="https://sru.ds.pg.gda.pl/lanstats/?ip='.$ip[2].'.'.$ip[3].'"><img src="https://sru.ds.pg.gda.pl/lanstats/153.19.'.$ip[2].'/'.str_pad($ip[3], 3, '0', STR_PAD_LEFT).'.'.$tag.'.png" alt="Statystyki transferów" /></a></p>';
	}

	public function details(array $d) {
		$url = $this->url(0);
		$urlNav = $this->url(0).'/computers/'.$d['id'];
		echo '<h1>'.$d['host'].'</h1>';
		if (is_null($d['userId'])) {
			$user = 'BRAK';
		} else {
			$user = '<a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).'</a>';
		}
		if ($d['typeId'] != 1) {
			echo '<p><em>Typ komputera:</em> '.$this->computerTypes[$d['typeId']].'</p>';
		}
		echo '<p><em>Właściciel:</em> '.$user.'</p>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> <a href="http://stats.ds.pg.gda.pl/?ip='.substr ($d['ip'], 7, 7).'">'.$d['ip'].'</a></p>';
		if (!$d['active']) {
			$max = 'BRAK <small>(było '.date(self::TIME_YYMMDD, $d['availableTo']).')</small>';
		} elseif ($d['availableTo'] != $d['availableMaxTo']) {
			$max = date(self::TIME_YYMMDD, $d['availableTo']).'<small> (max '.date(self::TIME_YYMMDD, $d['availableMaxTo']).')</small>';
		} else {
			$max = date(self::TIME_YYMMDD, $d['availableTo']);
		}
		echo '<p><em>Rejestracja do:</em> '.$max.'</p>';
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small></p>';
		if ($d['banned']) {
			$bans = '<a href="'.$url.'/computers/'.$d['id'].'/penalties">'.$d['bans'].' <strong>(aktywne)</strong></a>';
		} elseif ($d['bans']>0) {
			$bans = '<a href="'.$url.'/computers/'.$d['id'].'/penalties">'.$d['bans'].'</a>';
		} else {
			$bans= '0';
		}
		echo '<p><em>Kary:</em> '.$bans.'</p>';
		$acls = array();
		if ($d['canAdmin']) {
			$acls[] = 'admin';
		}
		if (count($acls)) {
			echo '<p><em>Uprawnienia:</em> '.implode(', ', $acls).'</p>';
		}
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedBy']).'</a>';;
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		if (strlen($d['comment'])) {
			echo '<p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		echo '<p class="nav"><a href="'.$urlNav.'">Dane</a> <a href="'.$url.'/penalties/:add/computer:'.$d['id'].'">Ukarz</a> <a href="'.$urlNav.'/history">Historia zmian</a>  <a href="'.$urlNav.'/:edit">Edycja</a> ';
		if($d['active'])
		{
			echo '<a href="'.$urlNav.'/:del"> Wyrejestruj</a>';
		}
		echo '</p>';
	}

	public function formEdit(array $d) {
		$d['availableTo'] = date(self::TIME_YYMMDD, $d['availableTo']);
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo $form->mac('MAC');
		$this->showMacHint();
		echo $form->availableTo('Rejestracja do');
		echo '<small>Maksymalnie do '.date(self::TIME_YYMMDD, $d['availableMaxTo']).'</small><br />';
	}

	public function formEditAdmin(array $d, $dormitories, $history=null) {
		if (is_array($history)) {
			$d = $history + $d;
		}
		$d['availableMaxTo'] = date(self::TIME_YYMMDD, $d['availableMaxTo']);
		$d['availableTo'] = date(self::TIME_YYMMDD, $d['availableTo']);
		$d['dormitory'] = $d['dormitoryId'];
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo $form->host('Nazwa');
		echo $form->mac('MAC');
		echo $form->ip('IP');
		echo $form->availableTo('Rejestracja do');
		echo $form->availableMaxTo('Rejestracja max do', array('id'=>'availableMaxTo'));
		foreach ($dormitories as $dorm) {
			$tmp[$dorm['id']] = $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo $form->_fieldset('Uprawnienia');
		echo $form->canAdmin('Komputer administratora', array('type'=>$form->CHECKBOX));
		echo $form->_end();
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));

		$conf = UFra::shared('UFconf_Sru');
		$date = $conf->computerAvailableMaxTo;
		?>
<script type="text/javascript">
input = document.getElementById('availableMaxTo');
if (input) {
	button = document.createElement('input');
	button.setAttribute('value', '<?=$date;?>');
	button.setAttribute('type', 'button');
	button.onclick = function() {
		input = document.getElementById('availableMaxTo');
		input.value = this.value;
	}
	input.parentNode.insertBefore(button, input.nextSibling);
	space = document.createTextNode(' ');
	input.parentNode.insertBefore(space, input.nextSibling);
}
input = document.getElementById('computerEdit_ip');
if (input) {
	button = document.createElement('input');
	button.setAttribute('value', 'Pierwsze wolne');
	button.setAttribute('type', 'button');
	button.onclick = function() {
		input = document.getElementById('computerEdit_ip');
		input.value = '';
	}
	input.parentNode.insertBefore(button, input.nextSibling);
	space = document.createTextNode(' ');
	input.parentNode.insertBefore(space, input.nextSibling);
}
</script>
		<?
	}

	public function formAdd(array $d, $admin=false) {
		$form = UFra::factory('UFlib_Form', 'computerAdd', $d, $this->errors);

		if (!$admin && $this->_srv->get('msg')->get('computerAdd/errors/ip/noFree')) {
			echo $this->ERR($this->errors['ip/noFree']);
		}		

		if ($admin) {
			echo $form->typeId('Typ', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($this->computerTypes),
			));
		}
		echo $form->host('Nazwa');
		if ($admin) {
			echo $form->ip('IP');
?><script type="text/javascript">
input = document.getElementById('computerAdd_ip');
if (input) {
	button = document.createElement('input');
	button.setAttribute('value', 'Pierwsze wolne');
	button.setAttribute('type', 'button');
	button.onclick = function() {
		input = document.getElementById('computerAdd_ip');
		input.value = '';
	}
	input.parentNode.insertBefore(button, input.nextSibling);
	space = document.createTextNode(' ');
	input.parentNode.insertBefore(space, input.nextSibling);
}
</script><?
		}
		echo $form->mac('MAC');
		$this->showMacHint();
	}

	public function formDel(array $d) {
		$d['confirm'] = false;
		$form = UFra::factory('UFlib_Form', $d);
		echo $form->confirm('Tak, chcę wyrejestrować ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]'));
	}

	public function formDelAdmin(array $d) {
		$form = UFra::factory('UFlib_Form');
		echo $form->confirm('Tak, wyrejestruj ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]', 'value'=>'1'));
	}

	public function listAdmin(array $d) {
		$url = $this->url(0).'/computers/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
		}
	}

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'computerSearch', $d, $this->errors);

		echo $form->host('Host');
		echo $form->ip('IP (153.19.)');
		echo $form->mac('MAC');
	}

	public function searchResults(array $d) {
		$url = $this->url(0);
		foreach ($d as $c) {
			if (is_null($c['userId'])) {
				$owner = '(BRAK)';
			} else {
				$owner = '(<a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>)';
			}
			echo '<li'.(!$c['active']?' class="old">Do '.date(self::TIME_YYMMDD, $c['availableTo']).' ':'>').(!$c['active']?'<del>':'').'<a href="'.$url.'/computers/'.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.$owner.'</span>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	private function showMacHint() {
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://skos.ds.pg.gda.pl/wiki/faq/rejestracja"><small>Co to jest MAC?</small></a><br/>';
	}

	public function configDhcp(array $d) {
		foreach ($d as $c) {
			if ($c['banned']) {
				$c['ip'] = str_replace('153.19.', '172.16.', $c['ip']);
			}
			echo "host\t".$c['host']."\t{ hardware ethernet ".$c['mac'].'; fixed-address '.$c['ip']."; }\n";
		}
	}

	public function configDnsRev(array $d) {
		$adm = UFbean_Sru_Computer::TYPE_ADMINISTRATION;
		foreach ($d as $c) {
			$host = $c['host'].($c['typeId']==$adm?'.adm':'').'.ds.pg.gda.pl';
			echo substr(strrchr($c['ip'], '.'),1)."\t\tPTR\t".$host.".\n";
		}
	}

	public function configEthers(array $d) {
		foreach ($d as $c) {
			if ($c['banned']) {
				$c['mac'] = '00:00:00:00:00:00';
			}
			echo $c['mac']."\t".$c['ip']."\n";
		}
	}

	public function configDns(array $d) {
		foreach ($d as $c) {
			echo $c['host']."\t\tA\t".$c['ip']."\n";
		}
	}

	public function shortList(array $d) {
		$url = $this->url(0).'/computers/';
		foreach ($d as $c) {
			echo '<li>'.(!$c['active']?'<del>':'').'<a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function penaltyAdd(array $d, array $post) {
		$form = UFra::factory('UFlib_Form', 'penaltyAdd', $post);

		$tmp = array(
			'0' => '<em>Ostrzeżenie</em>',
			'' => '<strong>Wszystkie komputery</strong>',
		);
		foreach ($d as $c) {
			$tmp[$c['id']] = $c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small>';;
		}
		echo $form->computerId('Zakres', array(
			'type' => $form->RADIO,
			'labels' => $form->_labelize($tmp),
			'labelClass' => 'radio',
			'class' => 'radio',
		));		
	
	}

	public function apiComputersLocations(array $d) {
		foreach ($d as $c) {
			if ($c['banned']) {
				$c['ip'] = str_replace('153.19.', '172.16.', $c['ip']);
			}
			echo $c['mac']."\t".$c['ip']."\t".$c['locationAlias']."\n";
		}
	}

	public function apiComputersOutdated(array $d) {
		foreach ($d as $c) {
			echo $c['host']."\n";
		}
	}

	public function mailChange(array $d, $history = null) {
		if ($history instanceof UFbean_SruAdmin_ComputerHistoryList) {
			$history->write('mail', $d);
		} else {
			echo 'Nazwa hosta: '.$d['host']."\n";
			echo 'Ważny do: '.date(self::TIME_YYMMDD,$d['availableTo'])."\n";
			echo 'IP: '.$d['ip']."\n";
			echo 'Adres MAC: '.$d['mac']."\n";
		}
	}

	public function mailChangeEn(array $d, $history = null) {
		if ($history instanceof UFbean_SruAdmin_ComputerHistoryList) {
			$history->write('mailEn', $d);
		} else {
			echo 'Host name: '.$d['host']."\n";
			echo 'Available to: '.date(self::TIME_YYMMDD,$d['availableTo'])."\n";
			echo 'IP: '.$d['ip']."\n";
			echo 'MAC address: '.$d['mac']."\n";
		}
	}
}
