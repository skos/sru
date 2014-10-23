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
			foreach ($post as &$tmp) {
				$tmp = trim($tmp);
			}

			$bean = UFra::factory('UFbean_Sru_Computer');
                        
                        if(isset($post['mac'])) {
                                $post['mac'] = preg_replace('/[^a-zA-Z0-9:-]/', '', $post['mac']);
                        }

			$finds = array();
			if (isset($post['typeId']) && !empty($post['typeId'])) {
				$finds[] = 'typeId:'.urlencode($post['typeId']);
			}
			if (isset($post['host']) && !empty($post['host'])) {
				$val = urlencode($post['host']);
				$domain = stripos($val, 'ds.pg.gda.pl');
				if ($domain !== false) {
					$val = substr($val, 0, $domain - 1);
				}
				$finds[] = 'host:'.$val;
			}
			if (isset($post['mac']) && !empty($post['mac'])) {
				$finds[] = 'mac:'.urlencode($post['mac']);
			}
			if (isset($post['ip']) && !empty($post['ip'])) {
				$finds[] = 'ip:'.urlencode($post['ip']);
			}
			if (isset($post['computersActive']) && $post['computersActive'] == true) {
				$finds[] = 'computersActive:' . true;
			}
			
			if (count($finds)) {
				UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/computers/search/'.implode('/', $finds));
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
