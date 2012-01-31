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
			foreach ($post as &$tmp) {
				$tmp = trim($tmp);
			}
			
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
			if (isset($post['registryNo']) && !empty($post['registryNo'])) {
				$finds[] = 'registryNo:'.urlencode(mb_strtolower($post['registryNo'], 'UTF-8'));
			}
			if (isset($post['email']) && !empty($post['email'])) {
				$finds[] = 'email:'.urlencode(mb_strtolower($post['email'], 'UTF-8'));
			}
			if (isset($post['room']) && !empty($post['room'])) {
				$finds[] = 'room:'.urlencode(preg_replace('/[^a-z0-9*]/', '', mb_strtolower($post['room'], 'UTF-8')));
			}
			if (isset($post['dormitory']) && !empty($post['dormitory'])) {
				$finds[] = 'dormitory:'.urlencode(mb_strtolower($post['dormitory'], 'UTF-8'));
			}
			if (isset($post['typeId']) && !empty($post['typeId'])) {
				$finds[] = 'typeId:'.urlencode(mb_strtolower($post['typeId'], 'UTF-8'));
			}
			if (isset($post['active']) && $post['active'] == true) {
				$finds[] = 'active:' . true;
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
