<?
/**
 * usuniecie aliasu komputera
 */
class UFact_SruApi_Computer_DelAlias
extends UFact {

	const PREFIX = 'computerDelAlias';

	public function go() {
		try {
			$deleted = array();
			$bean = UFra::factory('UFbean_SruAdmin_ComputerAlias');
			$bean->getByHost($this->_srv->get('req')->get->alias);
			array_push($deleted, $bean->host);
			$bean->del();

			$conf = UFra::shared('UFconf_Sru');
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
				$body = $box->hostAliasesChangedMailBody($computer, $deleted, null, $admin);
				$sender->send($user, $title, $body, self::PREFIX);
			}

			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
			var_dump("aaaa");
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX);
			UFra::error($e);
		}
	}
}
