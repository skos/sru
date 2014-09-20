<?

/**
 * usuniecie komputera
 */
class UFact_SruAdmin_Computer_Del
extends UFact {

	const PREFIX = 'computerDel';

	public function go() {
		try {
			if (!$this->_srv->get('req')->post->{self::PREFIX}['confirm']) {
				return;
			}
			$this->begin();
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK((int)$this->_srv->get('req')->get->computerId);

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();

			$bean->active = false;
			if ($bean->canAdmin) {
				$bean->canAdmin = false;
			}
			if ($bean->exAdmin) {
				$bean->exAdmin = false;
			}
			$bean->availableTo = NOW;
			$bean->modifiedAt = NOW;
			$bean->modifiedById = $admin->id;
			$bean->save();

			// jeśli to serwer, to zaktualizujmy stan wirtualek i interfejsów, jeśli wirtualka, to interfejsów
			if ($bean->typeId == UFbean_Sru_Computer::TYPE_SERVER || $bean->typeId == UFbean_Sru_Computer::TYPE_SERVER_VIRT) {
				try {
					$comps = UFra::factory('UFbean_Sru_ComputerList');
					$comps->updateActiveByMasterId($bean->id, false, $this->_srv->get('session')->authAdmin);
				} catch (UFex_Dao_NotFound $e) {
					// uzytkownik nie ma komputerow
				}
			}

			// usuwamy przypisane aliasy
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

			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostChangedMailTitle($bean, $user);
				$body = $box->hostChangedMailBody($bean, self::PREFIX, $user);
				$sender->send($user, $title, $body, self::PREFIX);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
