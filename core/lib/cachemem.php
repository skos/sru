<?php

/**
 * cache uzywajacy serwera memcached
 */
class  UFlib_CacheMem
extends UFlib_Cache {

	/**
	 * obiekt serwera cache'u
	 */
	protected $cache;
	protected $cached = false;

	public function __construct() {
		parent::__construct();
		$this->cache = new Memcache;
		$conf = UFra::shared('UFconf_Cache');
		if (!$this->cache->connect($conf->host, $conf->port)) {
			UFra::error('Not connected to the cache '.$conf->host.':'.$conf->port);
		} else {
			$this->cached = true;
		}
	}

	public function set($var, $val, $ttl=60) {
		if ($this->cached) {
			$this->cache->set($var, $val, 0, $ttl);
		}
		parent::set($var, $val, $ttl);
	}

	public function del($var) {
		if ($this->cached) {
			$this->cache->delete($var);
		}
		parent::del($var);
	}

	public function is($var) {
		if (!$this->cached) {
			return parent::is($var);
		}
		$is = parent::is($var);
		if ($is) {
			return true;
		}
		// nie ma w cache'u lokalnym - pobranie z serwera cache'u
		$tmp = $this->cache->get($var);
		if (false === $tmp) {
			return false;
		} else {
			parent::set($var, $tmp);
			return true;
		}
	}
}
