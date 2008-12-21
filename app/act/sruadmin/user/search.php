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
				$finds[] = 'name:'.urlencode(mb_strtolower($post['name'], 'UTF-8'));
			}
			if (isset($post['login']) && !empty($post['login'])) {
				$finds[] = 'login:'.urlencode(mb_strtolower($post['login'], 'UTF-8'));
			}
			if (isset($post['surname']) && !empty($post['surname'])) {
				$finds[] = 'surname:'.urlencode(mb_strtolower($post['surname'], 'UTF-8'));
			}
			if (isset($post['email']) && !empty($post['email'])) {
				$finds[] = 'email:'.urlencode(mb_strtolower($post['email'], 'UTF-8'));
			}
			if (count($finds)) {
				UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/users/search/'.implode('/', $finds));
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
