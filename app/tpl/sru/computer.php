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

	protected $computerSearchTypes = array(
		5 => '',
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
	
/*
	 * Szablon wyświetlania ostatnio modyfikowanych użytkowników
	 * 
	 */
	public function computerLastModified(array $d){
		$url = $this->url(0);
		
		foreach($d as $c){
			echo '<li>';
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedAt']);
			echo '<small> zmodyfikował/dodał komputer: </small><a href="'.$url.'/computers/'.$c['id'].'">';
			echo $this->_escape($c['host']).'</a><small> należący do użytkownika ';
			echo '<a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' "';
			echo $c['login'].'" '.$this->_escape($c['userSurname']).'</a> ';
			echo '</small></li>';
		}
	}
	
	public function listOwn(array $d) {
		$url = $this->url(1).'/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.date(self::TIME_YYMMDD, $c['availableTo']).'</span></li>';
		}
	}

	public function listToActivate(array $d) {
		$url = $this->url(1).'/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'/:activate">'.$c['host'].' <small>'.$c['mac'].'</small></a></li>';
		}
	}

	public function titleDetails(array $d) {
		echo 'Komputer "'.$d['host'].'"';
	}

	public function titleEdit(array $d) {
		echo 'Edycja komputera "'.$d['host'].'"';
	}

	public function titleAliasesEdit(array $d) {
		echo 'Edycja aliasów komputera "'.$d['host'].'"';
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

	public function details(array $d, $switchPort, $aliases) {
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
		echo '<p><em>MAC:</em> ';
		if ($switchPort != null) {
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchId.'/port/'.$switchPort->id.'/macs">'.$d['mac'].'</a> ';
			echo '<small>(<a href="'.$this->url(0).'/switches/'.$switchPort->switchId.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($switchPort->dormitoryAlias, $switchPort->switchNo).'</a>, ';
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchId.'/port/'.$switchPort->id.'">port '.$switchPort->ordinalNo.'</a>)</small>';
		} else {
			echo $d['mac'];
		}
		echo '</p>';
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
		if (!is_null($aliases)) {
			$aliasesString = '<table><tr><td>';
			foreach ($aliases as $alias) {
				$aliasesString = $aliasesString.$alias['host'].'&nbsp;('.($alias['isCname'] ? 'CNAME' : 'A').'), ';
			}
			$aliasesString = substr($aliasesString, 0 , -2);
			$aliasesString = $aliasesString.'</td></tr></table>';
			echo '<p><em>Aliasy:</em> '.$aliasesString.'</p>';
		}
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
		echo '<div id="computerMore">';
		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		echo '</div>';
		echo '<p class="nav"><a href="'.$urlNav.'">Dane</a> &bull; ';
		$acl = $this->_srv->get('acl');
		if ($acl->sruAdmin('penalty', 'addForComputer', $d['id'])) {
			echo '<a href="'.$url.'/penalties/:add/computer:'.$d['id'].'">Ukarz</a> &bull; ';
		}
		echo '<a href="'.$urlNav.'/history">Historia zmian</a> &bull;
			<a href="'.$urlNav.'/:edit">Edycja</a> &bull; ';
		if($d['active'] && $d['typeId'] == 4) {
			echo '<a href="'.$urlNav.'/:aliases"> Aliasy</a> &bull; ';
		}
		if($d['active']) {
			echo '<a href="'.$urlNav.'/:del"> Wyrejestruj</a> &bull; ';
		}
		echo '<span id="computerMoreSwitch"></span>';
		if (strlen($d['comment'])) echo ' <img src="'.UFURL_BASE.'/i/gwiazdka.png" />';
		echo '</p>';
?><script type="text/javascript">
function changeVisibility() {
	var div = document.getElementById('computerMore');
	if (div.sruHidden != true) {
		div.style.display = 'none';
		div.sruHidden = true;
	} else {
		div.style.display = 'block';
		div.sruHidden = false;
	}
}
var container = document.getElementById('computerMoreSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Szczegóły');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function formEdit(array $d, $activate = false) {
		if ($this->_srv->get('msg')->get('computerEdit/errors/ip/noFree')) {
			echo $this->ERR($this->errors['ip/noFree']);
		}
		if ($activate) {
			$d['availableTo'] = date(self::TIME_YYMMDD, $d['availableMaxTo']);
		} else {
			$d['availableTo'] = date(self::TIME_YYMMDD, $d['availableTo']);
		}
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo $form->mac('MAC', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Adres fizyczny karty sieciowej komputera" /> '.$this->showMacHint().'<br/>'));
		echo $form->availableTo('Rejestracja do', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Data, kiedy komputer przestaje być aktywny" /><br/>'));
		echo '<small>Maksymalnie do '.date(self::TIME_YYMMDD, $d['availableMaxTo']).'</small><br />';
	}

	public function formEditAdmin(array $d, $dormitories, $history = null) {
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
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			} else if($temp[1] == '5l')
				$temp[1] = '5Ł';
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($this->computerTypes),
		));
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

	public function formAdd(array $d, $admin = false, $macAddress = null) {
		$post = $this->_srv->get('req')->post;
		$mac = $macAddress;
		try {
			$mac = $post->computerAdd['mac']; //jeśli jest w poście, to przypisz
		} catch (UFex_Core_DataNotFound $e) {
		}
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
		echo $form->host('Nazwa', array('after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Nazwa komputera w sieci - nie musi być zgodna z nazwą w systemie Windows/Linux. Możesz podać inną nazwę niż propozycja SRU." /><br/>'));
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
		echo '<span id="macContainer"><a href="#" id="macMoreSwitch">';
		if (!$this->_srv->get('msg')->get('computerAdd/errors/mac') && $mac != null && $mac == $macAddress) {
			echo 'Rejestruję się z <b>nie swojego</b> komputera<br/><br/>';
		}
		echo '</a></span>';
		echo '<div id="macMore">';
		echo $form->mac('MAC', array('value'=>$mac, 'after'=>' <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Adres fizyczny karty sieciowej komputera" /> '.$this->showMacHint().'<br/>'));
		echo '</div>';

?><script type="text/javascript">
var moreLink = document.getElementById('macMoreSwitch');
var container = document.getElementById('macContainer');
var macField = document.getElementById('computerAdd_mac');
var div = document.getElementById('macMore');
moreLink.onclick = function() {
	div.style.display = 'block';
	container.removeChild(moreLink);
	macField.value = '';
}
<?
if (!$this->_srv->get('msg')->get('computerAdd/errors/mac') && $mac != null && $mac == $macAddress) {
?>
div.style.display = 'none';
<?
}
?>
</script><?
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

	public function formAliasesEdit(array $d, $aliases) {
		$form = UFra::factory('UFlib_Form', 'computerAliasesEdit', $d, $this->errors);
		$url = $this->url(0).'/computers/';
		if (!is_null($aliases)) {
			echo $form->_fieldset('Usuń aliasy komputera:');
			foreach ($aliases as $alias) {
				echo $form->aliasesChk($alias['host'], array('type'=>$form->CHECKBOX, 'name'=>'computerAliasesEdit[aliases]['.$alias['id'].']', 'id'=>'computerAliasesEdit[aliases]['.$alias['id'].']'));
			}
			echo $form->_end();
		}
		echo $form->_fieldset('Dodaj alias:');
		if ($this->_srv->get('msg')->get('computerAliasesEdit/errors/host/duplicated')) {
			echo $this->ERR($this->errors['host/duplicated']);
		}
		if ($this->_srv->get('msg')->get('computerAliasesEdit/errors/host/regexp')) {
			echo $this->ERR($this->errors['host/regexp']);
		}
		echo $form->alias('Alias');
		echo $form->isCname('Wpis CNAME <img src="'.UFURL_BASE.'/i/pytajnik.png" title="Aliasy są domyślnie wpisami A">', array('type'=>$form->CHECKBOX));
		echo $form->_end();
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

		echo $form->typeId('Typ', array('type' => $form->SELECT,'labels' => $form->_labelize($this->computerSearchTypes),));
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
		return '<a href="http://skos.ds.pg.gda.pl/wiki/faq/rejestracja"><small>Skąd wziąć MAC?</small></a>';
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

	public function configDns(array $d, $aliases) {
		foreach ($d as $c) {
			echo $c['host']."\t\tA\t".$c['ip']."\n";
		}
		if (!is_null($aliases)) {
			foreach ($aliases as $alias) {
				if ($alias['isCname']) {
					echo $alias['host']."\t\tCNAME\t".$alias['parent']."\n";
				} else {
					echo $alias['host']."\t\tA\t".$alias['ip']."\n";
				}
			}
		}
	}

	public function configAdmins(array $d) {
		foreach ($d as $c) {
			echo $c['ip']."\n";
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

	public function hostChangedMailTitlePolish(array $d) {
		echo 'Dane Twojego hosta zostały zmienione';
	}

	public function hostChangedMailTitleEnglish(array $d) {
		echo 'Your host data have been changed';
	}

	public function hostChangedMailBodyPolish(array $d, $action) {
		if ($action == UFact_Sru_Computer_Add::PREFIX) {
			echo 'Potwierdzamy, że do Twojego konta w SKOS PG został dodany nowy host.'."\n\n";
		} else if ($action == UFact_Sru_Computer_Edit::PREFIX) {
			echo 'Potwierdzamy, że zmiana danych Twojego hosta w SKOS PG została zapisana.'."\n\n";
		} else {
			echo 'Potwierdzamy dezaktywację Twojego hosta w SKOS PG.'."\n\n";
		}
		echo 'Nazwa hosta: '.$d['host']."\n";
		echo 'Ważny do: '.date(self::TIME_YYMMDD,$d['availableTo'])."\n";
		echo 'IP: '.$d['ip']."\n";
		echo 'Adres MAC: '.$d['mac']."\n";
	}
	
	public function hostChangedMailBodyEnglish(array $d, $action) {
		if ($action == UFact_Sru_Computer_Add::PREFIX) {
			echo 'We confirm, that a new host has been added to your SKOS PG account.'."\n\n";
		} else if ($action == UFact_Sru_Computer_Edit::PREFIX) {
			echo 'We confirm, that change ofyour host data in SKOS PG has been saved.'."\n\n";
		} else {
			echo 'We confirm, that your host in SKOS PG has been deactivated.'."\n\n";
		}
		echo 'Host name: '.$d['host']."\n";
		echo 'Available to: '.date(self::TIME_YYMMDD,$d['availableTo'])."\n";
		echo 'IP: '.$d['ip']."\n";
		echo 'MAC address: '.$d['mac']."\n";
	}

	public function hostAdminChangedMailBodyPolish(array $d, $action, $history = null) {
		if ($action == UFact_SruAdmin_Computer_Add::PREFIX) {
			echo 'Informujemy, że do Twojego konta w SKOS PG dodano nowego hosta.'."\n\n";
		} else if ($action == UFact_SruAdmin_Computer_Edit::PREFIX) {
			echo 'Informujemy, że dane Twojego hosta w SKOS PG uległy zmianie.'."\n\n";
		} else {
			echo 'Informujemy, że Twój host w SKOS PG został dezaktywowany.'."\n\n";
		}

		if ($history instanceof UFbean_SruAdmin_ComputerHistoryList) {
			$history->write('mail', $d);
		} else {
			echo 'Nazwa hosta: '.$d['host']."\n";
			echo 'Ważny do: '.date(self::TIME_YYMMDD,$d['availableTo'])."\n";
			echo 'IP: '.$d['ip']."\n";
			echo 'Adres MAC: '.$d['mac']."\n";
		}
	}

	public function hostAdminChangedMailBodyEnglish(array $d, $action, $history = null) {
		if ($action == UFact_SruAdmin_Computer_Add::PREFIX) {
			echo 'We inform, that a new host has been added to your SKOS PG account.'."\n\n";
		} else if ($action == UFact_SruAdmin_Computer_Edit::PREFIX) {
			echo 'We inform, that data of your host in SKOS PG has been changed.'."\n\n";
		} else {
			echo 'We inform, that your host in SKOS PG has been deactivated.'."\n\n";
		}

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
