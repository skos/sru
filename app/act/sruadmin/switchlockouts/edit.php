<?php
/**
 * edycja lockoutów switcha
 */
class UFact_SruAdmin_SwitchLockouts_Edit
extends UFact {

	const PREFIX = 'switchLockoutsEdit';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$switch = UFra::factory('UFbean_SruAdmin_Switch');
			$switch->getBySerialNo($this->_srv->get('req')->get->switchSn);

			$pattern = '/^[0-9a-fA-F]{2}?([- :]?[0-9a-fA-F]{2}){5}$/';

			if (is_null($switch->ip)) {
				return;
			}
			$nothingToDo = true;

			$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip);
			if ($post['mac'] != '') {
				$nothingToDo = false;
				if (!preg_match($pattern, $post['mac'])) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'MAC format error', 0, E_WARNING, array('mac' => 'wrongFormat'));
				}
				if (strlen($post['mac']) < 17) {
					$mac = $post['mac'];
					for ($i = 0; $i < 5; $i++) {
						if ($mac[$i * 3 + 2] != ':' && $mac[$i * 3 + 2] != '-') {
							$mac = substr($mac, 0, $i * 3 + 2).':'.substr($mac, $i * 3 + 2);
						}
					}
					$post['mac'] = $mac;
				}
				$result = $hp->setLockoutMac($post['mac']);
				if (!$result) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Writing to switch error', 0, E_WARNING, array('switch' => 'writingError'));
				}
			}

			if (isset($post['lockouts'])) {
				while (key($post['lockouts'])) {
					if (current($post['lockouts'])) {
						$nothingToDo = false;
						$result = $hp->setLockoutMac(key($post['lockouts']), false);
						if (!$result) {
							throw UFra::factory('UFex_Dao_DataNotValid', 'Writing to switch error', 0, E_WARNING, array('switch' => 'writingError'));
						}
					}
					next($post['lockouts']);
				}
			}
			
			if ($nothingToDo) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Nothing to change', 0, E_WARNING, array('switch' => 'nothingToDo'));
			}

 			$this->markOk(self::PREFIX);
 			$this->postDel(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
