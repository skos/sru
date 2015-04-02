<?
/**
 * aliasy komputerow
 */
class UFbean_SruAdmin_ComputerAliasList
extends UFbeanList {
	public function search($host) {
		$this->data = $this->dao->search($host);
	}
}
