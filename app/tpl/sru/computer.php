<?
/**
 * szablon tpl komputera
 */
class UFtpl_Sru_Computer
extends UFtpl_Common {

	protected static $computerTypes = array(
		1 => 'Student - komp/tel',
		2 => 'Student - AP',
		3 => 'Student - inne',
		11 => 'Turysta',
		21 => 'Organizacja',
		31 => 'Administracja',
		41 => 'Serwer fizyczny',
		42 => 'Serwer wirtualny',
	);

	protected static $computerTypesForHistory = array(
		0 => 'nieznany',
	);

	public static function getComputerType($typeId) {
		$computerTypes = self::$computerTypes + self::$computerTypesForHistory;
		return $computerTypes[$typeId];
	}

	protected $computerSearchTypes = array(
		0 => '',
		1 => 'Student - komp/tel',
		2 => 'Student - AP',
		3 => 'Student - inne',
		11 => 'Turysta',
		21 => 'Organizacja',
		31 => 'Administracja',
		41 => 'Serwer fizyczny',
		42 => 'Serwer wirtualny',
	);

	static public $userToComputerType = array(
		UFbean_Sru_User::TYPE_TOURIST_INDIVIDUAL => UFbean_Sru_Computer::TYPE_TOURIST,
		UFbean_Sru_User::TYPE_SKOS => UFbean_Sru_Computer::TYPE_SERVER,
		UFbean_Sru_User::TYPE_ADMINISTRATION => UFbean_Sru_Computer::TYPE_ADMINISTRATION,
		UFbean_Sru_User::TYPE_ORGANIZATION => UFbean_Sru_Computer::TYPE_ORGANIZATION,
	);
	
	protected $errors = array(
		'host' => 'Nieprawidłowa nazwa',
		'host/duplicated' => 'Nazwa jest już zajęta',
		'host/textMin' => 'Nazwa jest za krótka',
		'host/textMax' => 'Nazwa jest zbyt długa',
		'host/regexp' => 'Zawiera niedowzolone znaki',
		'comp/second' => 'Samodzielnie możesz dodać tylko jeden komputer. Jeżeli chcesz zarejestrować kolejny, zgłoś się do administratora lokalnego.',
		'mac' => 'Nieprawidłowy format',
		'mac/duplicated' => 'MAC jest już zajęty',
		'ip' => 'Nieprawidłowy format',
		'ip/noFree' => 'Nie ma wolnych IP w tym DS-ie - skontaktuj się ze swoim administratorem lokalnym w godzinach dyżurów',
		'ip/noFreeAdmin' => 'Nie ma wolnych IP w tym DS-ie',
		'ip/notFound' => 'Niedozwolony adres IP',
		'ip/duplicated' => 'IP zajęte',
		'availableTo' => 'Nieprawidłowy format',
		'dormitory' => 'Wybierz akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'exAdmin/notWithAdmin' => 'Nie można ustawić jednocześnie uprawnień admina i ex-admina',
		'masterHostId/null' => 'Maszyna wirtualna musi mieć ustawiony serwer fizyczny',
		'carerId/null' => 'Komputer administracji musi posiadać opiekuna',
	);
	
	/**
	 * Szablon wyświetlania ostatnio modyfikowanych użytkowników
	 * 
	 */
	public function computerLastModified(array $d){
		$url = $this->url(0);

		echo '<ul>';
		foreach($d as $c){;
			if ($c['banned'] == true) {
				echo '<li class="ban">';
			} else {
				echo '<li>';
			}
			echo date(self::TIME_YYMMDD_HHMM, $c['modifiedat']);
			echo '<small> zmodyfikował/dodał komputer: </small><a href="'.$url.'/computers/'.$c['id'].'">';
			echo $this->_escape($c['host']).'</a><small> należący do użytkownika ';
			if($c['active'] == true){
				echo '<a href="'.$url.'/users/'.$c['userid'].'">'.$this->_escape($c['name']).' "';
				echo $c['login'].'" '.$this->_escape($c['surname']).'</a>';
			}else{
				echo '<del><a href="'.$url.'/users/'.$c['userid'].'">'.$this->_escape($c['name']).' "';
				echo $c['login'].'" '.$this->_escape($c['surname']).'</a></del>';
			}
			echo '</small></li>';
		}
		echo '</ul>';
	}
	
	public function listOwn(array $d) {
		$url = $this->url(0).'/computers/';
		$acl = $this->_srv->get('acl');
		foreach ($d as $c) {
			echo '<li><span class="userData">'.$c['host'].'</span><small>&nbsp;&nbsp;IP: '.$c['ip'].'&nbsp;&nbsp;MAC: '.$c['mac'].'</small>
				<span>'.(is_null($c['availableTo']) ? '' : date(self::TIME_YYMMDD, $c['availableTo'])).'</span>
				<a class="userAction" href="'.$url.$c['id'].'">Szczegóły</a>';
				if ($acl->sru('computer', 'edit')) {
					echo ' &bull; <a class="userAction" href="'.$url.$c['id'].'/:edit">Edytuj</a></li>';
				}
		}
	}

	public function listToActivate(array $d) {
		$url = $this->url(0).'/computers/';
		echo '<ul>';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'/:activate">'.$c['host'].' <small>'.$c['mac'].'</small></a></li>';
		}
		echo '</ul>';
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
		echo '<p><em>Typ:</em> '.self::$computerTypes[$d['typeId']].'</p>';
		echo '<p><em>MAC:</em> '.$d['mac'].'</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p><em>Rejestracja do:</em> '.(is_null($d['availableTo']) ? 'brak limitu' : date(self::TIME_YYMMDD, $d['availableTo'])).'</p>';
		echo '<p><em>Miejsce:</em> '.$d['locationAlias'].' ('.$d['dormitoryName'].')</p>';
		echo '<p><em>Liczba kar:</em> '.$d['bans'].'</p>';
		echo '<p><em>Widziany:</em> '.($d['lastSeen'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastSeen'])).'</p>';
	}

	public function details(array $d, $switchPort, $aliases, $virtuals) {
		$url = $this->url(0);
		$urlNav = $this->url(0).'/computers/'.$d['id'];
		echo '<h1>'.$d['host'].'</h1>';
		if (is_null($d['userId'])) {
			$user = 'BRAK';
		} else {
			$user = '<a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).'</a>'.(strlen($d['userComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['userComment'].'" />':'');
		}
		echo '<p><em>Typ komputera:</em> '.self::$computerTypes[$d['typeId']].'</p>';
		if (!is_null($d['carerName'])) {
			echo '<p><em>Opiekun:</em> <a href="'.$url.'/admins/'.$d['carerId'].'">'.$d['carerName'].'</a></p>';
		}
		echo '<p><em>Właściciel:</em> '.$user.'</p>';
		echo '<p><em>MAC:</em> ';
		if ($switchPort != null) {
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'/macs">'.$d['mac'].'</a> ';
			echo '<small>(<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($switchPort->dormitoryAlias, $switchPort->switchNo).'</a>, ';
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'">port '.$switchPort->ordinalNo.'</a>)</small>';
		} else {
			echo $d['mac'];
		}
		echo '</p>';
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		if (!$d['active']) {
			$max = '<strong>BRAK</strong>';
		} else {
			$max = date(self::TIME_YYMMDD, $d['availableTo']);
		}
		if (!$d['active'] || !is_null($d['availableTo'])) {
			echo '<p><em>Rejestracja do:</em> '.$max.'</p>';
		}
		echo '<p><em>Miejsce:</em> <a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'/'.$d['locationAlias'].'">'.$d['locationAlias'].'</a> <small>(<a href="'.$url.'/dormitories/'.$d['dormitoryAlias'].'">'.$d['dormitoryAlias'].'</a>)</small>'.(strlen($d['locationComment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['locationComment'].'" />':'').'</p>';
		if ($d['banned']) {
			$bans = '<a href="'.$url.'/computers/'.$d['id'].'/penalties">'.$d['bans'].' <strong>(aktywne)</strong></a>';
		} elseif ($d['bans']>0) {
			$bans = '<a href="'.$url.'/computers/'.$d['id'].'/penalties">'.$d['bans'].'</a>';
		} else {
			$bans= '0';
		}
		echo '<p><em>Kary:</em> '.$bans.'</p>';
		if (!is_null($d['masterHostName'])) {
			echo '<p><em>Serwer fizyczny:</em> <a href="'.$url.'/computers/'.$d['masterHostId'].'">'.$d['masterHostName'].'</a></p>';
		}
		if (!is_null($virtuals)) {
			$virtualsString = '<table><tr><td>';
			foreach ($virtuals as $virt) {
				$virtualsString = $virtualsString.'<a href="'.$url.'/computers/'.$virt['id'].'">'.$virt['host'].'</a>, ';
			}
			$virtualsString = substr($virtualsString, 0 , -2);
			$virtualsString = $virtualsString.'</td></tr></table>';
			echo '<p><em>Maszyny wirtualne:</em> '.$virtualsString.'</p>';
		}
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
		if ($d['exAdmin']) {
			$acls[] = 'ex-admin';
		}
		if (count($acls)) {
			echo '<p><em>Uprawnienia:</em> '.implode(', ', $acls).'</p>';
		}
		if ($d['typeId'] != UFbean_Sru_Computer::TYPE_SERVER && $d['typeId'] != UFbean_Sru_Computer::TYPE_SERVER_VIRT) {
			echo '<p><em>Widziany:</em> '.($d['lastSeen'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastSeen'])).'</p>';
			echo '<p><em>Autodezakt.:</em> '.($d['autoDeactivation'] ? 'tak' : 'nie').'</p>';
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
		if($d['active'] && ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT)) {
			echo '<a href="'.$urlNav.'/:aliases"> Aliasy</a> &bull; ';
		}
		echo '<a href="'.$urlNav.'/stats"> Statystyki transferu</a> &bull; ';
		if($d['active']) {
			echo '<a href="'.$urlNav.'/:del"> Wyrejestruj</a> &bull; ';
		}
		echo '<span id="computerMoreSwitch"></span>';
		if (strlen($d['comment'])) echo ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['comment'].'" />';
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
		if ($this->_srv->get('msg')->get('computerEdit/errors/comp/second')) {
			echo $this->ERR($this->errors['comp/second']);
		}
		if ($this->_srv->get('msg')->get('computerEdit/errors/host/duplicated')) {
			echo $this->ERR('Nazwa jest już zajęta. Skorzystaj z <a href="'.$this->url(0).'/computers/:add">formularza dodawania nowego komputera</a>.');
		}
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'.ds.pg.gda.pl</h1>';
		echo $form->mac('MAC', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" alt="?" title="Adres fizyczny karty sieciowej komputera." /> '.$this->showMacHint().'<br/>'));
	}

	public function formEditAdmin(array $d, $dormitories, $user = null, $history = null, $servers = null, $waletAdmins = null, $virtuals = null) {
		if (is_array($history)) {
			$d = $history + $d;
		}
		$d['availableTo'] = is_null($d['availableTo']) ? '' : date(self::TIME_YYMMDD, $d['availableTo']);
		$d['dormitory'] = $d['dormitoryId'];
		if (!$d['active'] && !is_null($user)) {
			$d['dormitory'] = $user->dormitoryId;
			$d['locationAlias'] = $user->locationAlias;
		}
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo $form->host('Nazwa');
		echo $form->mac('MAC');
		if (!$d['active'] && !is_null($user)) {
			echo $form->activateHost('Aktywuj', array('type'=>$form->CHECKBOX));
		}
		echo $form->ip('IP');
		echo $form->availableTo('Rejestracja do');
		foreach ($dormitories as $dorm) {
			$temp = explode("ds", $dorm['alias']);
			if (!isset($temp[1])) {
				$temp[1] = $dorm['alias'];
			}
			$tmp[$dorm['id']] = $temp[1] . ' ' . $dorm['name'];
		}
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$computerTypes),
			'disabled' => ((is_null($virtuals)) ? false : true),
			'after' => ((is_null($virtuals)) ? '<br/>' : ' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Nie można zmienić typu serwerowi, do którego przypisane są serwery wirtualne."/><br/>'),
		));
		if (!is_null($waletAdmins)) {
			$tmp = array();
			foreach ($waletAdmins as $w) {
				$tmp[''] = '';
				$tmp[$w['id']] = $w['name'];
			}
			echo '<div id="carers">';
			echo $form->carerId('Opiekun', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		if (!is_null($servers)) {
			$tmp = array();
			foreach ($servers as $serv) {
				if ($serv['id'] == $d['id']) {
					continue;
				}
				$tmp[''] = '';
				$tmp[$serv['id']] = $serv['host'];
			}
			echo '<div id="servers">';
			echo $form->masterHostId('Serwer fizyczny', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		echo $form->_fieldset('Uprawnienia');
		echo $form->canAdmin('Komputer administratora <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Oznacza komputer na liście uploaderów oraz umozliwia dostęp do części webaplikacji SKOS."/>', array('type'=>$form->CHECKBOX));
		echo $form->exAdmin('Komputer ex-administratora <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Oznacza komputer na liście uploaderów."/>', array('type'=>$form->CHECKBOX));
		echo $form->_end();
		echo $form->_fieldset('Inne');
		$conf = UFra::shared('UFconf_Sru');
		echo '<div id="autoDeactivation">';
		echo $form->autoDeactivation('Autodezaktywacja <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Komputery, które nie były widziane dłużej niż '.$conf->computersMaxNotSeen.' dni, zostaną dezaktywowane. Hosty typu serwerowego nigdy nie są dezaktywowane z powodu braku widzialności."/>', array('type'=>$form->CHECKBOX));
		echo '<br/></div>';
		echo $form->comment('Komentarz', array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->_end();
		?>
<script type="text/javascript">
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

initialCarerId = document.getElementById("computerEdit_carerId").value;
initialMasterHostId = document.getElementById("computerEdit_masterHostId").value;
initialTypeId = document.getElementById("computerEdit_typeId").value;
initialAutoDeactivation = document.getElementById("computerEdit_autoDeactivation").checked;
(function (){
	form = document.getElementById('computerEdit_typeId');
	function changeVisibility() {
		<? if (!is_null($waletAdmins)) { ?>
		var carer = document.getElementById("carers");
		var carerId = document.getElementById("computerEdit_carerId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_ADMINISTRATION; ?>) {
			carerId.value = initialCarerId;
			carer.style.display = "block";
			carer.style.visibility = "visible";
		} else {
			carer.style.display = "none";
			carer.style.visibility = "hidden";
			carerId.value = '';
		}
		<? } if (!is_null($servers)) { ?>
		var masterHost = document.getElementById("servers");
		var masterHostId = document.getElementById("computerEdit_masterHostId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?>) {
			masterHostId.value = initialMasterHostId;
			masterHost.style.display = "block";
			masterHost.style.visibility = "visible";
		} else {
			masterHost.style.display = "none";
			masterHost.style.visibility = "hidden";
			masterHostId.value = '';
		}
		<? } ?>
		var autoDeactivationDiv = document.getElementById("autoDeactivation");
		var autoDeactivation = document.getElementById("computerEdit_autoDeactivation");
		var typeId = document.getElementById("computerEdit_typeId").value;
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?>) {
			autoDeactivationDiv.style.display = "none";
			autoDeactivationDiv.style.visibility = "hidden";
			autoDeactivation.checked = false;
		} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_STUDENT_AP; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_STUDENT_OTHER; ?>) {
			autoDeactivationDiv.style.display = "block";
			autoDeactivationDiv.style.visibility = "visible";
			if (typeId != initialTypeId) {
				autoDeactivation.checked = false;
			} else {
				autoDeactivation.checked = initialAutoDeactivation;
			}
		} else {
			autoDeactivationDiv.style.display = "block";
			autoDeactivationDiv.style.visibility = "visible";
			if (typeId != initialTypeId) {
				autoDeactivation.checked = true;
			} else {
				autoDeactivation.checked = initialAutoDeactivation;
			}
		}
	}
	form.onchange = changeVisibility;
	changeVisibility();
})()
<?
if (!$d['active'] && !is_null($user)) {
?>
var activateChkB = document.getElementById('computerEdit_activateHost');
var vailableTo = document.getElementById('computerEdit_availableTo');
var vailableToVal = vailableTo.value;
var ip = document.getElementById('computerEdit_ip');
var ipVal = ip.value;
activateChkB.onclick = function() {
	if (activateChkB.checked) {
		vailableTo.value = '';
		ip.value = '';
	} else {
		vailableTo.value = vailableToVal;
		ip.value = ipVal;
	}
}
<?
}
?>
</script>
		<?
	}

	public function formAdd(array $d, $user, $admin = false, $macAddress = null, $servers = null, $waletAdmins = null) {
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
		if ($this->_srv->get('msg')->get('computerAdd/errors/comp/second')) {
			echo $this->ERR($this->errors['comp/second']);
		}
		if ($admin) {
			echo $form->typeId('Typ', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize(self::$computerTypes),
				'value' => (array_key_exists($user->typeId, self::$userToComputerType) ? self::$userToComputerType[$user->typeId] : UFbean_Sru_Computer::TYPE_STUDENT),
			));
			if (!is_null($waletAdmins)) {
				$tmp = array();
				foreach ($waletAdmins as $w) {
					$tmp[''] = '';
					$tmp[$w['id']] = $w['name'];
				}
				echo '<div id="carers">';
				echo $form->carerId('Opiekun', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
			if (!is_null($servers)) {
				$tmp = array();
				foreach ($servers as $serv) {
					$tmp[''] = '';
					$tmp[$serv['id']] = $serv['host'];
				}
				echo '<div id="servers">';
				echo $form->masterHostId('Serwer fizyczny', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
		}
		echo $form->host('Nazwa', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Nazwa komputera w sieci - nie musi być zgodna z nazwą w systemie Windows/Linux. Możesz podać inną nazwę niż propozycja SRU - jest ona prawie dowolna, ale może zawierać tylko litery, cyfry oraz myślnik." /><br/>'));
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

(function (){
	form = document.getElementById('computerAdd_typeId');
	function changeVisibility() {
		<? if (!is_null($waletAdmins)) { ?>
		var carer = document.getElementById("carers");
		var carerId = document.getElementById("computerAdd_carerId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_ADMINISTRATION; ?>) {
			carer.style.display = "block";
			carer.style.visibility = "visible";
		} else {
			carer.style.display = "none";
			carer.style.visibility = "hidden";
			carerId.value = '';
		}
		<? } if (!is_null($servers)) { ?>
		var masterHost = document.getElementById("servers");
		var masterHostId = document.getElementById("computerAdd_masterHostId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?>) {
			masterHost.style.display = "block";
			masterHost.style.visibility = "visible";
		} else {
			masterHost.style.display = "none";
			masterHost.style.visibility = "hidden";
			masterHostId.value = '';
		}
		<? } ?>
	}
	form.onchange = changeVisibility;
	changeVisibility();
})()
</script><?
		}
		echo '<span id="macContainer"><a href="#" id="macMoreSwitch">';
		if (!$this->_srv->get('msg')->get('computerAdd/errors/mac') && $mac != null && $mac == $macAddress) {
			echo 'Rejestruję się z <b>nie swojego</b> komputera<br/><br/>';
		}
		echo '</a></span>';
		echo '<div id="macMore">';
		echo $form->mac('MAC', array('value'=>$mac, 'after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Adres fizyczny karty sieciowej komputera." /> '.$this->showMacHint().'<br/>'));
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
?></script><?
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
		echo $form->isCname('Wpis CNAME <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" title="Aliasy są domyślnie wpisami CNAME.">', array('type'=>$form->CHECKBOX, 'value'=>'1'));
		echo $form->_end();
	}

	public function listAdmin(array $d) {
		$url = $this->url(0).'/computers/';
		foreach ($d as $c) {
			echo '<li><a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span><small>('.self::$computerTypes[$c['typeId']].')</small> '.(is_null($c['availableTo']) ? '' : date(self::TIME_YYMMDD, $c['availableTo'])).'</span>'.(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').'</li>';
		}
	}

	public function listWalet(array $d) {
		$url = $this->url(0).'/dormitories/';
		foreach ($d as $c) {
			echo '<li>'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a>, <span>lokalizacja: '.$c['locationAlias'].' (<a href="'.$url.$c['dormitoryAlias'].'">'.strtoupper($c['dormitoryAlias']).'</a>)</span></li>';
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
			echo '<li'.($c['banned'] ? ' class="ban"' : '').'>'.(!$c['active']? 'Do '.date(self::TIME_YYMMDD, $c['availableTo']).' ':'').(!$c['active']?'<del>':'').'<a href="'.$url.'/computers/'.$c['id'].'">'.$c['host'].(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span>'.$owner.'</span>'.(!$c['active']?'</del>':'').'</li>';
		}
	}

	public function searchResultsUnregistered(array $d, $switchPort) {
		$url = $this->url(0);
		echo '<h1>Komputer niezarejestrowany</h1>';
		echo '<p><em>Switch i port:</em> <a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($switchPort->dormitoryAlias, $switchPort->switchNo).'</a>, ';
		echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'">port '.$switchPort->ordinalNo.'</a></p>';
	}

	private function showMacHint() {
		return '<a href="http://faq.ds.pg.gda.pl/"><small>Skąd wziąć MAC?</small></a>';
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
			echo '<li'.($c['banned']?' class="ban"' : '').'>'.(!$c['active']?'<del>':'').'<a href="'.$url.$c['id'].'">'.$c['host'].' <small>'.$c['ip'].'/'.$c['mac'].'</small></a> <span><small>('.self::$computerTypes[$c['typeId']].')</small> '.(is_null($c['availableTo']) ? '' : date(self::TIME_YYMMDD, $c['availableTo'])).'</span>'.(!$c['active']?'</del>':'').(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').'</li>';
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

	public function transferStats(array $d, $file, $hour, $date) {
		$form = UFra::factory('UFlib_Form', 'transferStats');
		echo $form->_start();
		echo $form->_fieldset();
		echo $form->statHour('Godzina (HH:MM)', array('value'=>$hour));
		echo $form->statDate('Data (YYYYMMDD)', array('value'=>$date));
		echo $form->_submit('Zobacz', array('after'=>' <img src="'.UFURL_BASE.'/i/img/pytajnik.png" alt="?" alt="?" title="Podgląd danych z przeszłości, gdzie data jest końcem wyświetlanych okresów. Uwaga, im starsze dane, tym mniej precyzyjne!" />'));
		echo $form->_end();
		echo $form->_end(true);

		echo '<h3>Upload przez ostatnie 3 godziny</h3>';
		echo '<p><img src="'.UFURL_BASE.'/i/stats-img/'.$file.'.png" alt="Statystyki transferów: 3h" /></p>';
		echo '<h3>Upload przez ostatnie 24h godziny</h3>';
		echo '<p><img src="'.UFURL_BASE.'/i/stats-img/'.$file.'.day.png" alt="Statystyki transferów: 24h" /></p>';
		echo '<h3>Upload przez ostatni tydzień</h3>';
		echo '<p><img src="'.UFURL_BASE.'/i/stats-img/'.$file.'.week.png" alt="Statystyki transferów: tydzień" /></p>';
		echo '<h3>Upload przez ostatni miesiąc</h3>';
		echo '<p><img src="'.UFURL_BASE.'/i/stats-img/'.$file.'.month.png" alt="Statystyki transferów: miesiąc" /></p>';
		echo '<h3>Upload przez ostatni rok</h3>';
		echo '<p><img src="'.UFURL_BASE.'/i/stats-img/'.$file.'.year.png" alt="Statystyki transferów: rok" /></p>';
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
		echo 'Ważny do: '.(is_null($d['availableTo']) ? 'brak limitu' : date(self::TIME_YYMMDD,$d['availableTo']))."\n";
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
		echo 'Available to: '.(is_null($d['availableTo']) ? 'no limit' : date(self::TIME_YYMMDD,$d['availableTo']))."\n";
		echo 'IP: '.$d['ip']."\n";
		echo 'MAC address: '.$d['mac']."\n";
	}

	public function hostAdminChangedMailBodyPolish(array $d, $action, $history = null, $admin = null) {
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
			echo 'Ważny do: '.(is_null($d['availableTo']) ? 'brak limitu' : date(self::TIME_YYMMDD,$d['availableTo']))."\n";
			echo 'IP: '.$d['ip']."\n";
			echo 'Adres MAC: '.$d['mac']."\n";
		}
		if (!is_null($admin)) {
			echo 'Admin modyfikujący: '.$admin->name."\n";
		}
	}

	public function hostAdminChangedMailBodyEnglish(array $d, $action, $history = null, $admin = null) {
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
			echo 'Available to: '.(is_null($d['availableTo']) ? 'no limit' : date(self::TIME_YYMMDD,$d['availableTo']))."\n";
			echo 'IP: '.$d['ip']."\n";
			echo 'MAC address: '.$d['mac']."\n";
		}
		if (!is_null($admin)) {
			echo 'Admin: '.$admin->name."\n";
		}
	}

	public function hostAliasesChangedMailBody(array $d, array $deleted, $added, $admin) {
		echo 'Zmodyfikowano aliasy hosta: '.$d['host']."\n\n";
		if (!is_null($added)) {
			echo 'Dodano alias: '.$added."\n";
		}
		if (count($deleted) > 0) {
			echo 'Usunięto alias(y): '.implode(', ', $deleted)."\n";
		}
		echo "\n".'Admin modyfikujący: '.$admin->name."\n";
	}
}
