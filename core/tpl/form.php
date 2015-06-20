<?php
/**
 * generator kodu formularzy
 */
class UFtpl_Form {

	/**
	 * sprawdza i przygotowuje parametry
	 * 
	 * @param array $params - parametry przekazane do funckji
	 * @param array $defaults - domyslne wartosci
	 * @return array - polaczone domyslne i zadane parametry
	 */
	protected static function _params(array $params, array $defaults) {
		return $params + $defaults;
	}

	/**
	 * parsuje standardowe html-owe parametry, ktore moga miec wyszstkie tagi
	 */
	protected static function _standardParams(array $params) {
		$html = array();

		// id
		if (isset($params['id']) && is_string($params['id'])) {
			$html[] = 'id="'.$params['id'].'"';
		}

		// class
		if (isset($params['class'])) {
			if (is_array($params['class'])) {
				$params['class'] = implode(' ', $params['class']);
			}
			if (is_string($params['class'])) {
				$html[] = 'class="'.$params['class'].'"';
			}
		}

		// laczymy wszystko
		$o = implode(' ', $html);
		if ('' !== $o) {
			return ' '.$o;
		}
	}
	
	/**
	 * rozpoczecie formularza
	 */
	public static function formStart(array $params=array()) {
		$defaults = array(
			'action'   => '',
			'method'   => 'post',
			'upload'   => false,
			'enctype'  => null,
		);
		$params = self::_params($params, $defaults);

		// ustawienie parametrow
		$html = array();

		// method
		if (('get' === $params['method']) || 'post' === $params['method']) {
			$html[] = 'method="'.$params['method'].'"';
		}

		// upload & encoding
		if (true === $params['upload']) {
			$html[] = 'enctype="multipart/form-data"';
		} elseif (is_string($params['enctype'])) {
			$html[] = 'enctype="'.$params['enctype'].'"';
		}

		return '<form action="'.(string)$params['action'].'" '.implode(' ', $html).self::_standardParams($params).'>'."\n";
	}

	/**
	 * zakonczenie formularza
	 */
	public static function formEnd() {
		return '</form>'."\n";
	}

	/**
	 * rozpoczecie grupy pol
	 */
	public static function fieldsetStart($params=array()) {
		$defaults = array(
			'legend'   => null,
			'msgError' => null,
		);
		$params = self::_params($params, $defaults);

		// dodanie klasy, gdy mamy blad formularza
		if (!is_null($params['msgError'])) {
			if (isset($params['class'])) {
				if (is_array($params['class'])) {
					$params['class'][] = 'formError';
				} else {
					$params['class'] .= ' formError';
				}
			} else {
				$params['class'] = 'formError';
			}
		}

		$o = '<fieldset'.self::_standardParams($params).'>'."\n";
		// legend
		if (is_string($params['legend'])) {
			$o .= '<legend>'.$params['legend'].'</legend>'."\n";
		}
		if (!is_null($params['msgError'])) {
			$o .= '<p class="msgError">'.$params['msgError'].'</p>'."\n";
		}
		return $o;
	}

	/**
	 * zakonczenie grupy pol
	 */
	public static function fieldsetEnd() {
		return '</fieldset>'."\n";
	}

	/**
	 * pole tekstowe
	 */
	public static function inputArea($params=array()) {
		$defaults = array(
			'name'  => '',
			'id'    => null,
			'value' => '',
			'label' => '---',
			'var'   => null,
			'rows'  => 15,
			'cols'  => 60,
			'msgError' => null,
			'after'    => '<br /> ',
			'before'   => '',
		);
		$params = self::_params($params, $defaults);

		if (!is_null($params['var'])) {
			$params['id'] .= (string)$params['var'];
		}

		$p = '';
		if (isset($params['rows']) && is_int($params['rows'])) {
			$p .= ' rows="'.$params['rows'].'"';
		}
		if (isset($params['cols']) && is_int($params['cols'])) {
			$p .= ' cols="'.$params['cols'].'"';
		}

		$oI = '<textarea name="'.$params['name'].'"'.$p.self::_standardParams($params).'>'.htmlspecialchars($params['value']).'</textarea>';
		if (is_string($params['label'])) {
			$oL = '<label';
			if (!is_null($params['id']) && is_string($params['id'])) {
				$oL .= ' for="'.$params['id'].'"';
			}
			$oL .= '>'.$params['label'].'</label>';
		} else {
			$oL = '';
		}
		$o = $oL.' '.$oI;
		if (!is_null($params['msgError'])) {
			$o .= ' <strong class="msgError">'.$params['msgError'].'</strong>';
		}
		return $params['before'].$o.$params['after'];
	}

	/**
	 * wszelkie inputy
	 */
	protected static function input($params=array()) {
		$defaults = array(
			'name'     => '',
			'id'       => null,
			'value'    => '',
			'var'      => null,
			'checked'  => null,
			'msgError' => null,
			'after'    => '<br /> ',
			'before'   => '',
			'disabled' => false,
			'readonly' => false,
			'maxlength' => null,
		);
		$params = self::_params($params, $defaults);

		if (!is_null($params['var'])) {
			$params['id'] .= (string)$params['var'];
		}
		if ('area' === $params['type']) {
			return self::textarea($params);
		}
		if ('radio' === $params['type']) {
			$tmp = htmlspecialchars($params['value']);
			$params['id'] .= '_'.$tmp;

			// zaznaczenie wybranego
			if ((string)$params['checked'] === (string)$params['value']) {
				$params['checked'] = true;
			} else {
				$params['checked'] = false;
			}
		}
		if ('checkbox' === $params['type'] && (!isset($params['noZero']) || !$params['noZero'])) {
			$params['before'] .= '<input type="hidden" value="0" name="'.$params['name'].'" />';
		}

		$p = '';

		if ('text' === $params['type'] && is_int($params['maxlength'])) {
			$p .= ' maxlength="'.$params['maxlength'].'"';
		}

		if (true === $params['checked']) {
			$p .= ' checked="checked"';
		}

		if (true === $params['readonly']) {
			$p .= ' readonly="readonly"';
		}

		if (true === $params['disabled']) {
			$p .= ' disabled="disabled"';
		}

		$oI = '<input name="'.$params['name'].'" type="'.$params['type'].'"';
		if (isset($params['value'])) {
			$oI .= ' value="'.htmlspecialchars($params['value']).'"';
		}
		$oI .= $p.self::_standardParams($params).' />';
		if (isset($params['label']) && is_string($params['label'])) {
			$oL = '<label';
			if (isset($params['id']) && is_string($params['id'])) {
				$oL .= ' for="'.$params['id'].'"';
			if (isset($params['labelClass']) && is_string($params['labelClass'])) {
				$oL .= ' class="'.$params['labelClass'].'"';
			}
			}
			$oL .= '>'.$params['label'].'</label>';
		} else {
			$oL = '';
		}

		switch($params['type']) {
			case 'submit':
				$o = $oI;
				break;
			case 'checkbox':
			case 'radio':
				$o = $oI.' '.$oL;
				break;
			default:
				$o = $oL.' '.$oI;
				break;
		}

		$noErrorable['submit'] = true;

		if (!is_null($params['msgError']) && !isset($noErrorable[$params['type']])) {
			if ('radio' !== $params['type'] || ('radio' === $params['type'] && $params['labels'][0]['value'] == $params['value'])) {
				$o .= ' <strong id="'.$params['id'].'Err" class="msgError">'.$params['msgError'].'</strong>';
			}
		}
		return $params['before'].$o.$params['after'];
	}

	/**
	 * haslo
	 */
	public static function inputPassword($params=array()) {
		$params['type'] = 'password';
		$params['value'] = '';
		return self::input($params);
	}

	/**
	 * input z kalendarzem
	 */
	public static function inputCalender($params=array()) {
		$params['labelClass'] = "datepicker" . (!empty($params['labelClass'])? " ".$params['labelClass'] : "");
		return self::input($params);
	}
	/**
	 * input z kalendarzem
	 */
	public static function inputTimeCalender($params=array()) {
		$params['labelClass'] = "datepicker" . (!empty($params['labelClass'])? " ".$params['labelClass'] : "");
		return self::input($params);
	}
	/**
	 * input tekstowy
	 */
	public static function inputHidden($params=array()) {
		$params['type'] = 'hidden';
		$params['after'] = '';
		$params['before'] = '';
		unset($params['label']);
		return self::input($params);
	}

	/**
	 * input tekstowy
	 */
	public static function inputText($params=array()) {
		$params['type'] = 'text';
		return self::input($params);
	}

	/**
	 * radiobutton
	 */
	public static function inputRadio($params=array()) {
		$params['type'] = 'radio';
		if (!isset($params['labels']) || !is_array($params['labels'])) {
			return self::input($params);
		}
		$params['checked'] = $params['value'];
		$legend = $params['label'];
		$labels = $params['labels'];
		$txt = '';
		foreach ($labels as $label) {
			$params['value'] = $label['value'];
			$params['label'] = $label['label'];
			$txt .= self::input($params);
		}

		return self::fieldsetStart(array('legend'=>$legend, 'class'=>'radio')).$txt.self::fieldsetEnd();
	}

	/**
	 * select
	 */
	public static function inputSelect($params=array()) {
		$defaults = array(
			'name'     => '',
			'id'       => null,
			'value'    => '',
			'label'    => '---',
			'labels'   => array(),
			'var'      => null,
			'checked'  => null,
			'msgError' => null,
			'after'    => '<br /> ',
			'before'   => '',
			'disabled' => false,
		);
		$params = self::_params($params, $defaults);
		$labels = $params['labels'];
		
		$txt = '';
		foreach ($labels as $label) {
			$array = is_array($params['value']);
			if (!$array && (string)$label['value'] === (string)$params['value']) {
				$checked = ' selected="selected"';
			} elseif ($array && in_array($label['value'], $params['value'])) {
				$checked = ' selected="selected"';
			} else {
				$checked = '';
			}
			$txt .= '<option'.$checked.' value="'.htmlspecialchars(stripslashes($label['value'])).'">'.$label['label'].'</option>';
		}

		$p = '';
		if (true === $params['disabled']) {
			$p .= ' disabled="disabled"';
		}
		if (!is_null($params['var'])) {
			$params['id'] .= (string)$params['var'];
		}
		$oI = '<select name="'.$params['name'].'"'.$p.self::_standardParams($params).'>'.$txt.'</select>';
		if (is_string($params['label'])) {
			$oL = '<label>'.$params['label'].'</label> ';
		} else {
			$oL = '';
		}
		$o = $oL.' '.$oI;
		if (!is_null($params['msgError'])) {
			$o .= ' <strong class="msgError">'.$params['msgError'].'</strong>';
		}

		return $params['before'].$o.$params['after'];
	}

	/**
	 * checkbox
	 */
	public static function inputCheckbox($params=array()) {
		if (true == $params['value']) {
			$params['checked'] = true;
		}
		$params['value'] = '1';
		$params['type'] = 'checkbox';
		$params['labelClass'] = 'checkbox';
		if (isset($params['class'])) {
			if (is_array($params['class'])) {
				$params['class'][] = 'checkbox';
			} else {
				$params['class'] = array($params['class'], 'checkbox');
			}
		} else {
			$params['class'] = 'checkbox';
		}
		return self::input($params);
	}

	/**
	 * pole wyboru pliku
	 */
	public static function inputFile($params=array()) {
		$params['type'] = 'file';
		unset($params['value']);
		return self::input($params);
	}

	/**
	 * przycisk wyslania formularza
	 */
	public static function submit($params=array()) {
		$defaults = array(
			'name'  => 'submit',
			'label' => null,
			'after' => '',
		);
		$params = self::_params($params, $defaults);
		$params['type'] = 'submit';
		if (isset($params['class']) && is_string($params['class'])) {
			$params['class'] .= ' submit';
		} else {
			$params['class'] = 'submit';
		}
		return self::input($params);
	}
}
