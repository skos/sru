<?

/**
 * wysukiwanie urządzenia
 */
class UFact_SruAdmin_InventoryCard_Search
extends UFact {

	const PREFIX = 'inventoryCardSearch';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			foreach ($post as &$tmp) {
				$tmp = trim($tmp);
			}

			$finds = array();
			if (isset($post['inventoryNo']) && !empty($post['inventoryNo'])) {
				$finds[] = 'inventoryNo:'.strtolower(urlencode($post['inventoryNo']));
			}
			if (isset($post['serialNo']) && !empty($post['serialNo'])) {
				$finds[] = 'serialNo:'.strtolower(urlencode(str_replace('/', '\\', $post['serialNo'])));
			}
			if (isset($post['dormitory']) && !empty($post['dormitory'])) {
				$finds[] = 'dormitory:'.urlencode(mb_strtolower($post['dormitory'], 'UTF-8'));
			}
			
			if (count($finds)) {
				UFlib_Http::redirect(UFURL_BASE.'/'.implode('/', $this->_srv->get('req')->segments(0)).'/inventory/search/'.implode('/', $finds));
			}
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
