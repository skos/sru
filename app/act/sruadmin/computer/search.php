<?

/**
 * wysukiwanie komputera
 */
class UFact_SruAdmin_Computer_Search
extends UFact {

	const PREFIX = 'computerSearch';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};

			$bean = UFra::factory('UFbean_Sru_Computer');

			$finds = array();
			if (isset($post['host']) && !empty($post['host'])) {
				$val = urlencode($post['host']);
				$finds[] = 'host:'.$val;
			}
			if (isset($post['mac']) && !empty($post['mac'])) {
				$finds[] = 'mac:'.urlencode($post['mac']);
			}
			if (isset($post['ip']) && !empty($post['ip'])) {
				$finds[] = 'ip:'.urlencode($post['ip']);
			}
			if (count($finds)) {
				UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/computers/search/'.implode('/', $finds));
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
