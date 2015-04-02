<?

/**
 * wyslanie maila z linkiem do zalogowania uzytkownika
 */
class UFact_Sru_User_SendPassword
extends UFact {

	const PREFIX = 'sendPassword';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX, null, array('email'));

			try {
				$list = UFra::factory('UFbean_Sru_UserList');
				$list->listByEmailActive($bean->email, true);
				if (count($list) === 1) {
					$bean->getByEmailActive($bean->email, true);
				} else {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('email'=>'notUnique'));
					return;
				}
			} catch (UFex_Dao_NotFound $e) {
				$this->rollback();
				$this->markErrors(self::PREFIX, array('email'=>'notFound'));
				return;
			}
			$id = $bean->id;

			// wyslanie maila
			$token = UFra::factory('UFbean_Sru_Token');
			$token->token = md5($id.NOW);
			$token->userId = $id;
			$token->type = UFbean_Sru_Token::RECOVER;
			$token->save();

			$box = UFra::factory('UFbox_Sru');
			$sender = UFra::factory('UFlib_Sender');
			$title = $box->userRecoverPasswordMailTitle($bean);
			$body = $box->userRecoverPasswordMailBodyToken($bean, $token);
			$sender->send($bean, $title, $body);

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
