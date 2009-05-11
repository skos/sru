<?

/**
 * dodanie uzytkownika
 */
class UFact_SruAdmin_User_Add
extends UFact {

	const PREFIX = 'userAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->fillFromPost(self::PREFIX, array('password'));
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->gg = '';
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			// wygenerowanie hasla
			$password = md5($bean->login.NOW);
			$password = base_convert($password, 16, 35);
			$password = substr($password, 0, 8);
			$bean->password = $bean->generatePassword($bean->login, $password);

			// sprawdzenie w bazie osiedla, jezeli admin nie wymusil zignorowania problemu
			if (!array_key_exists('ignoreWalet', $post) || 0 == $post['ignoreWalet']) {
				$walet = UFra::factory('UFbean_Sru_User');
				try {
					$walet->getFromWalet($bean->name, $bean->surname, $bean->locationAlias, $bean->dormitory);
				} catch (UFex_Dao_NotFound $e) {
					throw UFra::factory('UFex_Dao_DataNotValid', 'User not in Walet database', 0, E_WARNING,  array('walet' => 'notFound'));
				}
			}
				
			$id = $bean->save();
			$bean->getByPK($id);	// uzupelnione dane dociagane z innych tabel
			$req = $this->_srv->get('req');
			$req->get->userId = $id;

			// wyslanie maila
			$box = UFra::factory('UFbox_Sru');
			$title = $box->userAddMailTitle($bean);
			$body = $box->userAddMailBodyNoInfo($bean, $password);
			$headers = $box->userAddMailHeaders($bean);
			mail($bean->email, $title, $body, $headers);

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
