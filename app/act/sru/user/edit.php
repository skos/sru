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
			$bean->getFromSession();
			$bean->fillFromPost(self::PREFIX, array('login','password','name','surname'));
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
