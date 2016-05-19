<?php
/**
 * edycja aliasów komputera
 */
class UFact_SruAdmin_Computer_AliasesEdit
extends UFact {

	const PREFIX = 'computerAliasesEdit';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$this->begin();
			$computer = UFra::factory('UFbean_Sru_Computer');
			$computer->getByPK((int)$this->_srv->get('req')->get->computerId);
			
			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($computer->userId);

			$bean = UFra::factory('UFbean_SruAdmin_ComputerAlias');

			$deleted = array();
			if (isset($post['aliases'])) {
				while (key($post['aliases'])) {
					if (current($post['aliases'])) {
						$aliasId = key($post['aliases']);
						$bean->getByPK($aliasId);
						array_push($deleted, $bean->host);
						$bean->del();
					}
					next($post['aliases']);
				}
			}

			$added = null;
			if ($post['alias'] != '') {
				$bean = UFra::factory('UFbean_SruAdmin_ComputerAlias');
				$bean->computerId = $computer->id;
				$bean->host = $post['alias'];
				$bean->domainName = $post['alias'].'.'.$computer->domainSuffix;
				$bean->recordType = ($post['isCname'] == 1 ? UFbean_SruAdmin_ComputerAlias::TYPE_CNAME : UFbean_SruAdmin_ComputerAlias::TYPE_A);
				$added = $post['alias'];
				$bean->save();
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail && (count($deleted) > 0 || !is_null($added))) {
				// wyslanie maila
				$admin = UFra::factory('UFbean_SruAdmin_Admin');
				$admin->getByPK($this->_srv->get('session')->authAdmin);

				$box = UFra::factory('UFbox_SruAdmin');
				$sender = UFra::factory('UFlib_Sender');
				$title = $box->hostAliasesChangedMailTitle($computer);
				$body = $box->hostAliasesChangedMailBody($computer, $deleted, $added, $admin);
				$sender->send($user, $title, $body, self::PREFIX);
			}

		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->rollback();
			UFra::error($e);
		}
	}
}
