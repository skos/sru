<?php
/**
 * logi ufry
 */
class UFdao_Core_Log
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		return UFra::logs();
	}
}
