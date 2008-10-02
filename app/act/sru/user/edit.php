<?

/**
 * edycja danych uzytkownika
 */
class UFact_Sru_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	protected function checkOldPassword(&$bean, &$post) {
		if ($bean->password != $bean->generatePassword($bean->login, $post['password3'])) {
			throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password3" is not valid', 0, E_WARNING, array('password3' => 'invalid'));
		}
	}
	
	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean->getFromSession();
			$locationId = $bean->locationId;
			$bean->fillFromPost(self::PREFIX, array('email', 'login','password','name','surname'));


			if (isset($post['password']) && $post['password'] != '' ) {
				$this->checkOldPassword($bean, $post);
				if (!isset($post['password2']) || $post['password'] != $post['password2']) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data "password" and "password2" do not match', 0, E_WARNING, array('password' => 'mismatch'));
				}
				$bean->password = $bean->generatePassword($bean->login, $post['password']);
			}
			if (isset($post['email']) && $post['email'] != $bean->email) {
				$this->checkOldPassword($bean, $post);
				$bean->email = $post['email'];
			}

			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
				
			$bean->save();
	
			if ($locationId != $bean->locationId) {
				$comps = UFra::factory('UFbean_Sru_ComputerList');
				$comps->updateLocationByUserId($bean->locationId, $bean->id);
			}
	
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
