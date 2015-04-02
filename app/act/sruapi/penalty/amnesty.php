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
			$bean->modifiedById = $admin->id;
			$bean->modifiedAt = NOW;
			
			$bean->save();

			try {
				$swPorts = UFra::factory('UFbean_SruAdmin_SwitchPortList');
				$swPorts->listByPenaltyId($bean->id);
				foreach ($swPorts as $port) {
					$switch = UFra::factory('UFbean_SruAdmin_Switch');
					$switch->getByPK($port['switchId']);
					$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
					$result = $hp->setPortStatus($port['ordinalNo'], UFlib_Snmp_Hp::ENABLED);
					$name = UFlib_Helper::formatPortName($port['locationAlias'], null, false, $hp->removeSpecialChars($port['comment']));
					$result = $result && $hp->setPortAlias($port['ordinalNo'], $name);
				}
				$swPorts->updatePenaltyIdByPortId($port['id'], null);
			} catch (UFex_Dao_NotFound $e) {
				// brak portów z przypisaną karą
			}

			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
