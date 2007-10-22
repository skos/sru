<?

/**
 * dodanie strony tekstowej
 */
class UFact_Text_Text_Add
extends UFact {

	const PREFIX = 'textAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Text_Text');
			$bean->fillFromPost(self::PREFIX);
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->markErrors(self::PREFIX, array('alias'=>'duplicated'));
		}
	}
}
