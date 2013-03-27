<?php
/**
 * edycja portu switcha
 */
class UFact_SruAdmin_SwitchPort_Edit
extends UFact {

	const PREFIX = 'switchPortEdit';

	public function go() {
		try {
			$this->begin();
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$conf = UFra::shared('UFconf_Sru');

			$switch = UFra::factory('UFbean_SruAdmin_Switch');
			$switch->getBySerialNo($this->_srv->get('req')->get->switchSn);
			$bean = UFra::factory('UFbean_SruAdmin_SwitchPort');
			$bean->getBySwitchIdAndOrdinalNo($switch->id, (int)$this->_srv->get('req')->get->portNo);

			$bean->fillFromPost(self::PREFIX);

			if ($post['locationAlias'] != '' && $bean->connectedSwitchId != '') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Location and connected switch set in one time', 0, E_WARNING, array('locationAlias' => 'roomAndSwitch'));
			}
			if ($bean->connectedSwitchId != '' && $bean->admin == true) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Connected switch and admin set in one time', 0, E_WARNING, array('connectedSwitchId' => 'switchAndAdmin'));
			}
			if (key_exists('penaltyId', $post) && $post['penaltyId'] != '' && $post['portEnabled']) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Penalty set to enabled port', 0, E_WARNING, array('portEnabled' => 'enabledAndPenalty'));
			}
			if ($post['locationAlias'] != '') {
				try {
					$dorm = UFra::factory('UFbean_Sru_Dormitory');
					$dorm->getByPK($switch->dormitoryId);
				} catch (UFex $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data dormitory error', 0, E_WARNING, array('locationAlias' => 'noDormitory'));
				}
				try {
					$loc = UFra::factory('UFbean_Sru_Location');
					$loc->getByAliasDormitory($post['locationAlias'], $dorm->id);
					$bean->locationId = $loc->id;
				} catch (UFex $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Data room error', 0, E_WARNING, array('locationAlias' => 'noRoom'));
				}
			} else {
				$bean->locationId = NULL;
			}

			if ($post['copyToSwitch'] && !is_null($switch->ip)) {
				$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
				$result = false;
				$connectedSwitch = null;
				if ($post['connectedSwitchId'] != '') {
					$connectedSwitch = UFra::factory('UFbean_SruAdmin_Switch');
					$connectedSwitch->getByPK($post['connectedSwitchId']);
				}
				$ban = false;
				if (key_exists('penaltyId', $post) && $post['penaltyId'] != '') {
					$ban = true;
				}
				$comment = '';
				if ($post['comment'] != '') {
					$comment = $hp->removeSpecialChars($post['comment']);
				}
				$name = UFlib_Helper::formatPortName($post['locationAlias'], $connectedSwitch, $ban, $comment);
				$result = $hp->setPortAlias($bean->ordinalNo, $name);
					
				if (!$result) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Writing to switch error', 0, E_WARNING, array('switch' => 'writingError'));
				}
			}

			$sendMail = false;
			$result = true;
			if (!is_null($switch->ip)) {
				$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
				if ($post['portEnabled']) {
					$status = UFlib_Snmp_Hp::ENABLED;
				} else {
					$status = UFlib_Snmp_Hp::DISABLED;
				}
				if (isset($post['portStatus']) && $post['portStatus'] != $post['portEnabled']) {
					$result = $hp->setPortStatus($bean->ordinalNo, $status);
					if ($result) $sendMail = true;
				}
				// zawsze przy edycji opuszczamy flagę, jesli jest podniesiona
				$flag = $hp->getIntrusionFlag($bean->ordinalNo);
				if ($flag == UFlib_Snmp_Hp::UP) {
					$result = $result && $hp->setIntrusionFlag($bean->ordinalNo, UFlib_Snmp_Hp::DOWN);
				}
				if (!$result) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'Writing to switch error or port status not changed', 0, E_WARNING, array('switch' => 'writingError'));
				}
			}

			$bean->save();

			if ($sendMail &&  $conf->sendEmail) {
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$admin = UFra::factory('UFbean_SruAdmin_Admin');
				$admin->getByPK($this->_srv->get('session')->authAdmin);
				$title = $box->switchPortModifiedMailTitle($bean);
				$body = $box->switchPortModifiedMailBody($bean, $admin, $post['portEnabled']);
				$sender->sendMail("admin-".$bean->dormitoryAlias."@ds.pg.gda.pl", $title, $body, self::PREFIX);
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
