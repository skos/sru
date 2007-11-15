<?

/**
 * edycja przez administratora danych uzytkownika
 */
class UFact_SruAdmin_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getByPK((int)$this->_srv->get('req')->get->userId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $bean->login;
			$bean->fillFromPost(self::PREFIX, array('password'));
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			
			if(isset($post['password']) && $post['password'] != '' )
			{
				$bean->password = $post['password'];	
			}				
			$bean->save();

			if ($this->_srv->get('req')->post->{self::PREFIX}['changeComputersLocations']) {
				$comps = UFra::factory('UFbean_Sru_ComputerList');
				$comps->updateLocationByUserId($bean->locationId, $bean->id, $this->_srv->get('session')->authAdmin);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			if ($login != $bean->login) {
				$this->_srv->get('msg')->set(self::PREFIX.'/loginChanged');
			}
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->rollback();
			if (0 == $e->getCode()) {
				$this->markErrors(self::PREFIX, array('mac'=>'regexp'));
			} else {
				throw $e;
			}
		}
	}
}
