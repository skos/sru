<?

/**
 * dodanie uzytkownika przez administratora Waleta
 */
class UFact_SruWalet_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX, array('password'));
			$bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;

			// tworzymy login
			$user = UFra::factory('UFbean_Sru_User');
			$login = strtolower(substr(UFlib_Helper::removeSpecialChars($bean->name), 0, 2).substr(UFlib_Helper::removeSpecialChars($bean->surname), 0, 3));
			$used = true;
			try {
				$user->getByLogin($login);
			} catch (UFex_Dao_NotFound $e) {
				$used = false;
			}
			if ($used) {
				for($i = 1; $i < 100; $i++) {
					try {
						$user->getByLogin($login.$i);
					} catch (UFex_Dao_NotFound $e) {
						$used = false;
						break;
					}
				}
				$bean->login = $login.$i;
			} else {
				$bean->login = $login;
			}

			// poprawmy imię i nazwisko, aby miało tylko 1 dużą literę
			$bean->name = mb_convert_case($bean->name, MB_CASE_TITLE, "UTF-8");
			$bean->surname = mb_convert_case($bean->surname, MB_CASE_TITLE, "UTF-8");

			$bean->updateNeeded = true;
			$bean->changePasswordNeeded = true;

			$post = $this->_srv->get('req')->post->{self::PREFIX};

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);

			$bean->lastLocationChange = $bean->referralStart;
			if($post['pesel'] == '') {
				$bean->pesel = null;
			}
			
			// zapis narodowości
			if($post['nationalityName'] != '') {
				$country = UFra::factory('UFbean_SruWalet_Country');
				$nationality = mb_convert_case(trim($post['nationalityName']), MB_CASE_LOWER, "UTF-8");
				try {
					$country->getByName($nationality);
					$countryId = $country->id;
				} catch (UFex_Dao_NotFound $e) {
					$country->nationality = $nationality;
					$countryId = $country->save();
				}
			} else {
				$countryId = null;
			}
			$bean->nationality = $countryId;
				
			$id = $bean->save();
			$req = $this->_srv->get('req');
			$req->get->userId = $id;
			$req->get->password = $password;

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
