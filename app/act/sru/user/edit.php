<?

/**
 * edycja danych uzytkownika
 */
class UFact_Sru_User_Edit
extends UFact {

	const PREFIX = 'userEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_User');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean->getFromSession();
			$bean->fillFromPost(self::PREFIX, array('login','password','name','surname'));
		
			$bean->password = $post['password'];		
			
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
				
			$bean->save();
	
			$comps = UFra::factory('UFbean_Sru_ComputerList');
			$comps->updateLocationByUserId($bean->locationId, $bean->id);
	
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
