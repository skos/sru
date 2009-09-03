<?
/**
 * wszystkie dostępne usługi 
 */
class UFdao_Sru_Service
extends UFdao {
	
	public function listAllServices($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
			
		return $this->doSelect($query);
	}

}
