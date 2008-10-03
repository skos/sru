<?

/**
 * aktywacja konta uzytkownika i jego zalogowanie
 */
class UFact_Sru_User_Confirm
extends UFact {

	const PREFIX = 'userConfirm';

	public function go() {
		try {
			$this->begin();
			$token = UFra::factory('UFbean_Sru_Token');
			$token->getByTokenType($this->_srv->get('req')->get->userToken, UFbean_Sru_Token::CONFIRM);
			$token->del();

			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK($token->userId);
			$bean->active = true;
			$bean->save();
			
			$sess = $this->_srv->get('session');
			$sess->auth = $bean->id;
			
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
