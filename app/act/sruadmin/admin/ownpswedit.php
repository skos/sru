<?php
/**
 * edycja hasła administratora skosu
 */
class UFact_SruAdmin_Admin_OwnPswEdit
extends UFact {

	const PREFIX = 'adminOwnPswEdit';

	public function go() {
		$post = $this->_srv->get('req')->post->{self::PREFIX};
		try {
			$bean = UFra::factory('UFbean_SruAdmin_Admin');
			$bean->getByPK((int) $this->_srv->get('req')->get->adminId);

			if (isset($post['password']) && $post['password'] != '') {
				$bean->password = $post['password'];
				$bean->lastPswChange = NOW;
				$bean->badLogins = 0;
			}

			$bean->modifiedById = $this->_srv->get('session')->authAdmin;
			$bean->modifiedAt = NOW;

			$bean->save();
			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
			UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments()));
		} catch (UFex_Dao_DataNotValid $ex) {
			$this->markErrors(self::PREFIX, $ex->getData());
		} catch (Exception $e) {
			UFra::error($e);
		}
	}
}