<?

/**
 * dodanie uzytkownika
 */
class UFact_Sru_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX);
			$bean->active = false;
			$bean->gg = '';

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);


			// sprawdzenie w bazie osiedla
			$walet = UFra::factory('UFbean_Sru_User');
			$conf = UFra::shared('UFconf_Sru');
			if ($conf->checkWalet) {
				// sprawdzenie w bazie osiedla
				$walet = UFra::factory('UFbean_Sru_User');
				try {
					$walet->getFromWalet($bean->name, $bean->surname, $bean->locationAlias, $bean->dormitory);
				} catch (UFex_Dao_NotFound $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'User not in Walet database', 0, E_WARNING,  array('walet' => 'notFound'));
				}
			}

			$id = $bean->save();
			$bean->getByPK($id);	// uzupelnione dane dociagane z innych tabel
			$req = $this->_srv->get('req');

			// wyslanie maila
			$box = UFra::factory('UFbox_Sru');
			$title = $box->userAddMailTitle($bean);
			try {
				$oldUser = UFra::factory('UFbean_Sru_User');
				$oldUser->getOldByEmail($bean->email);

				$token = UFra::factory('UFbean_Sru_Token');
				$token->token = md5($id.NOW);
				$token->userId = $id;
				$token->save();

				$body = $box->userAddMailBodyToken($bean, $password, $token);
			} catch (UFex_Dao_NotFound $e) {
				$body = $box->userAddMailBodyNoToken($bean, $password);
			}
			$headers = $box->userAddMailHeaders($bean);
			mail($bean->email, $title, $body, $headers);

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
