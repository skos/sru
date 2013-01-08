<?php
/**
 * dostep do bazy redis
 */
class UFlib_Db_DriverRedis {
    
    /**
     * polaczenie do bazy
     */
    protected $connection = null;

    /**
     * adres
     */
    protected $host;

    /**
     * numer portu
     */
    protected $port;

	/**
	 * czy automatycznie nawiazywac polaczenie
	 */
	protected $autoconnect = false;

 
    public function __construct($params) {
		if (isset($params['host']) && is_string($params['host']) &&
			isset($params['port']) && is_int($params['port'])) {
			$this->host = $params['host'];
			$this->port = $params['port'];
			
			if (isset($params['autoconnect']) && (true === $params['autoconnect'])) {
				$this->autoconnect = true;
			} else {
				$this->connect($this->host, $this->port);
			}
		} else {
			throw new UFex_Db_BadConfig('Bad DB config');
		}
    }

	/**
	 * automatycznie nawiazuje polaczenie, jezeli go nie ma
	 *
	 * @throws UFex_Db_NotConnected - autoconnect wylaczony i nie ma polaczenia
	 */
	protected function autoconnect() {
		if (!$this->connection) {
			if (true === $this->autoconnect) {
				$this->connect($this->host, $this->port);
			} else {
				throw new UFex_Db_NotConnected('Not connected (no autoconnection)', 0, E_ERROR);
			}
		}
	}
    
    /**
     * nawiazuje polaczenie z baza
     * 
     * @param string $host - nazwa hosta
     * @param int $port - numer portu
     * @return void
     */
    public function connect($host='127.0.0.1', $port=6379) {
        if ($this->connection) {
            return;
		}
		UFra::debug('DB connect...');
		$this->connection = fsockopen($this->host, $this->port, $errno, $errstr);
		if (false === $this->connection) {
			throw new UFex_Db_NotConnected('Could not connect to database server: '.$this->host.':'.$this->port.' ('.$errno.' - '.$errstr.')', 0, E_ERROR);
		}
		UFra::debug('...DB connect');
    }
    
    /**
     * rozlaczenie
     */
    public function disconnect() {
        if ($this->connection) {
            fclose($this->connection);
		}
        $this->connection = null;
    }
    
    /**
     * sprawdza polaczenie
     */
    public function ping() {
        $this->autoconnect();
        $this->_write("PING\r\n");
        return $this->_getResponse();
    }
    
    public function doEcho($s) {
        $this->autoconnect();
        $this->_write("ECHO " . strlen($s) . "\r\n$s\r\n");
        return $this->_getValue();
    }
    
    public function set($name, $value, $preserve=false) {
        $this->autoconnect();
        $this->_write(($preserve?'SETNX':'SET')." $name ".strlen($value)."\r\n$value\r\n");
        if ('OK' == $this->_getResponse()) {
			return 1;
		} else {
			return 0;
		}
    }
    
    public function get($name) {
        $this->autoconnect();
        $this->_write("GET $name\r\n");
        return $this->_getValue();
    }
    
    public function mget(array $names) {
        $this->autoconnect();
        $this->_write("MGET ".implode(' ', $names)."\r\n");
        return $this->_getResponse();
    }
    
    public function incr($name, $amount=1) {
        $this->autoconnect();
		fwrite($this->connection, "INCRBY $name $amount\r\n");
        return $this->_getResponse();
    }
    
    public function decr($name, $amount=1) {
        $this->autoconnect();
		$this->_write("DECRBY $name $amount\r\n");
        return $this->_getResponse();
    }
    
    public function exists($name) {
        $this->autoconnect();
        $this->_write("EXISTS $name\r\n");
        return $this->_getResponse();
    }
    
    public function delete($name) {
        $this->autoconnect();
        $this->_write("DEL $name\r\n");
        return $this->_getResponse();
    }
    
    public function keys($pattern) {
        $this->autoconnect();
        $this->_write("KEYS $pattern\r\n");
        return explode(' ', $this->_getValue());
    }
    
    public function randomkey() {
        $this->autoconnect();
        $this->_write("RANDOMKEY\r\n");
        return $this->_getResponse();
    }
    
    public function rename($src, $dst, $preserve=False) {
        $this->autoconnect();
		if ($preserve) {
			$this->_write("RENAMENX $src $dst\r\n");
		} else {
			$this->_write("RENAME $src $dst\r\n");
		}
        return $this->_getResponse();
    }
    
    public function expire($name, $time) {
        $this->autoconnect();
        $this->_write("EXPIRE $name $time\r\n");
        return $this->_getResponse();
    }
    
    public function push($name, $value, $tail=true) {
        // default is to append the element to the list
        $this->autoconnect();
		if ($tail) {
			$this->_write("RPUSH $name ".strlen($value)."\r\n$value\r\n");
		} else {
			$this->_write("LPUSH $name ".strlen($value)."\r\n$value\r\n");
		}
        return $this->_getResponse();
    }
    
    public function ltrim($name, $start, $end) {
        $this->autoconnect();
        $this->_write("LTRIM $name $start $end\r\n");
        return $this->_getResponse();
    }
    
    public function lindex($name, $index) {
        $this->autoconnect();
        $this->_write("LINDEX $name $index\r\n");
        return $this->_getValue();
    }
    
    public function pop($name, $tail=true) {
        $this->autoconnect();
		if ($tail) {
			$this->_write("RPOP $name\r\n");
		} else {
			$this->_write("LPOP $name\r\n");
		}
        return $this->_getValue();
    }

	public function lrem($name, $value, $count=1) {
		$this->autoconnect();
		$this->_write("LREM $name $count ".strlen($value)."\r\n$value\r\n");
        return $this->_getResponse();
	}
    
    public function llen($name) {
        $this->autoconnect();
        $this->_write("LLEN $name\r\n");
        return $this->_getResponse();
    }
    
    public function lrange($name, $start, $end) {
        $this->autoconnect();
        $this->_write("LRANGE $name $start $end\r\n");
        return $this->_getResponse();
    }

    public function sort($name, $query=false) {
        $this->autoconnect();
		if (false === $query) {
			$this->_write("SORT $name\r\n");
		} else {
			$this->_write("SORT $name $query\r\n");
		}
        return $this->_getResponse();
    }
    
    public function lset($name, $value, $index) {
        $this->autoconnect();
        $this->_write("LSET $name $index ".strlen($value)."\r\n$value\r\n");
        return $this->_getResponse();
    }
    
    public function sadd($name, $value) {
        $this->autoconnect();
        $this->_write("SADD $name ".strlen($value)."\r\n$value\r\n");
        return $this->_getResponse();
    }
    
    public function srem($name, $value) {
        $this->autoconnect();
        $this->_write("SREM $name ".strlen($value)."\r\n$value\r\n");
        return $this->_getResponse();
    }
    
    public function sismember($name, $value) {
        $this->autoconnect();
        $this->_write("SISMEMBER $name ".strlen($value)."\r\n$value\r\n");
        return $this->_getResponse();
    }
    
    public function sinter(array $sets) {
        $this->autoconnect();
        $this->_write('SINTER '.implode(' ', $sets)."\r\n");
        return $this->_getResponse();
    }
    
    public function smembers($name) {
        $this->autoconnect();
        $this->_write("SMEMBERS $name\r\n");
        return $this->_getResponse();
    }

    public function scard($name) {
        $this->autoconnect();
        $this->_write("SCARD $name\r\n");
        return $this->_getResponse();
    }
    
    public function selectDb($name) {
        $this->autoconnect();
        $this->_write("SELECT $name\r\n");
        return $this->_getResponse();
    }
    
    public function move($name, $db) {
        $this->autoconnect();
        $this->_write("MOVE $name $db\r\n");
        return $this->_getResponse();
    }
    
    public function save($background=false) {
        $this->autoconnect();
		if ($background) {
			$this->_write("BGSAVE\r\n");
		} else {
			$this->_write("SAVE\r\n");
		}
        return $this->_getResponse();
    }
    
    public function lastsave() {
        $this->autoconnect();
        $this->_write("LASTSAVE\r\n");
        return $this->_getResponse();
    }
    
    public function flush($all=false) {
        $this->autoconnect();
		if ($all) {
			$this->_write("FLUSH\r\n");
		} else {
			$this->_write("FLUSHDB\r\n");
		}
        return $this->_getResponse();
    }
    
    public function info() {
        $this->autoconnect();
        $this->_write("INFO\r\n");
        $info = array();
        $data = $this->_getResponse();
        foreach (explode("\r\n", $data) as $l) {
            if (!$l) {
                continue;
			}
            list($k, $v) = explode(':', $l, 2);
            $_v = strpos($v, '.') !== false ? (float)$v : (int)$v;
            $info[$k] = (string)$_v == $v ? $_v : $v;
        }
        return $info;
    }
    
    protected function _write($s) {
		fwrite($this->connection, $s);
    }
    
    protected function _read($len=1024) {
        if ($s = fgets($this->connection)) {
            return $s;
		}
        $this->disconnect();
        trigger_error("Cannot read from socket.", E_USER_ERROR);
    }
    
    protected function _getResponse() {
        $data = trim($this->_read());
        switch ($data[0]) {
            case '-':
				$data = substr($data, 1);
                trigger_error(substr($data, 0, 4) == 'ERR ' ? substr($data, 4) : $data, E_USER_ERROR);
                break;
            case '+':
                return substr($data, 1);
            case '*':
                $num = (int)substr($data, 1);
                $result = array();
                for ($i=0; $i<$num; ++$i) {
                    $result[] = $this->_getValue();
				}
                return $result;
            default:
                return $this->_getValue($data);
        }
    }
    
    protected function _getValue($data=null) {
        if ($data === null) {
            $data = trim($this->_read());
		}
        if ($data === '$-1') {
            return null;
		}
        $c = $data[0];
        $data = substr($data, 1);
        $i = strpos($data, '.') !== false ? (int)$data : (float)$data;
        if ($c === ':') {
            return $i;
		}
        if ($c !== '$') {
            trigger_error("Unkown response prefix for '$c$data'", E_USER_ERROR);
		}
        $buffer = '';
        while ($i >= 0) {
            $data = $this->_read();
            $i -= strlen($data);
            $buffer .= $data;
        }
        return substr($buffer, 0, -2);
    }
}   
