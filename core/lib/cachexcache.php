<?php

/**
 * cache uzywajacy XCache
 */
class  UFlib_CacheXcache
extends UFlib_Cache {

	public function set($var, $val, $ttl=60) {
		xcache_set($var, $val, $ttl);
	}

	public function del($var) {
		xcache_unset($var);
	}

	public function is($var) {
		return xcache_isset($var);
	}

	public function get($var) {
		if (!$this->is($var)) {
			throw UFra::factory('UFex_Core_DataNotFound', 'Data key "'.$var.'" not found');
		}
		return xcache_get($var);
	}
}
