<?php

/**
 * cache plikowy
 */
class  UFlib_CacheFile
extends UFlib_Cache {

	protected $modified = false;

	public function set($var, $val, $ttl=10) {
		parent::set($var, $val, $ttl);
		$this->modified = true;
	}

	public function del($var) {
		parent::del($var);
		$this->modified = true;
	}

	public function restore($file) {
		$file = UFDIR_APP.'var/cache/'.$file;
		if (file_exists($file)) {
			$this->data = unserialize(file_get_contents($file));
			return true;
		} else {
			return false;
		}
	}

	public function store($file) {
		if (!$this->modified) {
			return true;
		}
		var_dump(getcwd());
		$file = UFDIR_APP.'var/cache/'.$file;
		return (bool)file_put_contents($file, serialize($this->data));
	}
}
