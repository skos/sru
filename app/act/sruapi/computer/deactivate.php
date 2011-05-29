<?
/**
 * deaktywacja komputera
 */
class UFact_SruApi_Computer_Deactivate
extends UFact {

	const PREFIX = 'computerDel';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByHost($this->_srv->get('req')->get->computerHost);

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$bean->modifiedById = $admin->id;
			$bean->modifiedAt = NOW;
			$bean->availableTo = NOW;
			$bean->active = false;
			$bean->canAdmin = false;
			$bean->exAdmin = false;
			
			$bean->save();

			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
