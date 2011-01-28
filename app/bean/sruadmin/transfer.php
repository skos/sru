<?
/**
 * transfer
 */
class UFbean_SruAdmin_Transfer
extends UFbeanList {
	public function getBytesSum() {
		return $this->data[0]['bytes_sum'];
	}
	public function getBytesMin() {
		return $this->data[0]['bytes_min'];
	}
	public function getBytesMax() {
		return $this->data[0]['bytes_max'];
	}
}
