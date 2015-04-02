<?

/**
 * wyslanie wiadomosci do Admina
 */
class UFact_Sru_User_SendMessage extends UFact {

	const PREFIX = 'sendMessage';

	public function go() {
		try {
			$sess = $this->_srv->get('session');

			// wysyłamy tylko jeśli user nie wysłał przed chwilą (F5 issue, #757)
			if (!$sess->is('otrsMsgSend') || $sess->otrsMsgSend != 1) {
				$post = $this->_srv->get('req')->post->{self::PREFIX};
				$bean = UFra::factory('UFbean_Sru_User');
				$bean->getFromSession();

				if (!isset($post['message']) || $post['message'] == '') {
					throw UFra::factory('UFex_Dao_DataNotValid', 'No message', 0, E_WARNING, array('message' => 'notEmpty'));
				}
				$otrs = UFra::factory('UFlib_Otrs');
				$otrs->sendMessage($bean, htmlspecialchars($post['message']));

				$sess->otrsMsgSend = 1;
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			$this->markErrors(self::PREFIX, array());
		}
	}

}
