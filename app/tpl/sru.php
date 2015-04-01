<?
/**
 * szablon modulu sru
 */
class UFtpl_Sru
extends UFtpl_Common {

	public function titleLogin() {
                echo _("Zaloguj się");
        }

	public function login(array $d) {
                echo '<div class="rightColumn">';
                echo '<div class="rightColumnInfo"><h2>' . _("Informacja") . '</h2><p>' . _("Na swoje konto możesz zalogować się dopiero po zameldowaniu Cię przez Biuro DSu.") . '</p></div>';
                echo '</div>';
                echo '<div class="leftColumn">';
                echo '<div id="login">';

                $form = UFra::factory('UFlib_Form');

                echo $form->_start($this->url(0) . '/');
                echo $form->_fieldset(_("Zaloguj się"));

                if ($this->_srv->get('msg')->get('userRecover/errors/token/invalid')) {
                        echo $this->ERR('Token w linku jest nieprawidłowy.');
                } elseif ($this->_srv->get('msg')->get('userLogin/errors')) {
                        echo $this->ERR(_("Nieprawidłowy login lub hasło. Czy aktywowałeś swoje konto w Biurze DS?"));
                }
                echo $d['user']->write('formLogin');
                echo $form->_submit(_("Zaloguj"));
                echo $form->_end();
                echo $form->_end(true);
                if ($this->_srv->get('msg')->get('userLogin/errors')) {
                        UFlib_Script::focus('userLogin_password');
                } else {
                        UFlib_Script::focus('userLogin_login');
                }
                echo '<span id="recoverPasswordSwitch"></span>';
                echo '</div>';
		// left column will continue...
	}

	public function titleMain() {
                echo _("System Rejestracji Użytkowników");
	}

	public function penaltyInfo(array $d) {
		echo '<div class="leftColumn">';
		if (!is_null($d['penalties'])) {
			$form = UFra::factory('UFlib_Form');
			echo $form->_start($this->url(0).'/');
			echo $form->_fieldset(_('Aktywne kary i ostrzeżenia'));
			echo $d['penalties']->write('listPenalty', $d['computers']);
			echo $form->_end();
			echo $form->_end(true);
		}
		// leftColumn will continue...
	}

	public function userInfo(array $d) {
		// leftColumn continues...
                if ($this->_srv->get('msg')->get('userEdit/ok')) {
                        echo $this->OK(_("Dane zostały zmienione. Pamiętaj, aby zaktualizować dane, gdy ponownie ulegną zmianie."));
                }

                $form = UFra::factory('UFlib_Form');

                echo $form->_start($this->url(0) . '/');
                echo $form->_fieldset(_("Twoje dane"));
                echo $d['user']->write('detailsUser');
                echo $form->_end();
                echo $form->_end(true);
		// leftColumn will continue...
	}

	public function hostsInfo(array $d) {
		// leftColumn continues...
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/');
                echo $form->_fieldset(_("Twoje komputery"));
                if (is_null($d['computers'])) {
                        if ($d['inactive'] != null) {
                                echo $this->ERR(_("Nie posiadasz komputerów."));
                                echo '<p>' . _("Przywróć komputer:") . '</p>';
                                $d['inactive']->write('listToActivate');
                                echo '<p><a href="' . $this->url(0) . '/computers/:add">' . _("lub dodaj nowy komputer") . '</a>.</p>';
                        } else {
                                echo $this->ERR(_("Nie posiadasz komputerów.") . ' <a href="' . $this->url(0) . '/computers/:add">' . _("Dodaj komputer") . '</a>.');
                        }
                } else {
                        echo '<ul>';
                        echo $d['computers']->write('listOwn');
                        echo '</ul>';
                }
                echo $form->_end();
                echo $form->_end(true);
		// leftColumn will continue...
	}
	
	public function functionsInfo(array $d) {
		// leftColumn continues...
		if (!is_null($d['functions'])) {
			$form = UFra::factory('UFlib_Form');

			echo $form->_fieldset(_("Twoje funkcje na rzecz DS i Osiedla"));
			echo $d['functions']->write('listOwn');
			echo $form->_end();
		}
		// leftColumn will continue...
	}

	public function contact(array $d) {
		echo '<div class="rightColumn">';
		$form = UFra::factory('UFlib_Form');
		echo $form->_start();

                echo $form->_fieldset(_("Kontakt"));
                if ($this->_srv->get('msg')->get('sendMessage/errors') && !$this->_srv->get('msg')->get('sendMessage/errors/message/notEmpty')) {
                        echo $this->ERR(_("Nie udało się wysłać wiadomości. Prosimy spróbować później lub wysłać wiadomość e-mail na adres admin@ds.pg.gda.pl"));
                }
                if ($this->_srv->get('msg')->get('sendMessage/ok')) {
                        echo $this->OK(_("Wiadomość została wysłana. Odpowiemy na nią najszybciej, jak to będzie możliwe na adres e-mail podany w SRU."));
                } else {
                        echo $d['user']->write('contactForm');
                }
                echo $form->_end();
                echo $form->_end(true);

                echo $form->_fieldset(_("Najbliższe dyżury"));

                if (!is_null($d['dutyHours'])) {
                        echo $d['dutyHours']->write('apiUpcomingDutyHours', 3, null, true, (($d['user']->lang=='pl')?false:true));
                }
                echo '<p><a class="userAction" href="http://dyzury.ds.pg.gda.pl/">' . _("Pełna lista dyżurów") . '</a>';
                echo $form->_end();
                echo $form->_end(true);
		// rightColumn will continue...
	}

	public function importantLinks(array $d) {
		// rightColumn continues...
		$conf = UFra::shared('UFconf_Sru');
		$links = $conf->userImportantLinks;
		if (!empty($links)) {
			$form = UFra::factory('UFlib_Form');
                        echo $form->_fieldset(_('Ważne linki'));
			echo '<ul>';
			foreach ($links as $url => $desc) {
				echo '<li><a href="'.$url.'">'.$desc.'</a>';
			}
			echo '</ul>';
			echo $form->_end();
			echo $form->_end(true);
		}
		// rightColumn ends
		echo '</div>';
	}

	public function userPenalties(array $d) {
		$d['penalties']->write('listAllPenalty', $d['computers']);
	}


	public function titlePenalties() {
		echo _('Archiwum kar i ostrzeżeń');
	}

	public function userPenaltiesNotFound() {
		$form = UFra::factory('UFlib_Form');
		echo $form->_start();
		echo $form->_fieldset(_("Archiwum kar i ostrzeżeń"));
                echo $this->OK(_("Hurra! Brak kar i ostrzeżeń! ;)"));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userMainMenu() {		
		echo '<div id="nav"><ul>';
                echo '<li><a href="' . $this->url(0) . '">' . _("Główna") . '</a></li>';
                echo '<li><a href="' . $this->url(0) . '/profile">' . _("Profil") . '</a></li>';
                echo '<li><a href="' . $this->url(0) . '/computers">' . _("Komputery") . '</a></li>';
                echo '<li><a href="' . $this->url(0) . '/penalties">' . _("Kary") . '</a></li>';
		echo '</ul></div>';
	}

	public function titleError404() {
                echo _('Nie znaleziono strony');
	}

	public function error403() {
                echo $this->ERR(_("Nie masz uprawnień do oglądania tej strony. Wróć do ") . '<a href="' . $this->url(0) . '/" title="' . _("System Rejestracji Użytkowników") . '">SRU</a>.');
        }

	public function titleError403() {
		echo _('Brak uprawnień');
	}

	public function error404() {
                echo $this->ERR(_("Nie znaleziono strony. Wróć do ") . '<a href="' . $this->url(0) . '/" title="' . _("System Rejestracji Użytkowników") . '">SRU</a>.');
	}

	public function titleUserEdit() {
                echo _('Edycja Twoich danych');
	}

	public function userEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
                echo $form->_fieldset(_('Twoje dane'));
                echo $d['user']->write('formEdit', $d['faculties']);
                echo $form->_submit(_('Zapisz'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserComputers() {
		echo _('Twoje komputery');
	}

	public function userComputers(array $d) {
		$form = UFra::factory('UFlib_Form');
		
		echo $form->_start();
                echo $form->_fieldset(_("Twoje komputery"));
                if ($this->_srv->get('msg')->get('computerEdit/ok')) {
                        echo $this->OK(_("Dane zostały zmienione."));
                } else if ($this->_srv->get('msg')->get('computerAdd/ok')) {
                        echo $this->OK(_("Komputer został dodany. Poczekaj cierpliwie, aż sieć zacznie działać. Może to potrwać nawet godzinę."));
                } elseif ($this->_srv->get('msg')->get('computerDel/ok')) {
                        echo $this->OK(_("Komputer został wyrejestrowany."));
                }
		echo '<ul>';
                $d['computers']->write('listOwn');
                echo '</ul>';
                echo '<p><br/>' . _("Samodzielnie możesz dodać tylko jeden komputer. Jeżeli chcesz zarejestrować kolejny, zgłoś się do administratora lokalnego.") . '</p>';
		echo $form->_end();
		echo $form->_end(true);
        }
	
	public function userApplications(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
                echo $form->_fieldset(_("Twoje wnioski o usługi serwerowe"));
                if ($this->_srv->get('msg')->get('computerFwExceptionsAdd/ok')) {
                        echo $this->OK(_("Wniosek został zapisany. Musi teraz zostać zaakceptowany. Poniżej znajdziesz jego aktualny status."));
                }
		echo '<ul>';
                $d['fwApplications']->write('listOwn');
                echo '</ul>';
		echo $form->_end();
	}

	public function userComputersNotFound(array $d) {
                $form = UFra::factory('UFlib_Form');
		
		echo $form->_start();
                echo $form->_fieldset(_("Twoje komputery"));
                if ($this->_srv->get('msg')->get('computerEdit/ok')) {
                        echo $this->OK(_("Dane zostały zmienione"));
                }
                if ($d != null) {
                        echo $this->ERR(_("Nie posiadasz komputerów."));
                        echo '<p>' . _("Przywróć komputer:") . '</p>';
                        $d['computers']->write('listToActivate');
                        echo '<p><a href="' . $this->url(1) . '/:add">' . _("lub dodaj nowy komputer") . '</a>.</p>';
                } else {
                        echo $this->ERR(_("Nie posiadasz komputerów.") . ' <a href="' . $this->url(1) . '/:add">' . _("Dodaj komputer") . '</a>.');
                }
		echo $form->_end();
		echo $form->_end(true);
	}

	public function titleUserComputer(array $d) {
		echo $d['computer']->write('titleDetails');
	}

	public function titleUserComputerNotFound(array $d) {
                echo _("Nie znaleziono komputera");
	}

	public function userComputer(array $d) {
		$acl = $this->_srv->get('acl');
		$form = UFra::factory('UFlib_Form');

		echo $form->_start();
		echo $form->_fieldset(_('Dane hosta'));
		echo '<div class="computer">';
		$d['computer']->write('detailsOwn', $d['user']);
                echo '<p><a class="userAction" href="' . $this->url(1) . '">' . _("Powrót do listy") . '</a>';
                if ($acl->sru('computer', 'edit')) {
                        echo ' &bull; <a class="userAction" href="' . $this->url(2) . '/:edit">' . _("Edytuj") . '</a>';
                }
		echo '</p></div>';
		echo $form->_end();
		echo $form->_end(true);
		$d['computer']->write('ownUploadChart');
	}

	public function userComputerNotFound() {
		echo $this->ERR(_('Nie znaleziono komputera'));
	}

	public function userComputerEdit(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset(_('Zmień dane komputera'));
		echo $d['computer']->write('formEdit', $d['activate']);
		echo $form->_submit(_('Zapisz'));
		echo $form->_end();
		echo $form->_end(true);
		if ($d['activate']) {
                        echo '<p class="nav"><a href="' . $this->url(1) . '">' . _("Powrót") . '</a></p>';
                } else {
                        echo '<p class="nav"><a href="' . $this->url(2) . '">' . _("Powrót") . '</a></p>';
                }
	}

	public function titleUserComputerAdd() {
		echo _('Dodaj komputer');
	}

	public function userComputerAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(0).'/computers/:add');
		echo $form->_fieldset(_('Dodaj komputer'));
		echo $d['computer']->write('formAdd', $d['user'], false, $d['macAddress'], null, null);
		echo $form->_submit(_('Dodaj'));
		echo $form->_end();
		echo $form->_end(true);
	}
	
	public function userComputerFwExceptionsAdd(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo '<h2>'._("Wniosek o pozwolenie na uruchomienie usług serwerowych").'</h2>';
		echo $form->_start($this->url());
		echo $d['computer']->write('formFwExceptionsUserAdd', $d['user']);
		echo $form->_submit(_("Zapisz"));
		echo $form->_end(true);
	}

	public function userComputerDel(array $d) {
		$form = UFra::factory('UFlib_Form');

		echo $form->_start($this->url(3).'/');
		echo $form->_fieldset(_('Wyrejestruj komputer'));
		echo $d['computer']->write('formDel');
		echo $form->_submit(_('Wyrejestruj'));
		echo $form->_end();
		echo $form->_end(true);
	}

	public function userBar(array $d) {
		echo $d['user']->write(__FUNCTION__, $d['lastLoginIp'], $d['lastLoginAt'], $d['lastInvLoginIp'], $d['lastInvLoginAt']);
	}

	public function recoverPassword(array $d) {
		// left column continues...
                if ($this->_isOK('sendPassword')) {
                        echo $this->OK(_('Kliknij link, który został wysłany na maila.'));
                } elseif ($this->_isOK('userRecoverPassword')) {
                        echo $this->OK(_('Nowe hasło zostało wysłane na maila.'));
                } elseif ($this->_srv->get('msg')->get('sendPassword/errors/email/notUnique')) {
                        echo $this->ERR(_('Podany email jest przypisany do kilku kont - proszę zgłosić się do administratora lokalnego w celu zmiany hasła.'));
                } elseif ($this->_isErr('sendPassword')) {
                        echo $this->ERR(_('Nie znaleziono aktywnego konta z podanym mailem. Czy aktywowałeś swoje konto w Biurze DS?'));
                }
		echo '<div id="recoverPassword">';
		$form = UFra::factory('UFlib_Form', 'sendPassword');

		echo $form->_start($this->url(0));
                echo $form->_fieldset(_('Przypomnij login i hasło'));
                echo $form->email(_('E-mail'));
                echo $form->_submit(_('Zmień'));
		echo $form->_end();
		echo $form->_end(true);
		echo '</div></div>';

?><script type="text/javascript">
function changeVisibility() {
	var rpDiv = document.getElementById('recoverPassword');
	var lDiv = document.getElementById('login');
	if (rpDiv.sruHidden !== true) {
		rpDiv.style.display = 'none';
		rpDiv.sruHidden = true;
		lDiv.style.display = 'block';
	} else {
		rpDiv.style.display = 'block';
		rpDiv.sruHidden = false;
		lDiv.style.display = 'none';
	}
}
var container = document.getElementById('recoverPasswordSwitch');
var button = document.createElement('a');
button.onclick = function() {
	changeVisibility();
}
var txt = document.createTextNode('Zapomniałem loginu lub hasła! / I forgot login or password!');
button.appendChild(txt);
container.appendChild(button);
changeVisibility();
</script><?
	}

	public function titleUserUnregistered() {
                echo _("Komputer niezarejestrowany w SKOS PG");
	}

	public function userUnregistered() {
		echo '<h1>Twój komputer jest niezarejestrowany w SKOS PG</h1>
<p>Aby zarejestrować się, musisz zameldować się w biurze/recepcji swojego akademika.</p>
<p>Jeżeli posiadasz konto w  SRU, a lista Twoich komputerów jest pusta, należy dodać komputer. Po zarejestrowaniu komputera należy poczekać nawet godzinę.</p>
<p>Zobacz także: <a href="#more">Więcej informacji</a> &bull; <a href="http://skos.ds.pg.gda.pl">Strona SKOS</a> </p>
<h1>Your computer is not registered in the SKOS PG</h1>
<p>You should visit your dorm office/reception.</p>
<p>If you have an account in SRU, but the list of your computers is empty, you should add your computer. After that you need to wait for 1 hour.</p>
<p>See also: <a href="#more">More info</a> &bull; <a href="http://skos.ds.pg.gda.pl">SKOS web page</a></p>
<p>*SKOS PG - it is a polish acronym for the campus network</p>';
	}
	
	public function userUnregisteredMore() {
		echo '<div style="clear:both"><a id="more" /><img src="'.UFURL_BASE.'/i/img/niezarejestrowani_info.png" alt="Szczegółowa instrukcja podłączenia się do Internetu"></div>';
	}

	public function titleUserBanned() {
                echo _('Komputer ukarany odcięciem od Internetu');
	}

	public function userBanned(array $d) {
                echo '<h1>' . _("Twój komputer został ukarany odcięciem od Internetu") . '</h1>';
                if (is_null($d['penalties'])) {
                        echo '<p>Zaloguj się, aby sprawdzić powód kary.</p>';
                } else if (count($d['penalties']) == 1) {
                        echo _("Twój komputer został odcięty z następującego powodu: ") . $d['penalties'][0]['reason'];
                } else {
                        echo _("Twój komputer został odcięty z następujących powodów:");
                        echo '<ul>';
                        foreach ($d['penalties'] as $penalty) {
                                echo '<li>' . $penalty['reason'] . '</li>';
                        }
                        echo '</ul>';
                }
		
		echo '<p>Możesz skontaktować się z administratorami w <a href="http://dyzury.ds.pg.gda.pl/">godzianch dyżurów</a>.</p>
<p>Zobacz także: <a href="http://skos.ds.pg.gda.pl">Strona SKOS</a> &bull; <a href="http://kary.ds.pg.gda.pl">Polityka kar</a></p>
<h1>Your computer has been punished by cutting off Internet</h1>';
		if (is_null($d['penalties'])) {
			echo '<p>Log in to check the reason of your penalty.</p>';
		} else if (count($d['penalties']) == 1) {
			echo 'Reason for your penalty: '.$d['penalties'][0]['reason'];
		} else {
			echo 'Reason for your penalties:';
			echo '<ul>';
			foreach ($d['penalties'] as $penalty) {
				echo '<li>'.$penalty['reason'].'</li>';
			}
			echo '</ul>';
		}
		echo '<p> You can contact us during <a href="http://dyzury.ds.pg.gda.pl/">our duty hours</a>.</p>
<p>See also: <a href="http://skos.ds.pg.gda.pl">SKOS web page</a> &bull; <a href="http://kary.ds.pg.gda.pl">Penalties politic</a></p>';
	}

	public function userAddByAdminMailTitle(array $d) {
            echo $d['user']->write('userAddByAdminMailTitle');	
	}
	
	public function userAddByAdminMailBody(array $d) {
            echo $d['user']->write('userAddByAdminMailBody', $d['password']);
	}
	
	public function userAddMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userAddMailTitleEnglish');
		} else {
			echo $d['user']->write('userAddMailTitlePolish');
		}
	}

	public function userAddMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userAddMailBodyEnglish', $d['dutyHours']);
		} else {
			echo $d['user']->write('userAddMailBodyPolish', $d['dutyHours']);
		}
	}

	public function userRecoverPasswordMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailTitleEnglish');
		} else {
			echo $d['user']->write('userRecoverPasswordMailTitlePolish');
		}
	}

	public function userRecoverPasswordMailBodyToken(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailBodyTokenEnglish', $d['token'], $d['host']);
		} else {
			echo $d['user']->write('userRecoverPasswordMailBodyTokenPolish', $d['token'], $d['host']);
		}
	}

	public function userRecoverPasswordMailBodyPassword(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('userRecoverPasswordMailBodyPasswordEnglish', $d['password'], $d['host']);
		} else {
			echo $d['user']->write('userRecoverPasswordMailBodyPasswordPolish', $d['password'], $d['host']);
		}
	}
	
	public function penaltyAddMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyAddMailTitleEnglish');
		} else {
			echo $d['penalty']->write('penaltyAddMailTitlePolish', $d['user']);
		}
	}
	
	public function penaltyAddMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyAddMailBodyEnglish', $d['user'], $d['computers'], $d['dutyHours']);
		} else {
			echo $d['penalty']->write('penaltyAddMailBodyPolish', $d['user'], $d['computers'], $d['dutyHours']);
		}
	}

	public function penaltyEditMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyEditMailTitleEnglish');
		} else {
			echo $d['penalty']->write('penaltyEditMailTitlePolish');
		}
	}
	
	public function penaltyEditMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['penalty']->write('penaltyEditMailBodyEnglish', $d['user'], $d['dutyHours']);
		} else {
			echo $d['penalty']->write('penaltyEditMailBodyPolish', $d['user'], $d['dutyHours']);
		}
	}
	
	public function dataChangedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('dataChangedMailTitleEnglish');
		} else {
			echo $d['user']->write('dataChangedMailTitlePolish');
		}
	}
	
	public function dataChangedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['user']->write('dataChangedMailBodyEnglish');
		} else {
			echo $d['user']->write('dataChangedMailBodyPolish');
		}
	}

	public function hostChangedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostChangedMailTitleEnglish');
		} else {
			echo $d['host']->write('hostChangedMailTitlePolish');
		}
	}
	
	public function hostChangedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostChangedMailBodyEnglish', $d['action']);
		} else {
			echo $d['host']->write('hostChangedMailBodyPolish', $d['action']);
		}
	}
	
	public function hostFwExceptionsChangedMailTitle(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostFwExceptionsChangedMailTitleEnglish');
		} else {
			echo $d['host']->write('hostFwExceptionsChangedMailTitlePolish');
		}
	}
	
	public function hostFwExceptionsChangedMailBody(array $d) {
		if ($d['user']->lang == 'en') {
			echo $d['host']->write('hostFwExceptionsChangedMailBodyEnglish', $d['deleted'], $d['added']);
		} else {
			echo $d['host']->write('hostFwExceptionsChangedMailBodyPolish', $d['deleted'], $d['added']);
		}
	}
}
