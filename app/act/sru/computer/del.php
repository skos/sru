<?

/**
 * usuniecie wlasnego komputera
 */
class UFact_Sru_Computer_Del
extends UFact {

	const PREFIX = 'computerDel';

	public function go() {
		try {
			if (!$this->_srv->get('req')->post->{self::PREFIX}['confirm']) {
				return;
			}
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);
			$bean->active = false;
			$bean->availableTo = NOW;
			$bean->save();

			$user = UFra::factory('UFbean_Sru_User');
			$user->getFromSession();

			// wyslanie maila do usera
			$box = UFra::factory('UFbox_Sru');
			$title = $box->hostChangedMailTitle($bean);
			$body = $box->hostChangedMailBody($bean, self::PREFIX);
			$headers = $box->hostChangedMailHeaders($bean);
			mail($user->email, $title, $body, $headers);

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_NotFound $e) {
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
