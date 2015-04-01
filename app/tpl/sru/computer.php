<?
/**
 * szablon tpl komputera
 */
class UFtpl_Sru_Computer
extends UFtpl_Common {

	protected static $computerTypes = array(
		1 => 'Student - komp / tel',
		2 => 'Student - AP', 
		3 => 'Student - inne',
		11 => 'Turysta',
		21 => 'Organizacja',
		31 => 'Administracja',
		41 => 'Serwer fizyczny',
		42 => 'Serwer wirtualny',
		43 => 'Urządzenie (kamera IP, itd.)',
		44 => 'Interfejs / usługa',
		45 => 'Urządzenie spoza stanu',
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
		43 => 'Urządzenie (kamera IP, itd.)',
		44 => 'Interfejs / usługa',
		45 => 'Urządzenie spoza stanu',
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
		'ip/noFreeAdmin' => 'Nie ma wolnych IP w tym DS-ie/VLAN-ie',
		'ip/notFound' => 'Niedozwolony adres IP',
		'ip/duplicated' => 'IP zajęte',
		'availableTo' => 'Nieprawidłowy format',
		'dormitory' => 'Wybierz akademik',
		'locationAlias' => 'Podaj pokój',
		'locationAlias/noDormitory' => 'Wybierz akademik',
		'locationAlias/noRoom' => 'Pokój nie istnieje',
		'exAdmin/notWithAdmin' => 'Nie można ustawić jednocześnie uprawnień admina i ex-admina',
		'masterHostId/null' => 'Ten typ hosta musi mieć ustawiony serwer nadrzędny',
		'skosCarerId/null' => 'Serwer musi posiadać opiekuna',
		'waletCarerId/null' => 'Host administracji musi posiadać opiekuna',
		'typeId/wrongHostType' => 'Właścicielem tego typu hosta nie może być ten typ użytkownika',
		'deviceModelId' => 'Serwer fizyczny i urządzenie muszą mieć wybrany model',
		'port/duplicated' => 'Wyjątek dla tego portu jest już aktywny',
		'port/regexp' => 'Nieprawidłowy format',
		'comment/textMin' => 'Wprowadź komentarz',
		'validTo/5' => 'Wprowadź poprawną datę',
		'validTo/tooLong' => 'Data nie może przekraczać końca semestru',
		'newExceptions/textMin' => 'Wprowadź porty',
		'purpose/empty' => 'Wybierz cel',
	);

	/**
	 * Szablon wyświetlania ostatnio modyfikowanych użytkowników
	 *
	 */
	public function computerLastModified(array $d){
		$url = $this->url(0);

		echo '<table id="computersModifiedT" class="bordered"><thead><tr>';
		echo '<th>Data</th>';
		echo '<th>Host</th>';
		echo '<th>Właściciel</th>';
		echo '</tr></thead><tbody>';
		foreach($d as $c){
			if ($c['banned'] == true) {
				echo '<tr class="ban">';
			} else {
				echo '<tr>';
			}
			echo '<td>'.date(self::TIME_YYMMDD_HHMM, $c['modifiedat']).'</td>';
			echo '<td>';
			if ($c['active']) {
				echo '<a href="'.$url.'/computers/'.$c['id'].'">'.$this->_escape($c['host']).'</a>';
			} else {
				echo '<del><a href="'.$url.'/computers/'.$c['id'].'">'.$this->_escape($c['host']).'</a></del>';
			}
			echo '</td><td>';
			if($c['u_active'] == true){
				echo '<a href="'.$url.'/users/'.$c['userid'].'">'.$this->_escape($c['name']).' "';
				echo $c['login'].'" '.$this->_escape($c['surname']).'</a>';
			}else{
				echo '<del><a href="'.$url.'/users/'.$c['userid'].'">'.$this->_escape($c['name']).' "';
				echo $c['login'].'" '.$this->_escape($c['surname']).'</a></del>';
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#computersModifiedT").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}

        public function listOwn(array $d){
                $url = $this->url(0) . '/computers/';
                $acl = $this->_srv->get('acl');
                foreach ($d as $c) {
                        echo '<li><span class="userData">' . $c['host'] . '</span><small>&nbsp;&nbsp;IP: ' . $c['ip'] . '&nbsp;&nbsp;MAC: ' . $c['mac'] . '</small>
				<span>' . (is_null($c['availableTo']) ? '' : date(self::TIME_YYMMDD, $c['availableTo'])) . '</span>
				<a class="userAction" href="' . $url . $c['id'] . '">' . _("Szczegóły") . '</a>';
                        if ($acl->sru('computer', 'edit')) {
                                echo ' &bull; <a class="userAction" href="' . $url . $c['id'] . '/:edit">' . _("Edytuj") . '</a></li>';
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

	public function titleFwExceptionsEdit(array $d) {
		echo 'Edycja wyjątków FW komputera "'.$d['host'].'"';
	}
	
        public function detailsOwn(array $d, $user){
                echo '<h1>' . $d['host'] . '.ds.pg.gda.pl</h1>';
                echo '<p><em>' . _("Typ:") . '</em> ' . _(self::$computerTypes[$d['typeId']]) . '</p>';
                echo '<p><em>' . _("MAC:") . '</em> ' . $d['mac'] . '</p>';
                echo '<p><em>' . _("IP:") . '</em> ' . $d['ip'] . '</p>';
                echo '<p><em>' . _("Rejestracja do:") . '</em> ' . (is_null($d['availableTo']) ? _("brak limitu") : date(self::TIME_YYMMDD, $d['availableTo'])) . '</p>';
                echo '<p><em>' . _("Miejsce:") . '</em> ' . $d['locationAlias'] . ' (' . (($user->lang == 'pl') ? $d['dormitoryName'] : $d['dormitoryNameEn']). ')</p>';
                echo '<p><em>' . _("Liczba kar:") . '</em> ' . $d['bans'] . '</p>';
                echo '<p><em>' . _("Widziany:") . '</em> ';
               	$this->ActiveTime($d['lastSeen']);
               }

    private function ActiveTime($lastSeen) {
	if($lastSeen == 0) echo _("nigdy");
        else if((date('U') - $lastSeen) <= 3600) echo _("max godzinę temu");
        else if((date('U') - $lastSeen) <= 18000) echo "max " . (int)((date('U') - $lastSeen)/3600 + 1) . _(" godziny temu");
        else if((date('U') - $lastSeen) < 86400) echo "max " .(int)((date('U') - $lastSeen)/3600 + 1) . _(" godzin temu"); // do doby
        else if((date('U') - $lastSeen) < 172800) echo _("max dzień temu");
        else if((date('U') - $lastSeen) < 604800) echo (int)((date('U') - $lastSeen)/86400) . _(" dni temu"); // do tygodnia
        else if((date('U') - $lastSeen) < 1209600 ) echo _("tydzień temu");
        else if((date('U') - $lastSeen) <= 3024000) echo (int)((date('U') - $lastSeen)/604800) . _(" tygodnie temu"); 
        else if((date('U') - $lastSeen) > 3024000) echo (int)((date('U') - $lastSeen)/604800) . _(" tygodni temu"); 
        echo '</p>';
    }
	
	public function ownUploadChart(array $d){
		echo '<p>'.$this->displayUploadChart($d, true).'</p>';
	}

	public function details(array $d, $switchPort, $aliases, $virtuals, $interfaces, $masterHost, $fwExceptions) {
		$url = $this->url(0);
		$urlNav = $this->url(0).'/computers/'.$d['id'];
		$acl = $this->_srv->get('acl');

		echo '<h1>'.$d['domainName'].'</h1>';
		if (is_null($d['userId'])) {
			$user = 'BRAK';
		} else {
			$user = '<a href="'.$url.'/users/'.$d['userId'].'">'.$this->_escape($d['userName']).' '.$this->_escape($d['userSurname']).'</a>'.(strlen($d['userCommentSkos']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$d['userCommentSkos'].'" />':'');
		}
		echo '<p><em>Typ komputera:</em> '.self::$computerTypes[$d['typeId']].'</p>';
		if ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE) {
			echo '<p><em>Model urządzenia:</em> '.$d['deviceModelName'].'</p>';
		}
		if (!is_null($d['carerName']) && $d['typeId'] != UFbean_Sru_Computer::TYPE_INTERFACE) {
			echo '<p><em>Opiekun:</em> <a href="'.$url.'/admins/'.$d['carerId'].'">'.$d['carerName'].'</a></p>';
		} else if (!is_null($masterHost) && !is_null($masterHost->carerId) && $d['typeId'] == UFbean_Sru_Computer::TYPE_INTERFACE) {
			echo '<p><em>Opiekun:</em> <a href="'.$url.'/admins/'.$masterHost->carerId.'">'.$masterHost->carerName.'</a></p>';
		}
		echo '<p><em>Właściciel:</em> '.$user.'</p>';
		echo '<p><em>MAC:</em> ';
		if ($switchPort != null) {
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'/macs">'.$d['mac'].'</a> ';
			echo '<small>(<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($switchPort->dormitoryAlias, $switchPort->switchNo, $switchPort->switchLab).'</a>, ';
			echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'">port '.$switchPort->ordinalNo.'</a>)</small>';
		} else {
			echo $d['mac'];
		}
		echo ' <small><span id="macvendor">wczytywanie dostawcy MACa...</span></small></p>';
?>
<script>
$("#macvendor").load('<?=UFURL_BASE?>/admin/apis/getmacvendor/<?=$d['mac']?>');
</script>
<?
		echo '<p><em>IP:</em> '.$d['ip'].'</p>';
		echo '<p><em>VLAN:</em> '.$d['vlanName'].' ('.$d['vlanId'].')</p>';
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
		if (!is_null($d['masterHostId']) && $d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT) {
			echo '<p><em>Serwer fizyczny:</em> <a href="'.$url.'/computers/'.$d['masterHostId'].'">'.$d['masterHostDomainName'].'</a></p>';
		}
		if (!is_null($d['masterHostId']) && $d['typeId'] == UFbean_Sru_Computer::TYPE_INTERFACE) {
			echo '<p><em>Serwer nadrzędny:</em> <a href="'.$url.'/computers/'.$d['masterHostId'].'">'.$d['masterHostDomainName'].'</a></p>';
		}
		if (!is_null($virtuals)) {
			$virtualsString = '<table><tr><td>';
			foreach ($virtuals as $virt) {
				$virtualsString = $virtualsString.'<a href="'.$url.'/computers/'.$virt['id'].'">'.$virt['domainName'].'</a>, ';
			}
			$virtualsString = substr($virtualsString, 0 , -2);
			$virtualsString = $virtualsString.'</td></tr></table>';
			echo '<p><em>Maszyny wirtualne:</em> '.$virtualsString.'</p>';
		}
		if (!is_null($interfaces)) {
			$interfacesString = '<table><tr><td>';
			foreach ($interfaces as $inter) {
				$interfacesString = $interfacesString.'<a href="'.$url.'/computers/'.$inter['id'].'">'.$inter['domainName'].'</a>, ';
			}
			$interfacesString = substr($interfacesString, 0 , -2);
			$interfacesString = $interfacesString.'</td></tr></table>';
			echo '<p><em>Interfejsy:</em> '.$interfacesString.'</p>';
		}
		if (!is_null($aliases)) {
			$aliasesString = '<table><tr><td>';
			foreach ($aliases as $alias) {
				$aliasesString = $aliasesString.$alias['domainName'].'&nbsp;('.($alias['isCname'] ? 'CNAME' : 'A').'), ';
			}
			$aliasesString = substr($aliasesString, 0 , -2);
			$aliasesString = $aliasesString.'</td></tr></table>';
			echo '<p><em>Aliasy:</em> '.$aliasesString.'</p>';
		}
		if (!is_null($fwExceptions)) {
			$exceptionsString = '';
			foreach ($fwExceptions as $exception) {
				$exceptionsString = $exceptionsString.($exception['port'] == 0 ? 'wszystkie' : $exception['port']).', ';
			}
			$exceptionsString = substr($exceptionsString, 0 , -2);
			echo '<p><em>Wyjątki FW:</em> '.$exceptionsString.'</p>';
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
		if ($d['typeId'] != UFbean_Sru_Computer::TYPE_SERVER && $d['typeId'] != UFbean_Sru_Computer::TYPE_SERVER_VIRT &&
			$d['typeId'] != UFbean_Sru_Computer::TYPE_MACHINE && $d['typeId'] != UFbean_Sru_Computer::TYPE_INTERFACE &&
			$d['typeId'] != UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) {
			echo '<p><em>Widziany:</em> ' . ($d['lastSeen'] == 0 ? 'nigdy' : date(self::TIME_YYMMDD_HHMM, $d['lastSeen'])).UFlib_Helper::displayHint(_("Ostatni odczyt z DHCP")).'</p>';
			echo '<p><em>Autodezaktywacja:</em> '.($d['autoDeactivation'] ? 'tak' : 'nie').'</p>';
		}
		if (is_null($d['modifiedBy'])) {
			$changed = 'UŻYTKOWNIK';
		} else {
			$changed = '<a href="'.$url.'/admins/'.$d['modifiedById'].'">'.$this->_escape($d['modifiedBy']).'</a>';
		}
		echo '<p><em>Zmiana:</em> '.date(self::TIME_YYMMDD_HHMM, $d['modifiedAt']).'<small> ('.$changed.')</small></p>';
		echo '<div id="computerMore">';
		if (strlen($d['comment'])) {
			echo '<p><em>Komentarz:</em></p><p class="comment">'.nl2br($this->_escape($d['comment'])).'</p>';
		}
		$this->displayUploadChart($d);
		echo '</div>';
		echo '<p class="nav"><a href="'.$urlNav.'">Dane</a> &bull; ';
		if ($acl->sruAdmin('penalty', 'addForComputer', $d['id'])) {
			echo '<a href="'.$url.'/penalties/:add/computer:'.$d['id'].'">Ukarz</a> &bull; ';
		}
		echo '<a href="'.$urlNav.'/history">Historia zmian</a> &bull;
			<a href="'.$urlNav.'/:edit">Edycja</a> &bull; ';
		if ($acl->sruAdmin('computer', 'editAliases')) {
			echo ' <a href="'.$urlNav.'/:aliases">Aliasy</a> &bull; ';
		}
		echo ' <a href="'.$urlNav.'/:fwexceptions">Wyjątki FW</a> &bull; ';
		if ($acl->sruAdmin('computer', 'inventoryCardAdd')) {
			echo ' <a href="'.$urlNav.'/:inventorycardadd">Dodaj kartę wyposażenia</a> &bull; ';
		}
		if($d['active']) {
			echo ' <a href="'.$urlNav.'/:del">Wyrejestruj</a> &bull; ';
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

	private function displayUploadChart($d, $isUser = false) {
		$conf = UFra::shared('UFconf_Sru');
		
		if ($d['vlanId'] == $conf->defaultVlan) {
			$upload = json_decode(file_get_contents($conf->uploadAPIUrl.$d['ip']), true);
			if ($upload != null && $upload != FALSE && $upload != '') {
				if ($isUser) {
					echo '<p>Poniżej znajduje się wykres Twojego uploadu z ostatnich 12 godzin. Upload jest w razie potrzeby automatycznie limitowany przez SKOS. :-)</p>';
				}
				echo '<div id="uploadChart"></div>';
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Godzina', 'Upload (kB/s)'],
			<?
				foreach ($upload as $date => $bytes) {
					echo '["'.substr($date, 11, -3).'",'.str_replace(',', '.',round($bytes/1024/5/60, 2)).'],';
				}
			?>
		]);

		var showEvery = parseInt(data.getNumberOfRows() / 8);
		var options = {
			width: 900,
			height: 563,
			hAxis: {
				title: "Godzina",
				showTextEvery: showEvery
			},
			vAxis: {
				title: "Średni upload z 5 min"
			},
			legend: {
				position:'none'
			}
		};

		var chart = new google.visualization.ColumnChart(
		document.getElementById("uploadChart"));

		chart.draw(data, options);
	}
</script>
<?
			} else if ($isUser) {
				echo '<p>Nie zarejestrowaliśmy uploadu z Twojego hosta w ciągu ostatnich 12 godzin. Upload jest w razie potrzeby automatycznie limitowany przez SKOS. :-)</p>';
			}
		}
	}
	
	public function formEdit(array $d, $activate = false) {
		if ($this->_srv->get('msg')->get('computerEdit/errors/ip/noFree')) {
			echo $this->ERR($this->errors['ip/noFree']);
		}
		if ($this->_srv->get('msg')->get('computerEdit/errors/comp/second')) {
			echo $this->ERR($this->errors['comp/second']);
		}
		if ($this->_srv->get('msg')->get('computerEdit/errors/host/duplicated')) {
			echo $this->ERR(_('Nazwa jest już zajęta. Wpisz inną lub skorzystaj z').' <a href="'.$this->url(0).'/computers/:add">'._('formularza dodawania nowego komputera').'</a>.');
		}
		$form = UFra::factory('UFlib_Form', 'computerEdit', $d, $this->errors);

		echo '<h1>'.$d['host'].'</h1>';
                echo $form->host(_('Nazwa'));
		echo $form->mac('MAC', array('after'=>UFlib_Helper::displayHint(_("Adres fizyczny karty sieciowej komputera.")).$this->showMacHint().'<br/>'));
		if ($this->_srv->get('req')->get->view == 'user/computer/activate') {
			echo $form->activateHost('', array('type'=>$form->HIDDEN, 'value'=>true));
		}
	}

	public function formEditAdmin(array $d, $dormitories, $user = null, $history = null, $servers = null, $skosAdmins = null, $waletAdmins = null, $virtuals = null, $deviceModels = null, $interfaces = null, $interfaceable = null) {
		$post = $this->_srv->get('req')->post;
		$masterHostId = null;
		try {
			if (array_key_exists('masterHostId', $post->computerEdit)) {
				$masterHostId = $post->computerEdit['masterHostId']; //jeśli jest w poście, to przypisz
			}
		} catch (UFex_Core_DataNotFound $e) {
		}
		if (is_array($history)) {
			$d = $history + $d;
		}
		$d['availableTo'] = is_null($d['availableTo']) ? '' : date(self::TIME_YYMMDD, $d['availableTo']);
		$d['dormitory'] = $d['dormitoryId'];
		if ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
			$d['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE || $d['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) {
			$d['skosCarerId'] = $d['carerId'];
		}
		if ($d['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) {
			$d['waletCarerId'] = $d['carerId'];
		}
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
		echo '<div id="location">';
		echo $form->dormitory('Akademik', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize($tmp),
		));
		echo $form->locationAlias('Pokój');
		echo '</div>';
		echo $form->typeId('Typ', array(
			'type' => $form->SELECT,
			'labels' => $form->_labelize(self::$computerTypes),
			'disabled' => ((is_null($virtuals) && is_null($interfaces)) ? false : true),
			'after' => ((is_null($virtuals) && is_null($interfaces)) ? '<br/>' : UFlib_Helper::displayHint("Nie można zmienić typu serwerowi, do którego przypisane są serwery wirtualne lub interfejsy.")),
		));
		if (!is_null($deviceModels)) {
			$tmp = array();
			foreach ($deviceModels as $dm) {
				$tmp[''] = '';
				$tmp[$dm['id']] = $dm['name'];
			}
			echo '<div id="deviceModel">';
			echo $form->deviceModelId('Model', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		if (!is_null($skosAdmins)) {
			$tmp = array();
			foreach ($skosAdmins as $w) {
				$tmp[''] = '';
				$tmp[$w['id']] = $w['name'];
			}
			echo '<div id="skosCarers">';
			echo $form->skosCarerId('Opiekun', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		if (!is_null($waletAdmins)) {
			$tmp = array();
			foreach ($waletAdmins as $w) {
				$tmp[''] = '';
				$tmp[$w['id']] = $w['name'];
			}
			echo '<div id="waletCarers">';
			echo $form->waletCarerId('Opiekun', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		if (!is_null($servers)) {
			$tmp = array();
			echo '<div id="servers">';
			echo $form->masterHostId('Serwer fizyczny/nadrzędny', array(
				'type' => $form->SELECT,
				'labels' => $form->_labelize($tmp),
			));
			echo '</div>';
		}
		echo $form->_fieldset('Uprawnienia');
		echo $form->canAdmin('Komputer administratora '.UFlib_Helper::displayHint("Umożliwia dostęp do części webaplikacji SKOS dla adminów."), array('type'=>$form->CHECKBOX));
		echo $form->exAdmin('Komputer ex-administratora'.UFlib_Helper::displayHint("Umożliwia dostęp do części webaplikacji SKOS dla ex-adminów."), array('type'=>$form->CHECKBOX));
		echo $form->_end();
		echo $form->_fieldset('Inne');
		$conf = UFra::shared('UFconf_Sru');
		echo '<div id="autoDeactivation">';
		echo $form->autoDeactivation('Autodezaktywacja'.UFlib_Helper::displayHint("Komputery, które nie były widziane dłużej niż ".$conf->computersMaxNotSeen." dni, zostaną dezaktywowane. Hosty typu serwerowego nigdy nie są dezaktywowane z powodu braku widzialności."), array('type'=>$form->CHECKBOX));
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

initialSkosCarerId = document.getElementById("computerEdit_skosCarerId").value;
initialWaletCarerId = document.getElementById("computerEdit_waletCarerId").value;
initialDeviceModelId = document.getElementById("computerEdit_deviceModelId").value;
initialTypeId = document.getElementById("computerEdit_typeId").value;
initialAutoDeactivation = document.getElementById("computerEdit_autoDeactivation").checked;
initialDormitory = document.getElementById("computerEdit_dormitory").value;
initialLocationAlias = document.getElementById("computerEdit_locationAlias").value;
(function (){
	form = document.getElementById('computerEdit_typeId');
	function changeVisibility() {
		<? if (!is_null($skosAdmins) && !is_null($waletAdmins)) { ?>
		var skosCarer = document.getElementById("skosCarers");
		var waletCarer = document.getElementById("waletCarers");
		var skosCarerId = document.getElementById("computerEdit_skosCarerId");
		var waletCarerId = document.getElementById("computerEdit_waletCarerId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_MACHINE; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE; ?>) {
			skosCarerId.value = initialSkosCarerId;
			skosCarer.style.display = "block";
			skosCarer.style.visibility = "visible";
			waletCarer.style.display = "none";
			waletCarer.style.visibility = "hidden";
		} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_ADMINISTRATION; ?>) {
			waletCarerId.value = initialWaletCarerId;
			waletCarer.style.display = "block";
			waletCarer.style.visibility = "visible";
			skosCarer.style.display = "none";
			skosCarer.style.visibility = "hidden";
		} else {
			skosCarer.style.display = "none";
			skosCarer.style.visibility = "hidden";
			waletCarer.style.display = "none";
			waletCarer.style.visibility = "hidden";
			skosCarerId.value = '';
			waletCarerId.value = '';
		}
		<? } if (!is_null($servers)) { ?>
		var masterHost = document.getElementById("servers");
		var masterHostId = document.getElementById("computerEdit_masterHostId");
		var dormitory = document.getElementById("computerEdit_dormitory");
		var locationAlias = document.getElementById("computerEdit_locationAlias");
		var location = document.getElementById("location");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_INTERFACE; ?>) {
			var selLength = masterHostId.options.length;
			var optSelected = false;
			for (i = 0; i < selLength; i++) { 
				masterHostId.remove(masterHostId.options[i]);
			}
			if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?>) {
				<?
				foreach ($servers as $serv) {
				if ($serv['id'] == $d['id']) {
					continue;
				}
				?>
				var option = document.createElement("option");
				option.value = <? echo $serv['id'] ?>;
				option.text = "<? echo $serv['host'] ?>";
				if (option.value == "<? echo $masterHostId; ?>" && !optSelected) {
					option.selected = true;
					optSelected = true;
				} else if (option.text == "<? echo $d['masterHostName']; ?>" && !optSelected) {
					option.selected = true;
					optSelected = true;
				}
				masterHostId.add(option);
				<? } ?>
			} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_INTERFACE; ?>) {
				<?
				foreach ($interfaceable as $serv) {
				?>
				var option = document.createElement("option");
				option.value = <? echo $serv['id'] ?>;
				option.text = "<? echo $serv['host'] ?>";
				if (option.value == "<? echo $masterHostId; ?>" && !optSelected) {
					option.selected = true;
					optSelected = true;
				} else if (option.text == "<? echo $d['masterHostName']; ?>" && !optSelected) {
					option.selected = true;
					optSelected = true;
				}
				masterHostId.add(option);
				<? } ?>
			}
			masterHost.style.display = "block";
			masterHost.style.visibility = "visible";
			dormitory.value = initialDormitory;
			locationAlias.value = initialLocationAlias;
			location.style.display = "none";
			location.style.visibility = "hidden";
			
		} else {
			masterHost.style.display = "none";
			masterHost.style.visibility = "hidden";
			masterHostId.value = '';
			location.style.display = "block";
			location.style.visibility = "visible";
		}
		<? } if (!is_null($deviceModels)) { ?>
		var deviceModel = document.getElementById("deviceModel");
		var deviceModelId = document.getElementById("computerEdit_deviceModelId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_MACHINE; ?>) {
			deviceModelId.value = initialDeviceModelId;
			deviceModel.style.display = "block";
			deviceModel.style.visibility = "visible";
		} else {
			deviceModelId.value = '';
			deviceModel.style.display = "none";
			deviceModel.style.visibility = "hidden";
		}
		<? } ?>
		var autoDeactivationDiv = document.getElementById("autoDeactivation");
		var autoDeactivation = document.getElementById("computerEdit_autoDeactivation");
		var typeId = document.getElementById("computerEdit_typeId").value;
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_MACHINE; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_INTERFACE; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE; ?>) {
			autoDeactivationDiv.style.display = "none";
			autoDeactivationDiv.style.visibility = "hidden";
			autoDeactivation.checked = false;
		} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_STUDENT_OTHER; ?>) {
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

	public function formAdd(array $d, $user, $admin = false, $macAddress = null, $servers = null, $skosAdmins = null, $waletAdmins = null, $deviceModels = null, $interfaceable = null) {
		$post = $this->_srv->get('req')->post;
		$mac = $macAddress;
		try {
			$mac = $post->computerAdd['mac']; //jeśli jest w poście, to przypisz
		} catch (UFex_Core_DataNotFound $e) {
		}
		$typeId = null;
		try {
			if (array_key_exists('typeId', $post->computerAdd)) {
				$typeId = $post->computerAdd['typeId']; //jeśli jest w poście, to przypisz
			}
		} catch (UFex_Core_DataNotFound $e) {
		}
		$masterHostId = null;
		try {
			if (array_key_exists('masterHostId', $post->computerAdd)) {
				$masterHostId = $post->computerAdd['masterHostId']; //jeśli jest w poście, to przypisz
			}
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
				'value' => !is_null($typeId) ? $typeId : (array_key_exists($user->typeId, UFbean_Sru_Computer::$defaultUserToComputerType) ? UFbean_Sru_Computer::$defaultUserToComputerType[$user->typeId] : UFbean_Sru_Computer::TYPE_STUDENT),
			));
			if (!is_null($deviceModels)) {
				$tmp = array();
				foreach ($deviceModels as $dm) {
					$tmp[''] = '';
					$tmp[$dm['id']] = $dm['name'];
				}
				echo '<div id="deviceModel">';
				echo $form->deviceModelId('Model', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
			if (!is_null($skosAdmins)) {
				$tmp = array();
				foreach ($skosAdmins as $w) {
					$tmp[''] = '';
					$tmp[$w['id']] = $w['name'];
				}
				echo '<div id="skosCarers">';
				echo $form->skosCarerId('Opiekun', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
			if (!is_null($waletAdmins)) {
				$tmp = array();
				foreach ($waletAdmins as $w) {
					$tmp[''] = '';
					$tmp[$w['id']] = $w['name'];
				}
				echo '<div id="waletCarers">';
				echo $form->waletCarerId('Opiekun', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
			if (!is_null($servers)) {
				$tmp = array();
				echo '<div id="servers">';
				echo $form->masterHostId('Serwer fizyczny / nadrzędny', array(
					'type' => $form->SELECT,
					'labels' => $form->_labelize($tmp),
				));
				echo '</div>';
			}
		}
		echo $form->host(_('Nazwa'), array('after'=>UFlib_Helper::displayHint(_("Nazwa komputera w sieci - nie musi być zgodna z nazwą w systemie Windows/Linux. Możesz podać inną nazwę niż propozycja SRU - jest ona prawie dowolna, ale może zawierać tylko litery, cyfry oraz myślnik."))));
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
		var skosCarer = document.getElementById("skosCarers");
		var waletCarer = document.getElementById("waletCarers");
		var skosCarerId = document.getElementById("computerAdd_skosCarerId");
		var waletCarerId = document.getElementById("computerAdd_waletCarerId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_MACHINE; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE; ?>) {
			waletCarerId.value = '';
			skosCarer.style.display = "block";
			skosCarer.style.visibility = "visible";
			waletCarer.style.display = "none";
			waletCarer.style.visibility = "hidden";
		} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_ADMINISTRATION; ?>) {
			skosCarerId.value = '';
			waletCarer.style.display = "block";
			waletCarer.style.visibility = "visible";
			skosCarer.style.display = "none";
			skosCarer.style.visibility = "hidden";
		} else {
			skosCarer.style.display = "none";
			skosCarer.style.visibility = "hidden";
			waletCarer.style.display = "none";
			waletCarer.style.visibility = "hidden";
			skosCarerId.value = '';
			waletCarerId.value = '';
		}
		<? } if (!is_null($servers)) { ?>
		var masterHost = document.getElementById("servers");
		var masterHostId = document.getElementById("computerAdd_masterHostId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_INTERFACE; ?>) {
			var selLength = masterHostId.options.length;
			for (i = 0; i < selLength; i++) { 
				masterHostId.remove(masterHostId.options[i]);
			}
			if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER_VIRT; ?>) {
				<?
				foreach ($servers as $serv) {
				?>
				var option = document.createElement("option");
				option.value = <? echo $serv['id'] ?>;
				option.text = "<? echo $serv['host'] ?>";
				if (option.value == "<? echo $masterHostId; ?>") {
					option.selected = true;
				}
				masterHostId.add(option);
				<? } ?>
			} else if (form.value == <? echo UFbean_Sru_Computer::TYPE_INTERFACE; ?>) {
				<?
				foreach ($interfaceable as $serv) {
				?>
				var option = document.createElement("option");
				option.value = <? echo $serv['id'] ?>;
				option.text = "<? echo $serv['host'] ?>";
				if (option.value == "<? echo $masterHostId; ?>") {
					option.selected = true;
				}
				masterHostId.add(option);
				<? } ?>
			}
			masterHost.style.display = "block";
			masterHost.style.visibility = "visible";
		} else {
			masterHost.style.display = "none";
			masterHost.style.visibility = "hidden";
			masterHostId.value = '';
		}
		<? } if (!is_null($deviceModels)) { ?>
		var deviceModel = document.getElementById("deviceModel");
		var deviceModelId = document.getElementById("computerAdd_deviceModelId");
		if (form.value == <? echo UFbean_Sru_Computer::TYPE_SERVER; ?> || form.value == <? echo UFbean_Sru_Computer::TYPE_MACHINE; ?>) {
			deviceModel.style.display = "block";
			deviceModel.style.visibility = "visible";
		} else {
			deviceModelId.value = '';
			deviceModel.style.display = "none";
			deviceModel.style.visibility = "hidden";
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
		echo $form->mac('MAC', array('value'=>$mac, 'after'=>''.UFlib_Helper::displayHint(_("Adres fizyczny karty sieciowej komputera.")).$this->showMacHint().'<br/>'));
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

        public function formDel(array $d){
                $d['confirm'] = false;
                $form = UFra::factory('UFlib_Form', 'computerDel', $d);
                echo $form->confirm(_("Tak, chcę wyrejestrować ten komputer"), array('type' => $form->CHECKBOX, 'name' => 'computerDel[confirm]'));
        }

	public function formDelAdmin(array $d) {
		$form = UFra::factory('UFlib_Form', 'computerDel', $d);
		echo $form->confirm('Tak, wyrejestruj ten komputer', array('type'=>$form->CHECKBOX, 'name'=>'computerDel[confirm]', 'value'=>'1'));
	}

        public function formAliasesEdit(array $d, $aliases){
                $form = UFra::factory('UFlib_Form', 'computerAliasesEdit', $d, $this->errors);

                if (!is_null($aliases)) {
                        echo $form->_fieldset(_("Usuń aliasy komputera:"));
                        foreach ($aliases as $alias) {
                                echo $form->aliasesChk($alias['domainName'], array('type' => $form->CHECKBOX, 'name' => 'computerAliasesEdit[aliases][' . $alias['id'] . ']', 'id' => 'computerAliasesEdit[aliases][' . $alias['id'] . ']'));
                        }
                        echo $form->_end();
                }
                echo $form->_fieldset(_("Dodaj alias:"));
                if ($this->_srv->get('msg')->get('computerAliasesEdit/errors/host/duplicated')) {
                        echo $this->ERR($this->errors['host/duplicated']);
                }
                if ($this->_srv->get('msg')->get('computerAliasesEdit/errors/host/regexp')) {
                        echo $this->ERR($this->errors['host/regexp']);
                }
                echo $form->alias(_("Alias (.".$d['domainSuffix'].")"));
                echo $form->isCname(_("Wpis CNAME ") . UFlib_Helper::displayHint(_("Aliasy są domyślnie wpisami CNAME.")), array('type' => $form->CHECKBOX, 'value' => '1'));
                echo $form->_end();
        }
	
	public function formFwExceptionsEdit(array $d, $fwExceptions){
                $form = UFra::factory('UFlib_Form', 'computerFwExceptionsEdit', $d, $this->errors);

		$isAll = false;
                if (!is_null($fwExceptions)) {
                        echo $form->_fieldset(_("Usuń wyjątki FW komputera:"));
                        foreach ($fwExceptions as $exception) {
				if ($exception['port'] == 0) {
					$isAll = true;
				}
                                echo $form->exceptionsChk(($exception['port'] == 0 ? 'wszystkie porty' : 'port '.$exception['port']), array('type' => $form->CHECKBOX, 'name' => 'computerFwExceptionsEdit[exceptions][' . $exception['id'] . ']', 'id' => 'computerFwExceptionsEdit[exceptions][' . $exception['id'] . ']'));
                        }
                        echo $form->_end();
                }
		echo $form->_fieldset(_("Dodaj wyjątki"));
		if ($this->_srv->get('msg')->get('computerFwExceptionsEdit/errors/port/duplicated')) {
			echo $this->ERR($this->errors['port/duplicated']);
		}
		if ($this->_srv->get('msg')->get('computerFwExceptionsEdit/errors/port/regexp')) {
			echo $this->ERR($this->errors['port/regexp']);
		}
		echo $form->newExceptions(_("Wyjątki FW") . UFlib_Helper::displayHint(_("Podaj wyjątki rozdzielone przecinkiem. 0 oznacza wszystkie porty.")), array('disabled' => $isAll, 'after'=>'<span id="fwExceptionsValidationResult"></span><br/>'));
		echo $form->_end();
		
?><script type="text/javascript">
	var exceptions = document.getElementById('computerFwExceptionsEdit_newExceptions');
	function validateFwExceptions() {
		if (exceptions.value == '') {
			document.getElementById('fwExceptionsValidationResult').innerHTML = '';
			return;
		}
		if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else { // code for IE6, IE5
			xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText == 'false') {
					document.getElementById('fwExceptionsValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
				} else {
					document.getElementById('fwExceptionsValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
				}
			}
		}
		xmlhttp.open('GET',"<?=UFURL_BASE?>/api/validator/fwexceptions/" + encodeURIComponent(exceptions.value), true);
		xmlhttp.send();
	}
	exceptions.onchange = validateFwExceptions;
	exceptions.onkeyup = validateFwExceptions;
</script><?
        }
	
	public function formFwExceptionsUserAdd(array $d, $user){
                $form = UFra::factory('UFlib_Form', 'computerFwExceptionsAdd', $d, $this->errors);
		echo $form->_fieldset(_("Dane"));
		echo $form->name(_('Imię'), array('value'=>$user->name, 'disabled'=>true));
		echo $form->name(_('Nazwisko'), array('value'=>$user->surname, 'disabled'=>true));
		echo $form->name(_('Nr indeksu'), array('value'=>$user->registryNo, 'disabled'=>true));
		echo $form->_end();
		
		echo $form->_fieldset(_("Usługi"));
		echo $form->host(_("Zamierzam uruchomić usługi serwerowe na hoście"), array('value'=>$d['host'], 'disabled'=>true));
		echo $form->purpose(_("W celu"), array(
			'type' => $form->RADIO,
			'labels' => $form->_labelize(UFtpl_Sru_FwExceptionApplication::$applicationTypes),
			'labelClass' => 'radio',
			'class' => 'radio',
		));
		echo $form->comment(_("Chcę uruchomić następujące usługi").UFlib_Helper::displayHint(_("Możesz tu wpisać także dodatkowe uzasadnienie.")), array('type'=>$form->TEXTAREA, 'rows'=>5));
		echo $form->validTo(_("Do dnia").UFlib_Helper::displayHint("Maksymalnie koniec semestru, data w formacie RRRR-MM-DD, np. 1988-10-06"), array('type' => $form->CALENDER));
		if ($this->_srv->get('msg')->get('computerFwExceptionsAdd/errors/port/duplicated')) {
			echo $this->ERR($this->errors['port/duplicated']);
		}
		if ($this->_srv->get('msg')->get('computerFwExceptionsAdd/errors/port/regexp')) {
			echo $this->ERR($this->errors['port/regexp']);
		}
		echo $form->newExceptions(_("Porty usług").UFlib_Helper::displayHint(_("Podaj porty usług rozdzielone przecinkiem.")), array('after'=>'<span id="fwExceptionsValidationResult"></span><br/>'));
		echo $form->_end();
		
?><script type="text/javascript">
	var exceptions = document.getElementById('computerFwExceptionsAdd_newExceptions');
	function validateFwExceptions() {
		if (exceptions.value == '') {
			document.getElementById('fwExceptionsValidationResult').innerHTML = '';
			return;
		}
		if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else { // code for IE6, IE5
			xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (xmlhttp.responseText == 'false') {
					document.getElementById('fwExceptionsValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/wykrzyknik.png" alt="Błąd"/>';
				} else {
					document.getElementById('fwExceptionsValidationResult').innerHTML = ' <img src="<?=UFURL_BASE?>/i/img/ok.png" alt="OK"/>';
				}
			}
		}
		xmlhttp.open('GET',"<?=UFURL_BASE?>/api/validator/fwexceptions/" + encodeURIComponent(exceptions.value), true);
		xmlhttp.send();
	}
	exceptions.onchange = validateFwExceptions;
	exceptions.onkeyup = validateFwExceptions;
</script><?
        }

	public function listAdmin(array $d, $id = 0) {
		$url = $this->url(0);
		
		echo '<table id="computersFoundT'.$id.'" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>IP</th>';
		echo '<th>MAC</th>';
		echo '<th>Właściciel</th>';
		echo '<th>Aktywny do</th>';
		echo '</tr></thead><tbody>';
		$inactive = array();
		foreach ($d as $c) {			
			if (is_null($c['userId'])) {
				$owner = 'BRAK';
			} else {
				$owner = '<a href="'.$url.'/users/'.$c['userId'].'">'.$this->_escape($c['userName']).' '.$this->_escape($c['userSurname']).'</a>';
			}
			$toDisplay = '<tr'.($c['banned']?' class="ban"':'').'><td>'.(!$c['active']?'<del>':'').'<a href="'.$url.'/computers/'.$c['id'].'">'.$c['domainName'].(strlen($c['comment']) ? ' <img src="'.UFURL_BASE.'/i/img/gwiazdka.png" alt="" title="'.$c['comment'].'" />':'').(!$c['active']?'</del>':'').'</td>';
			$toDisplay .= '<td>'.$c['ip'].'</td>';
			$toDisplay .= '<td>'.$c['mac'].'</td>';
			$toDisplay .= '<td>'.$owner.'</td>';
			$toDisplay .= '<td>'.(!$c['active']? date(self::TIME_YYMMDD, $c['availableTo']).' ':'').'</td></tr>';
			
			if ($c['active']) {
				echo $toDisplay;
			} else {
				$inactive[$c['availableTo'].$c['id']] = $toDisplay;
			}
		}
		krsort($inactive, SORT_STRING);
		foreach ($inactive as $row) {
			echo $row;
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#computersFoundT<?echo $id;?>").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}

	public function listWalet(array $d) {
		$url = $this->url(0).'/dormitories/';
		
		echo '<table id="interfacesFoundT" class="bordered"><thead><tr>';
		echo '<th>Host</th>';
		echo '<th>IP</th>';
		echo '<th>MAC</th>';
		echo '<th>DS</th>';
		echo '<th>Pomieszczenie</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td>'.$c['host'].'</td>';
			echo '<td>'.$c['ip'].'</td>';
			echo '<td>'.$c['mac'].'</td>';
			echo '<td><a href="'.$url.$c['dormitoryAlias'].'">'.strtoupper($c['dormitoryAlias']).'</a></td>';
			echo '<td>'.$c['locationAlias'].'</td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#interfacesFoundT").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}
	
	public function listInterfaces(array $d) {
		$url = $this->url(0).'/computers/';
		
		echo '<table id="interfacesFoundT" class="bordered"><thead><tr>';
		echo '<th>Interfejs</th>';
		echo '<th>IP</th>';
		echo '<th>MAC</th>';
		echo '<th>Host nadrzędny</th>';
		echo '</tr></thead><tbody>';
		foreach ($d as $c) {
			echo '<tr><td><a href="'.$url.$c['id'].'">'.$c['domainName'].'</td>';
			echo '<td>'.$c['ip'].'</td>';
			echo '<td>'.$c['mac'].'</td>';
			echo '<td><a href="'.$url.$c['masterHostId'].'">'.$c['masterHostDomainName'].'</a></td></tr>';
		}
		echo '</tbody></table>';
		
?>
<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#interfacesFoundT").tablesorter({
            textExtraction:  'complex'
        });
    } 
);
</script>
<?
	}

	public function formSearch(array $d, array $searched) {
		$d = $searched + $d;
		$form = UFra::factory('UFlib_Form', 'computerSearch', $d, $this->errors);
		$cookieDisplay = UFlib_Request::getCookie('SRUDisplayUsers');

		echo $form->typeId('Typ', array('type' => $form->SELECT,'labels' => $form->_labelize($this->computerSearchTypes),));
		echo $form->host('Host');
		echo $form->ip('IP (153.19.)');
		echo $form->mac('MAC');

		$url = explode('/', $this->url());
		if(!in_array('computersActive:1', $url) && in_array('search', $url)) {
			echo $form->computersActive('Tylko aktywne', array(
				'type' => $form->CHECKBOX,
				'checked' => false
			));
		} else if(in_array('computersActive:1', $url) && in_array('search', $url)) {
			echo $form->computersActive('Tylko aktywne', array(
				'type' => $form->CHECKBOX,
				'checked' => true
			));
		}
		else if($cookieDisplay == '1' || $cookieDisplay === false) {
			echo $form->computersActive('Tylko aktywne', array(
				'type' => $form->CHECKBOX,
				'checked' => true,
			));
		} else {
			echo $form->computersActive('Tylko aktywne', array(
				'type' => $form->CHECKBOX,
				'checked' => false,
			));
		}
	}

	public function searchResults(array $d) {
		$this->listAdmin($d);
	}

	public function searchResultsUnregistered(array $d, $switchPort, $searchedMac) {
		echo '<h1>Komputer niezarejestrowany</h1>';
		echo '<p><em>Switch i port:</em> <a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'">'.UFtpl_SruAdmin_Switch::displaySwitchName($switchPort->dormitoryAlias, $switchPort->switchNo, $switchPort->switchLab).'</a>, ';
		echo '<a href="'.$this->url(0).'/switches/'.$switchPort->switchSn.'/port/'.$switchPort->ordinalNo.'">port '.$switchPort->ordinalNo.'</a>';
		echo ' <small><span id="macvendor">wczytywanie dostawcy MACa...</span></small></p>';
?>
<script>
$("#macvendor").load('<?=UFURL_BASE?>/admin/apis/getmacvendor/<?=$searchedMac?>');
</script>
<?
	}

        private function showMacHint(){
                return '<a href="http://faq.ds.pg.gda.pl/"><small>' . _("Skąd wziąć MAC?") . '</small></a>';
        }

	public function configDhcp(array $d) {
		foreach ($d as $c) {
			if ($c['banned']) {
				$c['ip'] = str_replace('153.19.', '172.16.', $c['ip']);
			}
			echo "host\t".$c['host'].'_'.$c['domainSuffix']."\t{ hardware ethernet ".$c['mac'].'; fixed-address '.$c['ip']."; }\n";
		}
	}

	public function configDnsRev(array $d, $mask) {
		foreach ($d as $c) {
			if ($mask == 24) {
				echo substr(strrchr($c['ip'], '.'),1)."\t\tPTR\t".$c['domainName'].".\n";
			} else if ($mask == 16) {
				$parts = explode('.', $c['ip']);
				echo $parts[3].'.'.$parts[2]."\t\tPTR\t".$c['domainName'].".\n";
			}
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

	public function configSkosEthers(array $d) {
		$ethers = array();
		foreach ($d as $c) {
			$ethers[] = array('vlan' => $c['vlanId'], 'mac' => $c['mac'], 'ip' => $c['ip']);
		}
		echo json_encode($ethers);
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
			'0' => '<i>Ostrzeżenie</i>',
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

	public function apiComputersNotSeen(array $d) {
		foreach ($d as $c) {
			echo $c['host']."\n";
		}
	}
	
	public function apiComputersServers(array $d) {
		$servers = array();
		foreach ($d as $c) {
			if ($c['typeId'] != UFbean_Sru_Computer::TYPE_INTERFACE) {
				if (!array_key_exists($c['id'], $servers)) {
					$servers[$c['id']] = array('hostname' => $c['host'], 'category' => self::getComputerType($c['typeId']),
					    'intNo' => 1, 'hypervisor' => $c['masterHostName'], 'model' => $c['deviceModelName']);
					$servers[$c['id']]['ips'] = array();
					$servers[$c['id']]['ips'][]  = array('vlan' => $c['vlanId'], 'mac' => $c['mac'], 'ip' => $c['ip']);
				} else {
					$servers[$c['id']]['hostname'] = $c['host'];
					$servers[$c['id']]['category'] = self::getComputerType($c['typeId']);
					$servers[$c['id']]['hypervisor'] = $c['masterHostName'];
					$servers[$c['id']]['model'] = $c['deviceModelName'];
					$servers[$c['id']]['intNo']++;
					$servers[$c['id']]['ips'][]  = array('vlan' => $c['vlanId'], 'mac' => $c['mac'], 'ip' => $c['ip']);
				}
			} else {
				// nie chcemy interfejsow organziacji
				if ($c['masterHostTypeId'] == UFbean_Sru_Computer::TYPE_ORGANIZATION) {
					continue;
				}
				if (!array_key_exists($c['masterHostId'], $servers)) {
					$servers[$c['masterHostId']] = array('intNo' => 0);
				}
				$servers[$c['masterHostId']]['intNo']++;
				$servers[$c['masterHostId']]['ips'][]  = array('vlan' => $c['vlanId'], 'mac' => $c['mac'], 'ip' => $c['ip']);
			}
		}
		echo json_encode(array_values($servers));
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
		} else if ($action == UFact_SruApi_Computer_Deactivate::PREFIX) {
			echo 'Informujemy, że Twój host w SKOS PG został automatycznie dezaktywowany z powodu długiej nieobecności w sieci lub przekroczenia czasu rejestracji.'."\n";
			echo 'Jeżeli był to Twój jedyny host, możesz go samodzielnie przywrócić z poziomu Twojego konta w SRU, zakładka "Komputery".'."\n\n";
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
			if ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$d['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE || $d['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE || 
				$d['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) {
				echo 'Opiekun: '.$d['carerName']."\n";
			}
			if ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE) {
				echo 'Model '.$d['deviceModelName']."\n";
			}
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
		} else if ($action == UFact_SruApi_Computer_Deactivate::PREFIX) {
			echo 'We inform, that your host in SKOS PG has been deactivated because of a long absence in the network or registration timeout.'."\n";
			echo 'If it was your only host, you can restore it from your account in SRU, tab "Computers".'."\n\n";
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
			if ($d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER || $d['typeId'] == UFbean_Sru_Computer::TYPE_SERVER_VIRT ||
				$d['typeId'] == UFbean_Sru_Computer::TYPE_MACHINE || $d['typeId'] == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE ||
				$d['typeId'] == UFbean_Sru_Computer::TYPE_ADMINISTRATION) {
				echo 'Carer: '.$d['carerName']."\n";
			}
		}
		if (!is_null($admin)) {
			echo 'Admin: '.$admin->name."\n";
		}
	}

	public function hostAliasesChangedMailBody(array $d, array $deleted, $added, $admin) {
		echo 'Zmodyfikowano aliasy hosta: '.$d['host']."\n";
		echo 'IP: '.$d['ip']."\n\n";
		if (!is_null($added)) {
			echo 'Dodano alias: '.$added."\n";
		}
		if (count($deleted) > 0) {
			echo 'Usunięto alias(y): '.implode(', ', $deleted)."\n";
		}
		echo "\n".'Admin modyfikujący: '.$admin->name."\n";
	}
	
	public function hostFwExceptionsChangedMailBody(array $d, array $deleted, $added, $admin) {
		echo 'Zmodyfikowano wyjątki FW hosta: '.$d['host']."\n";
		echo 'IP: '.$d['ip']."\n\n";
		if (count($added) > 0) {
			foreach ($added as $addK => $add) {
				if ($add == 0) {
					$added[$addK] = 'wszystkie porty';
				}
			}
			echo 'Dodano wyjątek(wyjątki): '.implode(', ', $added)."\n";
		}
		if (count($deleted) > 0) {
			foreach ($deleted as $delK => $del) {
				if ($del == 0) {
					$deleted[$delK] = 'wszystkie porty';
				}
			}
			echo 'Usunięto wyjątek(wyjątki): '.implode(', ', $deleted)."\n";
		}
		echo "\n".'Admin modyfikujący: '.$admin->name."\n";
	}
	
	public function hostFwExceptionsChangedMailTitlePolish(array $d) {
		echo 'Dane Twojego hosta zostały zmienione';
	}

	public function hostFwExceptionsChangedMailTitleEnglish(array $d) {
		echo 'Your host data have been changed';
	}
	
	public function hostFwExceptionsChangedMailBodyPolish(array $d, array $deleted, $added) {
		echo 'Zmodyfikowano wyjątki w SKOSfirewallu dla Twojego hosta '.$d['host']."\n\n";
		if (count($added) > 0) {
			foreach ($added as $addK => $add) {
				if ($add == 0) {
					$added[$addK] = 'wszystkie porty';
				}
			}
			echo 'Dodano '.(count($added) > 1 ? 'wyjątki' : 'wyjątek').' dla '.(count($added) > 1 ? 'portów' : 'portu').': '.implode(', ', $added)."\n";
		}
		if (count($deleted) > 0) {
			foreach ($deleted as $delK => $del) {
				if ($del == 0) {
					$deleted[$delK] = 'wszystkie porty';
				}
			}
			echo 'Usunięto '.(count($deleted) > 1 ? 'wyjątki' : 'wyjątek').' dla '.(count($deleted) > 1 ? 'portów' : 'portu').': '.implode(', ', $deleted)."\n";
		}
	}

	public function hostFwExceptionsChangedMailBodyEnglish(array $d, array $deleted, $added) {
		echo 'SKOSfirewall exceptions for your host '.$d['host'].' have been changed.'."\n\n";
		if (count($added) > 0) {
			foreach ($added as $addK => $add) {
				if ($add == 0) {
					$added[$addK] = 'all ports';
				}
			}
			echo 'Added '.(count($added) > 1 ? 'exceptions' : 'exception').' for '.(count($added) > 1 ? 'ports' : 'port').': '.implode(', ', $added)."\n";
		}
		if (count($deleted) > 0) {
			foreach ($deleted as $delK => $del) {
				if ($del == 0) {
					$deleted[$delK] = 'all ports';
				}
			}
			echo 'Removed '.(count($deleted) > 1 ? 'exceptions' : 'exception').' for '.(count($deleted) > 1 ? 'ports' : 'port').': '.implode(', ', $deleted)."\n";
		}
	}

	public function carerChangedToYouMailBody(array $d, $admin) {
		echo 'Zostałeś opiekunem hosta: '.$d['host']."\n";
		echo "\n".'Admin modyfikujący: '.$admin->name."\n";
	}
}
