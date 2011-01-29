<?
/**
 * wspólny bean
 */
class UFbean_Common
extends UFbeanSingle {

	private $alwaysChanged = array (
		'modifiedAt' => '',
		'modifiedById' => '',
		'updateNeeded' => '',
		'changePasswordNeeded' => '',
	);

	/**
	 * zapisz aktualne dane
	 * nadpisuje metodę UFbeanSingle - sprawdza, czy jest co zapisać i wywołuje metodę nadpisywaną
	 *
	 * @param bool $lastVal - czy pobierac wartosc autoincrement, gdy dodawana jest nowa wartosc?
	 * @return int - idengyfikator nowododanego rekordu lub ilosc
	 *               zmodyfikowanych rekordow w zaleznosci od tego, skad
	 *               pochodzily dane
	 */
	public function save($lastVal = true) {
		if(count(array_diff_key($this->dataChanged, $this->alwaysChanged)) > 0) {
			return UFbeanSingle::save($lastVal);
		}
	}
}
