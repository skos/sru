<?

/**
* odzyskanie hasła
*/
class UFact_Sru_User_Recover
extends UFact {

	const PREFIX = 'userRecover';

	public function go() {
		try {
			$this->begin();
			$token = UFra::factory('UFbean_Sru_Token');
			$token->getByToken($this->_srv->get('req')->get->userToken);
			$token->del();

			switch ($token->type) {
				case UFbean_Sru_Token::RECOVER:
					$bean = UFra::factory('UFbean_Sru_User');
					$bean->getByPK($token->userId);

					// wygenerowanie hasla
					$password = md5($bean->login.NOW);
					$password = base_convert($password, 16, 35);
					$password = substr($password, 0, 8);
					$bean->password = $bean->generatePassword($bean->login, $password);
					$bean->updateNeeded = true;
					$bean->save();

					$box = UFra::factory('UFbox_Sru');
					$sender = UFra::factory('UFlib_Sender');
					$title = $box->userRecoverPasswordMailTitle($bean);
					$body = $box->userRecoverPasswordMailBodyPassword($bean, $password);
					$sender->send($bean, $title, $body);
					$this->markOk(self::PREFIX.'Password');
					break;
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_NotFound $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, array('token'=>'invalid'));
		} catch (UFex $e) { 
			$this->rollback();
			UFra::error($e);
		}
	}
}