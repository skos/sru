<?

/**
 * wysukiwanie uzytkownika
 */
class UFact_SruAdmin_User_Search
extends UFact {

	const PREFIX = 'userSearch';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			$bean = UFra::factory('UFbean_Sru_User');

			$finds = array();
			if (isset($post['name']) && !empty($post['name'])) {
				$finds[] = 'name:'.urlencode($post['name']);
			}
			if (isset($post['login']) && !empty($post['login'])) {
				$finds[] = 'login:'.urlencode($post['login']);
			}
			if (isset($post['surname']) && !empty($post['surname'])) {
				$finds[] = 'surname:'.urlencode($post['surname']);
			}
			if (count($finds)) {
				UFra::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/users/search/'.implode('/', $finds));
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
