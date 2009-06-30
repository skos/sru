<?
/**
 * amnestia kary
 */
class UFact_SruApi_Penalty_Amnesty
extends UFact {

	const PREFIX = 'penaltyAmnesty';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->getByPK($this->_srv->get('req')->get->penaltyId);
			if (!$bean->active) {
				$this->markErrors(self::PREFIX, array('penalty'=>'notActive'));
				return;
			}
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$bean->endAt = NOW;
			$bean->amnestyById = $admin->id;
			$bean->amnestyAt = NOW;
			$bean->active = false;
			
			$bean->save();

			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}