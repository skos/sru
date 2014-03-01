<?
/**
 * deaktywacja komputera
 */
class UFact_SruApi_Computer_Deactivate
extends UFact {

	const PREFIX = 'computerDeactivate';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByHost($this->_srv->get('req')->get->computerHost);

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromHttp();

			$bean->modifiedById = $admin->id;
			$bean->modifiedAt = NOW;
			$bean->availableTo = NOW;
			$bean->active = false;
			$bean->canAdmin = false;
			$bean->exAdmin = false;
			
			$bean->save();
			
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

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($bean->userId);
				// nie musimy pobierać nowych danych hosta, ponieważ nie powinny się zmienić
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruApi');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostDeactivatedMailTitle($bean, $user);
				$body = $box->hostDeactivatedMailBody($bean, self::PREFIX, $user);
				$sender->send($user, $title, $body, self::PREFIX);
			}

			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
