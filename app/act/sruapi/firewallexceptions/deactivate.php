<?
/**
 * deaktywacja wyjatku fw
 */
class UFact_SruApi_Firewallexceptions_Deactivate
extends UFact {

	const PREFIX = 'fwExceptionDeactivate';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_FwException');
			$bean->getByPK($this->_srv->get('req')->get->fwId);

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$bean->modifiedBy = $admin->id;
			$bean->modifiedAt = NOW;
			$bean->active = false;
			
			$bean->save();

			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
