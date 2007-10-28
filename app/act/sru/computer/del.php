<?

/**
 * usuniecie wlasnego komputera
 */
class UFact_Sru_Computer_Del
extends UFact {

	const PREFIX = 'computerDel';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);
			$bean->active = false;
			$bean->availableTo = NOW;
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		}
	}
}
