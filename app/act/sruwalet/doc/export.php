<?php
/**
 * eksport danych DSu
 */
class UFact_SruWalet_Doc_Export
extends UFact {

	const PREFIX = 'docExport';

	public function go() {
		try {
			$post = $this->_srv->get('req')->post->{self::PREFIX};
			if ($post['docTypeId'] == '1') {
				$docCode = 'Dorm';
			} else if ($post['docTypeId'] == '2') {
				$docCode = 'DormUsers';
			} else if ($post['docTypeId'] == '3') {
				$docCode = 'DormRegBook';
			} else {
				$docCode = null;
			}
			
			if (!is_null($docCode)) {
				if ($post['formatTypeId'] == '2') {
					$docCode .= 'Xls';
				} else {
					$docCode .= 'Doc';
				}
			}
			
			$this->_srv->get('req')->get->docCode = $docCode;
			
			$this->_srv->get('req')->get->addFaculty = $post['addFaculty'];
			$this->_srv->get('req')->get->addYear = $post['addYear'];

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
