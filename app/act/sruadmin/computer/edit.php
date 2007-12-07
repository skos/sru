<?

/**
 * edycja przez administratora danych komputera
 */
class UFact_SruAdmin_Computer_Edit
extends UFact {

	const PREFIX = 'computerEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByPK((int)$this->_srv->get('req')->get->computerId);

			// w przypadku, gdy pole IP jest puste, pobieramy pierwszy wolny
			// IP w danym DS
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if($post['ip'] == '') {
				try {
					$ip = UFra::factory('UFbean_Sru_Ipv4');
					$ip->getFreeByDormitoryId($bean->dormitoryId);
					$post['ip'] = $ip->ip;
					$this->_srv->get('req')->post->{self::PREFIX} = $post;
				} catch (UFex_Dao_NotFound $e) {
					$this->markErrors(self::PREFIX, array('ip'=>'noFree'));
					return;
				}
			}

			$bean->fillFromPost(self::PREFIX, array('typeId'));
			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			if (0 == $e->getCode()) {
				$this->markErrors(self::PREFIX, array('mac'=>'regexp'));
			} else {
				throw $e;
			}
		}
	}
}
