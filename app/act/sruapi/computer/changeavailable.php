<?
/**
 * zmiana maksymalnego czasu rejestracji komputera
 */
class UFact_SruApi_Computer_ChangeAvailable
extends UFact {

	const PREFIX = 'changeAvailable';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_ComputerList');

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$bean->updateAvailableMaxTo($this->_srv->get('req')->get->availableMaxTo, $admin->id);

			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
