<?

/**
 * usuniecie strony tekstowej
 */
class UFact_Text_Text_Del
extends UFact {

	const PREFIX = 'textDel';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Text_Text');
			$bean->getByAlias($this->_srv->get('req')->get->alias);
			$bean->del();

			$this->_srv->get('req')->get->changedAlias = true;

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
