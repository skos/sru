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
			$acl = $this->_srv->get('acl');
			$login = $bean->login;
			$bean->fillFromPost(self::PREFIX, array('password', 'login', 'typeId', 'active'));
				
			if(isset($post['password']) && $post['password'] != '' )
			{
				$bean->password = $post['password'];	
			}
			
			if(isset($post['typeId']) && $acl->sruAdmin('admin', 'advancedEdit')) //bo chyba advancedEdit nie jest nigdzie przy zapisie sprawdzany indziej
			{
				$bean->typeId = $post['typeId'];	
			}	
			if(isset($post['active']) && $acl->sruAdmin('admin', 'advancedEdit'))
			{
				$bean->active = $post['active'];	
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
