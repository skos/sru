<?

/**
 * wymeldowanie przez administratora Waleta całego akademika
 */
class UFact_SruWalet_User_AllDel extends UFact {

	const PREFIX = 'allUsersDel';

	public function go() {
		try {
			if (!$this->_srv->get('req')->post->{self::PREFIX}['confirm']) {
				return;
			}

			$modifiedById = $this->_srv->get('session')->authWaletAdmin;
			$this->begin();
			$dorm = UFra::factory('UFbean_Sru_Dormitory');
			$dorm->getByAlias($this->_srv->get('req')->get->dormAlias);
			$users = UFra::factory('UFbean_Sru_UserList');

			$users->updateToDeactivate($dorm->id, $modifiedById);

			$this->commit();
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex $e) {
			UFra::error($e);
		}
	}

}
