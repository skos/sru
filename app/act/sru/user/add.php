<?

/**
 * dodanie uzytkownika
 */
class UFact_Sru_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX);
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->markErrors(self::PREFIX, array('login'=>'duplicated'));
		}
	}
}
