<?
/**
 * edycja kary
 */
class UFact_SruAdmin_Penalty_Edit
extends UFact {

	const PREFIX = 'penaltyEdit';

	public function go() {
		try {
			$this->begin();
			$conf = UFra::shared('UFconf_Sru');

			$bean = UFra::factory('UFbean_SruAdmin_Penalty');
			$bean->getByPK($this->_srv->get('req')->get->penaltyId);
			//aby miec historię do maila
			$oldBean = clone $bean;
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if (!$bean->active) {
				UFra::error('Penalty '.$bean->id.' is not active');
				return;
			}
			$acl = $this->_srv->get('acl');
			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();

			if($acl->sruAdmin('penalty', 'editOneFull', $bean->id)) {
				if ('' === $post['endAt']) {
					$bean->endAt = NOW;
				} else if (date(UFtpl_Common::TIME_YYMMDD_HHMM, $bean->endAt) !== $post['endAt']) {
					$bean->fillFromPost(self::PREFIX, null, array('endAt'));
				}
				$bean->fillFromPost(self::PREFIX, null, array('reason', 'after'));
				$bean->amnestyAfter = $bean->startAt + $bean->after * 24 * 3600;
				$tplTitle = $bean->templateTitle;
				try {
					$tpl = UFra::factory('UFbean_SruAdmin_PenaltyTemplate');
					$tpl->getByPK($this->_srv->get('req')->get->templateId);
					$bean->templateId = $tpl->id;
					$tplTitle = $tpl->title;
				} catch (UFex_Core_DataNotFound $e) {
				} catch (UFex_Dao_NotFound $e) {
				}
			} else if($acl->sruAdmin('penalty', 'editOnePartly', $bean->id)){
				if ('' === $post['endAt']) {
					$bean->endAt = $bean->amnestyAfter;
				} else if (date(UFtpl_Common::TIME_YYMMDD_HHMM, $bean->endAt) !== $post['endAt']) {
					$bean->fillFromPost(self::PREFIX, null, array('endAt'));
					if ($bean->endAt < $bean->amnestyAfter) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Modification before amnesty date', 0, E_WARNING, array('endAt' => 'tooShort'));
					}
				}
				$tplTitle = $bean->templateTitle;
			} else {
				if(!$acl->sruAdmin('penalty', 'editOne', $bean->id)) {
					UFra::error('Admin '.$admin->id.' doesn\'t have permission to edit this penalty');
					return;
				}

				if ('' === $post['endAt']) {
					$bean->endAt = $bean->amnestyAfter;
				} else if (date(UFtpl_Common::TIME_YYMMDD_HHMM, $bean->endAt) !== $post['endAt']) {
					$bean->fillFromPost(self::PREFIX, null, array('endAt'));
					if ($bean->endAt < $bean->amnestyAfter) {
						throw UFra::factory('UFex_Dao_DataNotValid', 'Modification before amnesty date', 0, E_WARNING, array('endAt' => 'tooShort'));
					}
				}
				$tplTitle = $bean->templateTitle;
			}
			if (trim($post['newComment']) == '' || trim($post['newComment']) == $bean->comment) {
				throw UFra::factory('UFex_Dao_DataNotValid', 'Modification comment cannot be null', 0, E_WARNING, array('newComment' => 'notNull'));
			}
			$bean->comment = trim($post['newComment']);
			$bean->modifiedAt = NOW;
			$bean->modifiedById = $this->_srv->get('session')->authAdmin; 
			if ($bean->endAt <= NOW) {
				$bean->endAt = NOW;
				$bean->amnestyById = $bean->modifiedById;
				$bean->amnestyAt = NOW;
				$bean->active = false;
			}

			$bean->save();

			if (!$bean->active) {
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
						$swPorts->updatePenaltyIdByPortId($port['id'], null);
					}
				} catch (UFex_Dao_NotFound $e) {
					// brak portów z przypisaną karą
				}
			}

			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);
			if ($conf->sendEmail && $bean->notifyByEmail()) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->penaltyEditMailTitle($bean, $user);
				$body = $box->penaltyEditMailBody($bean, $user);
				$sender->send($user, $title, $body, self::PREFIX);
			}
			if ($conf->sendEmail) {
				// wyslanie maila do admina
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->penaltyEditMailTitle($bean, $oldBean, $user);
				$body = $box->penaltyEditMailBody($bean, $oldBean, $tplTitle, $user, $admin);
				$sender->sendMail("admin-".$user->dormitoryAlias."@ds.pg.gda.pl", $title, $body, self::PREFIX);
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
