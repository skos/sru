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
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK((int)$this->_srv->get('req')->get->computerId);

			$admin = UFra::factory('UFbean_SruAdmin_Admin');
			$admin->getFromSession();

			$bean->active = false;
			if ($bean->canAdmin) {
				$bean->canAdmin = false;
			}
			$bean->availableTo = NOW;
			$bean->modifiedAt = NOW;
			$bean->modifiedById = $admin->id;
			$bean->save();

			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_SruAdmin');
				$title = $box->hostChangedMailTitle($bean);
				$body = $box->hostChangedMailBody($bean, self::PREFIX);
				$headers = $box->hostChangedMailHeaders($bean);
				mail($user->email, '=?UTF-8?B?'.base64_encode($title).'?=', $body, $headers);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
