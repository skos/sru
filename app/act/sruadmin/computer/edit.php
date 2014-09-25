<?

/**
 * edycja przez administratora danych komputera
 */
class UFact_SruAdmin_Computer_Edit
extends UFact {

	const PREFIX = 'computerEdit';

	public function go() {
		try {
			$this->begin();

			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK((int)$this->_srv->get('req')->get->computerId);
			
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);
			
			$carer = $bean->carerId;
			$active = $bean->active;

			// w przypadku, gdy pole IP jest puste, pobieramy pierwszy wolny
			// IP w danym DS
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if($post['ip'] == '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$dormitoryId = null;
					$vlan = null;
					if ($post['typeId'] <= UFbean_Sru_Computer::LIMIT_STUDENT) {
						$dormitory = UFra::factory('UFbean_Sru_Dormitory');
						$dormitory->getByPK($post['dormitory']);
						$dormitoryId = $dormitory->id;
					} else {
						$vlan = $bean->getVlanByComputerType($post['typeId']);
					}
					$ip->getFreeByDormitoryIdAndVlan($dormitoryId, $vlan);
					$post['ip'] = $ip->ip;
					$this->_srv->get('req')->post->{self::PREFIX} = $post;
				} catch (UFex_Dao_NotFound $e) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('ip'=>'noFreeAdmin'));
					return;
				}
			} else {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getByIp($post['ip']);
					$post['ip'] = $ip->ip;
					$this->_srv->get('req')->post->{self::PREFIX} = $post;
				} catch (UFex_Dao_NotFound $e) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('ip'=>'notFound'));
					return;
				} catch (UFex_Db_QueryFailed $e) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('ip'=>'notFound'));
					return;
				}
			}

			$bean->fillFromPost(self::PREFIX, array('availableTo')); // zgodnie z ticketem #176 filtr wyłączony
			if (($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT || 
				$bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE || $bean->typeId == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) &&
				!is_null($post['skosCarerId']) && $post['skosCarerId'] != 0) {
				$bean->carerId = $post['skosCarerId'];
			} else if ($bean->typeId == UFbean_Sru_Computer::TYPE_ADMINISTRATION && !is_null($post['waletCarerId']) && $post['waletCarerId'] != 0) {
				$bean->carerId = $post['waletCarerId'];
			} else {
				$bean->carerId = null;
			}
			if ($bean->canAdmin && $bean->exAdmin) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('exAdmin'=>'notWithAdmin'));
					return;
			}
			if (($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE) &&
				(is_null($post['deviceModelId']) || $post['deviceModelId'] == 0)) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('deviceModelId'=>'empty'));
					return;
			}
			if (array_key_exists('activateHost', $post) && $post['activateHost'] && !$bean->active && $user->active) {
				// przywrocenie aktywnosci komputera jeśli daty są ok
				$bean->active = true;
				$bean->lastActivated = NOW;
			}
			if(strtotime($post['availableTo']) <= NOW && $bean->active) {
				$conf = UFra::shared('UFconf_Sru');
				$bean->availableTo = $conf->computerAvailableTo;
			} else if ($active || $bean->active) {
				$bean->availableTo = strtotime($post['availableTo']);
			}
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			
			$bean->save();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && $bean->notifyByEmail()) {
				$history = UFra::factory('UFbean_SruAdmin_ComputerHistoryList');
				$history->listByComputerId($bean->id, 1);
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$admin = null;
				if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT || 
					$bean->typeId == UFbean_Sru_Computer::TYPE_MACHINE || $bean->typeId == UFbean_Sru_Computer::TYPE_NOT_SKOS_DEVICE) {
					$admin = UFra::factory('UFbean_SruAdmin_Admin');
					$admin->getByPK($this->_srv->get('session')->authAdmin);
					
					// admin, który został opiekunem, powinien dostać maila
					if ($carer != $bean->carerId) {
						$title = $box->carerChangedToYouMailTitle($bean);
						$body = $box->carerChangedToYouMailBody($bean, $admin);
						$newCarer = UFra::factory('UFbean_SruAdmin_Admin');
						$newCarer->getByPK($bean->carerId);
						$sender->send($newCarer, $title, $body, self::PREFIX);
					}
				}
				$title = $box->hostChangedMailTitle($bean, $user);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $user, $history, $admin);
				$sender->send($user, $title, $body, self::PREFIX);
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
