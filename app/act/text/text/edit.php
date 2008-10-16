<?

/**
 * zmiana strony tekstowej
 */
class UFact_Text_Text_Edit
extends UFact {

	const PREFIX = 'textEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Text_Text');
			$bean->getByAlias($this->_srv->get('req')->get->alias);
			$bean->fillFromPost(self::PREFIX);
			$bean->modifiedAt = NOW;
			$bean->save();

			$this->_srv->get('req')->get->changedAlias = true;

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->markErrors(self::PREFIX, array('alias'=>'duplicated'));
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
