<?php
/**
 * edycja administratora
 */
class UFact_SruAdmin_Admin_Edit
extends UFact {

	const PREFIX = 'adminEdit';

	public function go() {
		try {
			$this->begin();
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getByPK((int)$this->_srv->get('req')->get->adminId);
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$login = $bean->login;
			$bean->fillFromPost(self::PREFIX, array('password'));
				
			if(isset($post['password']) && $post['password'] != '' )
			{
				$bean->password = $post['password'];	
			}				
			$bean->save();

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			$this->commit();
		} catch (UFex_Dao_DataNotValid $e) {
			$this->rollback();
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex_Db_QueryFailed $e) {
			$this->rollback();
			if (0 == $e->getCode()) {
				$this->markErrors(self::PREFIX, array('mac'=>'regexp'));//@todo ??
			} else {
				throw $e;
			}
		}
	}
}
