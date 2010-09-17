<?php
/**
 * edycja portów switcha
 */
class UFact_SruAdmin_SwitchPorts_Edit
extends UFact {

	const PREFIX = 'switchPortsEdit';
	const PREFIX_COPY = 'copyAliasesFromSwitch';
	const MAX_PORT_NAME = 64;

	public function go() {
		try {
			try {
				if (!is_null($this->_srv->get('req')->post->{self::PREFIX_COPY})) {
					$this->_srv->get('msg')->info = 'copyFromSwitch';
					return;
				}
			} catch (UFex $e) {
			}

			$this->begin();
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			while ($port = current($post)) {
				$bean = UFra::factory('UFbean_SruAdmin_SwitchPort');
				$bean->getByPK((int)key($post));
				$switch = UFra::factory('UFbean_SruAdmin_Switch');
				$switch->getByPK($bean->switchId);

				$bean->comment = $port['comment'];
				if ($port['locationAlias'] != '' && $port['connectedSwitchId'] != '') {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Location and connected switch set in one time', 0, E_WARNING, array('locationAlias' => 'roomAndSwitch_'.$port['ordinalNo']));
				}
				if ($port['connectedSwitchId'] != '' && $bean->admin == true) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Connected switch and admin set in one time', 0, E_WARNING, array('locationAlias' => 'switchAndAdmin_'.$port['ordinalNo']));
				}
				if ($port['locationAlias'] != '') {
					try {
						$dorm = UFra::factory('UFbean_Sru_Dormitory');
						$dorm->getByPK($switch->dormitoryId);
					} catch (UFex $e) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Data dormitory error', 0, E_WARNING, array('locationAlias' => 'noDormitory'));
					}
					try {
						$loc = UFra::factory('UFbean_Sru_Location');
						$loc->getByAliasDormitory($port['locationAlias'], $dorm->id);
						$bean->locationId = $loc->id;
					} catch (UFex $e) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Data room error', 0, E_WARNING, array('locationAlias' => 'noRoom_'.$port['ordinalNo']));
					}
				} else {
					$bean->locationId = NULL;
				}
				$bean->connectedSwitchId = $port['connectedSwitchId'];

				if (!is_null($switch->ip)) {
					$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip);
					$result = false;
					if ($port['locationAlias'] != '') {
						if ($port['comment'] != '') {
							$name = $port['locationAlias'] . ': ' . $hp->removeSpecialChars($port['comment']);
							$name = substr($name, 0, self::MAX_PORT_NAME);
							$result = $hp->setPortAlias($bean->ordinalNo, $name);
						} else {
							$result = $hp->setPortAlias($bean->ordinalNo, $port['locationAlias']);
						}
					} else if ($port['connectedSwitchId'] != '') {
						$connectedSwitch = UFra::factory('UFbean_SruAdmin_Switch');
						$connectedSwitch->getByPK($port['connectedSwitchId']);
						if ($port['comment'] != '') {
							$name = $connectedSwitch->dormitoryAlias.'-hp'.$connectedSwitch->hierarchyNo . ': ' . $hp->removeSpecialChars($port['comment']);
							$name = substr($name, 0, self::MAX_PORT_NAME);
							$result = $hp->setPortAlias($bean->ordinalNo, $name);
						} else {
							$result = $hp->setPortAlias($bean->ordinalNo, $connectedSwitch->dormitoryAlias.'-hp'.$connectedSwitch->hierarchyNo);
						}
					} else if ($port['comment'] != '') {
						$result = $hp->setPortAlias($bean->ordinalNo, $hp->removeSpecialChars($port['comment']));
					} else {
						$result = $hp->setPortAlias($bean->ordinalNo, '');
					}
					if (!$result) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Writing to switch error', 0, E_WARNING, array('switch' => 'writingError'));
					}
				}

				$bean->save();
				next($post);
			}
 			$this->markOk(self::PREFIX);
 			$this->postDel(self::PREFIX);
 			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
