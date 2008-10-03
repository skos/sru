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

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);

			$id = $bean->save();
			$req = $this->_srv->get('req');

			$token = UFra::factory('UFbean_Sru_Token');
			$token->token = md5($id.NOW);
			$token->userId = $id;
			$token->save();

			// wyslanie maila
			$box = UFra::factory('UFbox_Sru');
			$title = $box->userAddMailTitle($bean);
			$body = $box->userAddMailBody($bean, $password, $token);
			$headers = $box->userAddMailHeaders($bean);
			mail($bean->email, $title, $body, $headers);

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
