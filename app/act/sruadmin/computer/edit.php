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

			// w przypadku, gdy pole IP jest puste, pobieramy pierwszy wolny
			// IP w danym DS
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if($post['ip'] == '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getFreeByDormitoryId((int) $post['dormitory']);
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

			if ($post['availableMaxTo'] == '') {
				$post['availableMaxTo'] = 'NOW';
				$this->_srv->get('req')->post->{self::PREFIX} = $post;
			}
			if ($post['availableTo'] == '') {
				//jeśli pusta data rejestracji, ustaw na maksymalną
				$post['availableTo'] = $post['availableMaxTo'];
				$this->_srv->get('req')->post->{self::PREFIX} = $post;
			}
			$bean->fillFromPost(self::PREFIX); // zgodnie z ticketem #176 filtr wyłączony
			if ($bean->canAdmin && $bean->exAdmin) {
					$this->rollback();
					$this->markErrors(self::PREFIX, array('exAdmin'=>'notWithAdmin'));
					return;
			}
			if (!$bean->active && $bean->availableMaxTo > NOW && $user->active) {
				// przywrocenie aktywnosci komputera, jezeli podano
				// przyszla date waznosci rejestracji
				$bean->active = true;
				$bean->lastActivated = NOW;
				$bean->availableTo = $bean->availableMaxTo;
			}
			if ($bean->active && $bean->availableMaxTo < NOW) {
				$bean->availableMaxTo = NOW;
			}
			if (strtotime(date('Y-m-d', $bean->availableTo)) > $bean->availableMaxTo) {
				$bean->availableTo = $bean->availableMaxTo;
			}
			if ($bean->availableTo <= NOW) {
				$bean->availableTo = NOW;
				$bean->active = false;

				if ($bean->typeId == 4) {
					// jeśli usuwamy serwer, to musimy mu też usunąć przypisane aliasy
					try {
						$aliases = UFra::factory('UFbean_SruAdmin_ComputerAliasList');
						$aliases->listByComputerId($bean->id);
						foreach ($aliases as $alias) {
							$aliasBean = UFra::factory('UFbean_SruAdmin_ComputerAlias');
							$aliasBean->getByPK($alias['id']);
							$aliasBean->del();
						}
					} catch (UFex_Dao_NotFound $e) {
					}
				}
			}
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			
			$bean->save();

			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && $bean->notifyByEmail()) {
				$history = UFra::factory('UFbean_SruAdmin_ComputerHistoryList');
				$history->listByComputerId($bean->id, 1);
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$admin = null;
				if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER) {
					$admin = UFra::factory('UFbean_SruAdmin_Admin');
					$admin->getByPK($this->_srv->get('session')->authAdmin);
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
