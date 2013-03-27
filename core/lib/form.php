<?php
/**
 * klasa tworzaca formularze
 */
class UFlib_Form
extends UFlib_ClassWithService {

	/**
	 * prefix danych w POST
	 */
	protected $postPrefix;

	/**
	 * dane do wyswietlenia w formularzu
	 */
	protected $data;

	/**
	 * komunikaty bledow do wyswietlenia
	 */
	protected $errors;

	/**
	 * template
	 */
	protected $form;

	/**
	 * typy pol i odpowiadajace im suffiksy metod
	 * 
	 * @var array
	 * @access protected
	 */
	protected $consts = array(
		'PASSWORD' => 'Password',
		'CHECKBOX' => 'Checkbox',
		'TEXTAREA' => 'Area',
		'SELECT'   => 'Select',
		'RADIO'    => 'Radio',
		'FILE'     => 'File',
		'HIDDEN'   => 'Hidden',
	);

	/**
	 * @param string/null $postPrefix - prefix danych w POST
	 * @param array $data - dane do wyswietlenia
	 * @param array $errors - komunikaty bledow dla wszystkich pol
	 * @param mixed $srv - uslugi frameworka
	 */
	public function __construct($postPrefix=null, array $data=array(), array $errors=array(), &$srv=null) {
		parent::__construct($srv);
		$this->form = $this->chooseTemplate();

		$this->postPrefix = $postPrefix;

		try {
			if (is_string($postPrefix)) {
				$post = $this->_srv->get('req')->post->$postPrefix;
				if (is_array($post)) {
					foreach ($post as &$d) {
						$d = stripslashes($d);
					}
					$data = $post + $data;
				}
			}
		} catch (UFex $e) {
		}
		$this->data = $data;
		
		try {
			$msg = $this->_srv->get('msg');
			foreach ($errors as $id=>$error) {
				if ($msg->get($postPrefix.'/errors/'.$id)) {
					$id = explode('/', $id, 2);
					$id = $id[0];
					$this->errors[$id] = $error;
				}
			}
		} catch (UFex $e) {
			$this->errors = array();
		}
	}

	public function __get($val) {
		return $this->consts[$val];
	}

	public function __call($method, $params) {
		$label = $params[0];
		if (isset($params[1]) && is_array($params[1])) {
			$params = $params[1];
		} else {
			$params = array();
		}
		if (!isset($params['id'])) {
			$params['id'] = $this->postPrefix.'_'.$method;
		}
		if (!isset($params['name'])) {
			$params['name'] = $this->postPrefix.'['.$method.']';
		}
		if (!isset($params['value'])) {
			if (array_key_exists($method, $this->data)) {
				$params['value'] = $this->data[$method];
			} else {
				$params['value'] = '';
			}
		}
		if (isset($this->errors[$method])) {
			$params['msgError'] = $this->errors[$method];
		}
		if (!isset($params['type'])) {
			$type = 'Text';
		} else {
			$type = $params['type'];
		}
		$params['label'] = $label;
		return call_user_func_array(array($this->form, 'input'.$type), array($params));
	}
	
	/**
	 * wybor template'u
	 * 
	 * @return UFtpl - template
	 */
	protected function chooseTemplate() {
		return UFra::shared('UFtpl_Form');
	}

	/**
	 * rozpoczyna formularz
	 * 
	 * @param string $action - akcja
	 * @param array $params - dodatkowe dane
	 */
	public function _start($action=null, array $params=array()) {
		if (is_string($action)) {
			$params['action'] = $action;
		}
		return $this->form->formStart($params);
	}

	/**
	 * rozpoczyna fieldset
	 * 
	 * @param string $legend - legenda
	 * @param array $params - dodatkowe parametry
	 */
	public function _fieldset($legend=null, array $params=array()) {
		if (is_string($legend)) {
			$params['legend'] = $legend;
		}
		return $this->form->fieldsetStart($params);
	}

	/**
	 * konczy fieldset lub caly formularz
	 * 
	 * @param bool $endForm - zakonczyc formularz?
	 */
	public function _end($endForm=false) {
		if ($endForm) {
			return $this->form->formEnd();
		} else {
			return $this->form->fieldsetEnd();
		}
	}

	/**
	 * przycisk submit
	 * 
	 * @param string $label - napis na przycisku
	 * @param array $params - dodatkowe parametry
	 */
	public function _submit($label, array $params=array()) {
		$params['value'] = $label;
		return $this->form->submit($params);
	}
	
	/**
	 * reorganizuje tablice danych do uzycia z input radio lub selectem
	 * 
	 * @param array $src - dane w formacie $value=>$label
	 * @param string/int $value - domyslna wartosc (pierwszy na liscie)
	 * @param string $label - domyslny label (pierwszy na liscie)
	 * @return array - zreorganizowane dane
	 */
	public function _labelize(array $src, $value=null, $label=null) {
		$tmp = array();
		if (is_string($label) && (is_int($value) || is_string($value))) {
			$tmp[] = array('value'=>$value, 'label'=>$label);
		}
		foreach ($src as $id=>$t) {
			$tmp[] = array('value'=>$id, 'label'=>$t);
		}
		return $tmp;
	}
}
