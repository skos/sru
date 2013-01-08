<?php
abstract class UFact
extends UFlib_ClassWithService {
	
	/**
	 * uruchomienie funkcjonalnosci klasy
	 */
	abstract public function go();

	public function __construct(Lib_Service &$srv = null) {
		parent::__construct($srv);
		$this->chooseDb();
		$this->req = $this->_srv->get('req');
	}

	/**
	 * wybor olaczenia do bazy
	 */
	protected function chooseDb() {
		$this->db = $this->_srv->get('db');
	}

	/**
	 * rozpoczecie transakcji
	 */
	protected function begin() {
		$this->db->begin();
	}

	/**
	 * zatwierdzenie transakcji
	 */
	protected function commit() {
		$this->db->commit();
	}

	/**
	 * wycofanie transakcji
	 */
	protected function rollback() {
		$this->db->rollback();
	}

	/**
	 * sygnalizuje bledy
	 * 
	 * @param string $prefix - prefix message'ow
	 * @param array $errors - bledy
	 */
	public function markErrors($prefix, array $errors=array()) {
		$msg =& $this->_srv->get('msg');
		$msg->set($prefix.'/errors');
		foreach ($errors as $err=>$data) {
			$msg->set($prefix.'/errors/'.$err.'/'.$data);
			UFra::debug('Not valid: '.$prefix.'/'.$err.'/'.$data);
		}
	}

	/**
	 * sygnalizuje bledy przy nastepnym wywolaniu strony
	 * 
	 * @param string $prefix - prefix message'ow
	 * @param array $errors - bledy
	 */
	public function markNextErrors($prefix, array $errors=array()) {
		$msg =& $this->_srv->get('msgNext');
		$msg->set($prefix.'/errors');
		foreach ($errors as $err=>$data) {
			$msg->set($prefix.'/errors/'.$err.'/'.$data);
			UFra::debug('Not valid: '.$prefix.'/'.$err.'/'.$data);
		}
	}

	/**
	 * sygnalizuje, ze akcja sie udala
	 * 
	 * @param string $prefix - prefix message'ow
	 */
	public function markOk($prefix) {
		$this->_srv->get('msg')->set($prefix.'/ok');
	}

	/**
	 * kasuje dane z POST
	 * 
	 * @param string $prefix - prefix do skasowania
	 */
	public function postDel($prefix) {
		$this->_srv->get('req')->post->del($prefix);
	}

	/**
	 * zasygnalizuje w nastepnym wywolaniu strony, ze akcja sie udala
	 * 
	 * @param string $prefix - prefix message'ow
	 */
	public function markNextOk($prefix) {
		$this->_srv->get('msgNext')->set($prefix.'/ok');
	}

	/**
	 * adres do aktualnej strony
	 * 
	 * @param int/null $number - ile pierwszych czlonow adresu brac pod uwage; null - wszystkie
	 * @return string
	 */
	protected function url($number=null) {
		$segments = $this->_srv->get('req')->segments($number);
		return rtrim(UFURL_BASE.'/'.implode('/', $segments), '/');
	}
}
