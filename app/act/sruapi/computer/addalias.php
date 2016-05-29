<?
/**
 * ddodanie aliasu komputera
 */
class UFact_SruApi_Computer_AddAlias
extends UFact {

	const PREFIX = 'computerAddAlias';

	public function go() {
		try {
			$get = $this->_srv->get('req')->get;
			$conf = UFra::shared('UFconf_Sru');

			try {
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByDomainName($get->computer);
			} catch (UFex_Dao_NotFound $e) {
				$computerAlias = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$computerAlias->getByDomainName($get->computer);
				$computer->getByPK($computerAlias->computerId);
			}

			$bean = UFra::factory('UFbean_SruAdmin_ComputerAlias');
			$bean->computerId = $computer->id;
			$bean->host = $get->alias;
			$bean->domainName = $get->alias.'.'.$computer->domainSuffix;
			$bean->recordType = $get->recordType;
			$bean->availTo = time() + $conf->computerAliasesValidTime;
			try {
				$bean->value = $get->value;
			} catch (UFex_Core_DataNotFound $e) {
			}
			$added = $get->alias;
			
			$bean->save();

			if ($conf->sendEmail) {
				$admin = UFra::factory('UFbean_SruAdmin_Admin');
				$admin->getFromHttp();
				$computer = UFra::factory('UFbean_Sru_Computer');
				$computer->getByPK($bean->computerId);
				$user = UFra::factory('UFbean_Sru_User');
				$user->getByPK($computer->userId);

				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostAliasesChangedMailTitle($computer);
				$body = $box->hostAliasesChangedMailBody($computer, array(), $added, $admin);
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
