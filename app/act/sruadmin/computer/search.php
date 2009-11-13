<?

/**
 * wysukiwanie komputera
 */
class UFact_SruAdmin_Computer_Search
extends UFact {

	const PREFIX = 'computerSearch';

	public function go() {
		echo "kurwa co jest";
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			foreach ($post as &$tmp) {
				$tmp = trim($tmp);
			}

			$bean = UFra::factory('UFbean_Sru_Computer');

			$finds = array();
			if (isset($post['host']) && !empty($post['host'])) {
				$val = urlencode($post['host']);
				$master_exploder = explode('.', $val);
				$finds[] = 'host:'.$master_exploder[0];
				

			}
			if (isset($post['mac']) && !empty($post['mac'])) {
				$finds[] = 'mac:'.urlencode($post['mac']);
			}
			if (isset($post['ip']) && !empty($post['ip'])) {
				$finds[] = 'ip:'.urlencode($post['ip']);
			}
			if (count($finds)) {
				
				UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/computers/search/'.implode('/', $finds));
				print "dasd " . $this->_srv->get('req')->segments(0);
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
