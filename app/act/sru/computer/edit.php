<?

/**
 * edycja danych wlasnego komputera
 */
class UFact_Sru_Computer_Edit
extends UFact {

	const PREFIX = 'computerEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);
			$bean->fillFromPost(self::PREFIX, null, array('mac', 'availableTo'));
			if ($bean->availableTo < NOW) {
				$bean->availableTo = NOW;
			}
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
