<?php
/**
 * rzutowanie danych na inne typy
 */
class UFlib_DaoNormalizer {

	public function fill2dao($data, $type) {
		switch ($type) {
			case UFmap::NULL_REAL:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
			case UFmap::REAL:
				if (is_float($data)) {
					break;
				}
				$data = (float)$data;
				break;
			case UFmap::NULL_BOOL:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
			case UFmap::BOOL:
				if (is_bool($data)) {
					return $data;
				}
				if ('f' === $data) {
					$data = false;
				} elseif ('t' === $data) {
					$data = true;
				} else {
					$data = (bool)$data;
				}
				//$data = ('f'===$data?false:true);
				break;
			case UFmap::NULL_DATE:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
			case UFmap::DATE:
				if (is_int($data)) {
					break;
				}
				$data = strtotime($data);
				if (false === $data) {
					$data = '';
				}
				break;
			case UFmap::NULL_TS:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
			case UFmap::TS:
				if (is_int($data)) {
					break;
				}
				$data = (int)strtotime((string)$data);
				break;
			case UFmap::NULL_INT:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
			case UFmap::INT:
				if (is_int($data)) {
					break;
				}
				$data = (int)$data;
				break;
			case UFmap::NULL_TEXT:
				if (is_null($data)) {
					break;
				}
				if ('' === $data) {
					$data = null;
					break;
				}
		}
		return $data;
	}

	public function db2daoRow(array $data, array $types) {
		foreach ($data as $key=>&$val) {
			switch ($types[$key]) {
				case UFmap::NULL_DATE:
				case UFmap::NULL_TS:
					if ('' === $val || is_null($val)) {
						$val = null;
						break;
					}
				case UFmap::DATE:
				case UFmap::TS:
					$val = (int)$val;
					break;
				default:
					$val = $this->fill2dao($val, $types[$key]);
			}
		}
		return $data;
	}
}
