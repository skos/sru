<?

/**
 * wyslanie wiadomosci do Admina
 */
class UFact_Sru_User_SendMessage extends UFact {

	const PREFIX = 'sendMessage';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			$bean = UFra::factory('UFbean_Sru_User');
			$bean->getFromSession();
			
			if (!isset($post['message']) || $post['message'] == '') {
				throw UFra::factory('UFex_Dao_DataNotValid', 'No message', 0, E_WARNING, array('message' => 'notEmpty'));
			}
			$otrs = UFra::factory('UFlib_Otrs');
			$otrs->sendMessage($bean, htmlspecialchars($post['message']));

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}

}
