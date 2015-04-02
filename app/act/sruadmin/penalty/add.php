<?php
/**
 * dodanie kary
 */
class UFact_SruAdmin_Penalty_Add
extends UFact {

	const PREFIX = 'penaltyAdd';

	public function go() {
		try {
			$this->begin();
			$conf = UFra::shared('UFconf_Sru');
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($this->_srv->get('req')->get->userId);

			$acl = $this->_srv->get('acl');
			if(!$acl->sruAdmin('penalty', 'addForUser', $user->id)) {
				UFra::error('Inactive user cannot be banned');
				return;
			}
			
			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->fillFromPost(self::PREFIX, null, array('duration', 'reason', 'comment', 'computerId', 'after'));
			$bean->userId = $user->id;
			$bean->startAt = NOW;
			$bean->endAt = NOW + $bean->duration * 24 * 3600;
			$bean->createdAt = NOW;
			$bean->createdById = $this->_srv->get('session')->authAdmin; 
			if ($bean->after > $bean->duration) {
				$bean->amnestyAfter = $bean->endAt;
			} else {
				$bean->amnestyAfter = NOW + $bean->after * 24 * 3600;
			}
			if (null === $bean->computerId) {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_COMPUTERS;
			} elseif (0 === $bean->computerId) {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_WARNING;
				$bean->active = false;
			} else {
				$bean->typeId = UFbean_SruAdmin_Penalty::TYPE_COMPUTER;
				$computerId = $bean->computerId;
				if(!$acl->sruAdmin('penalty', 'addForComputer', $bean->computerId)) {
					UFra::error('Inactive computer cannot be banned');
					return;
				}
			}

			try {
				$tpl = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
				$tpl->getByPK($this->_srv->get('req')->get->templateId);
				$bean->templateId = $tpl->id;
			} catch (UFex_Core_DataNotFound $e) {
			} catch (UFex_Dao_NotFound $e) {
			}

			$id = $bean->save();
			$bean->getByPK($id);	// uzupelnione dane dociagane z innych tabel
			$req = $this->_srv->get('req');
			$req->get->penaltyId = $id;

			if (UFbean_SruAdmin_Penalty::TYPE_COMPUTERS === $bean->typeId) {
				$computers = UFra::factory('UFbean_Sru_ComputerList');
				try {
					$computers->listByUserId($user->id);

					foreach ($computers as $computer) {
						$penalty = UFra::factory('UFbean_SruAdmin_ComputerBan');
						$penalty->penaltyId = $id;
						$penalty->computerId = $computer['id'];
						$penalty->save(false);
					}
				} catch (UFex_Dao_NotFound $e) {
					// uzytkownik nie ma komputerow
				}
			} elseif (UFbean_SruAdmin_Penalty::TYPE_COMPUTER === $bean->typeId) {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByUserIdPK($user->id, $computerId);

				$penalty = UFra::factory('UFbean_SruAdmin_ComputerBan');
				$penalty->penaltyId = $id;
				$penalty->computerId = $computer->id;
				$penalty->save(false);
			}

			// zablokujmy port, jeśli podany
			if (key_exists('portId', $post) && $post['portId'] != '') {
				try {
					$port = UFra::factory('UFbean_SruAdmin_SwitchPort');
					$port->getByPK($post['portId']);
					$port->penaltyId = $id;
					$port->save();
					$switch = UFra::factory('UFbean_SruAdmin_Switch');
					$switch->getByPK($port->switchId);
					$hp = UFra::factory('UFlib_Snmp_Hp', $switch->ip, $switch);
					$result = $hp->setPortStatus($port->ordinalNo, UFlib_Snmp_Hp::DISABLED);
					$name = UFlib_Helper::formatPortName($port->locationAlias, null, true, $hp->removeSpecialChars($port->comment));
					$result = $result && $hp->setPortAlias($port->ordinalNo, $name);
				} catch (UFex_Dao_NotFound $e) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('portId'=>'writeError'));
					return;
				}
			}

			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->penaltyAddMailTitle($bean, $user);
				if (UFbean_SruAdmin_Penalty::TYPE_COMPUTERS === $bean->typeId) {
					$computers = UFra::factory('UFbean_Sru_ComputerList');
					try {
						$computers->listByUserId($user->id);
					} catch (UFex_Dao_NotFound $e) {
						// uzytkownik nie ma komputerow
					}
				} elseif (UFbean_SruAdmin_Penalty::TYPE_COMPUTER === $bean->typeId) {
					$computer = UFra::factory('UFbean_Sru_Computer');
					$computer->getByUserIdPK($user->id, $computerId);
					$computers[0]= $computer;
				} else {
					$computers = null;
				}
				$body = $box->penaltyAddMailBody($bean, $user, $computers);
				$sender->send($user, $title, $body, self::PREFIX);
				
				// wyslanie maila do admina
				$admin = UFra::factory('UFbean_SruAdmin_Admin');
				$admin->getByPK($bean->createdById);
				$box = UFra::factory('UFbox_SruAdmin');
				$title = $box->penaltyAddMailTitle($bean, $user);
				$body = $box->penaltyAddMailBody($bean, $user, $computers, $admin);
				$sender->sendMail("adnet@ds.pg.gda.pl", $title, $body, self::PREFIX);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
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
