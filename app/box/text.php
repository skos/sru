<?

/**
 * tekst
 */
class UFbox_Text
extends UFbox {
	
	/**
	 * uniwersalny podglad
	 * 
	 * @param string $prefix - gdzie w POST szukac danych
	 */
	protected function preview($prefix) {
		$bean = UFra::factory('UFbean_Text_Text');
		$bean->fillFromPost($prefix);
		$d['text'] = $bean;
		return $this->render(__FUNCTION__, $d);
	}

	protected function _getTextByAlias() {
		$bean = UFra::factory('UFbean_Text_Text');
		$bean->getByAlias($this->_srv->get('req')->get->alias);

		return $bean;
	}

	/**
	 * podglad przy dadawaniu
	 */
	public function addPreview() {
		try {
			return $this->preview('textAdd');
		} catch (UFex $e) {
			return '';
		}
	}

	/**
	 * dodawanie
	 */
	public function add() {
		$bean = UFra::factory('UFbean_Text_Text');

		$d['text'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	/**
	 * lista stron
	 */
	public function listShort() {
		$bean = UFra::factory('UFbean_Text_TextList');
		try {
			$bean->listAlphabetically();
			$d['texts'] = $bean;
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function title() {
		try {
			$d['text'] = $this->_getTextByAlias();
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render(__FUNCTION__.'NotFound');
		}
	}

	public function show() {
		try {
			$d['text'] = $this->_getTextByAlias();
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('textNotFound');
		}
	}

	public function adminList() {
		$bean = UFra::factory('UFbean_Text_TextList');
		try {
			$bean->listAlphabetically();

			$d['texts'] = $bean;

			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->add();
		}
	}

	public function edit() {
		try {
			$d['text'] = $this->_getTextByAlias();
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('textNotFound');
		}
	}

	/**
	 * podglad przy edycji
	 */
	public function editPreview() {
		try {
			return $this->preview('textEdit');
		} catch (UFex $e) {
			return '';
		}
	}

	public function delete() {
		try {
			$d['text'] = $this->_getTextByAlias();
			return $this->render(__FUNCTION__, $d);
		} catch (UFex_Dao_NotFound $e) {
			return $this->render('textNotFound');
		}
	}
}
