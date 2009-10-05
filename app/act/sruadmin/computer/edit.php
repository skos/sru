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
					$this->markErrors(self::PREFIX, array('ip'=>'noFree'));
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

			if ('' == $post['availableMaxTo']) {
				$post['availableMaxTo'] = 'NOW';
				$this->_srv->get('req')->post->{self::PREFIX} = $post;
			}
			$bean->fillFromPost(self::PREFIX, array('typeId'));
			if (!$bean->active && $bean->availableMaxTo > NOW) {
				// przywrocenie aktywnosci komputera, jezeli podano
				// przyszla date waznosci rejestracji
				$bean->active = true;
				$bean->availableTo = $bean->availableMaxTo;
			}
			if ($bean->availableMaxTo < NOW) {
				$bean->availableMaxTo = NOW;
			}
			if ($bean->availableTo>$bean->availableMaxTo) {
				$bean->availableTo = $bean->availableMaxTo;
			}
			if ($bean->availableTo <= NOW) {
				$bean->active = false;
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
				$title = $box->hostChangedMailTitle($bean);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $history);
				$headers = $box->hostChangedMailHeaders($bean);
				mail($user->email, '=?UTF-8?B?'.base64_encode($title).'?=', $body, $headers);
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
