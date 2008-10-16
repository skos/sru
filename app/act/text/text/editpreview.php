<?

/**
 * podglad edycji strony tekstowej
 */
class UFact_Text_Text_EditPreview
extends UFact {

	const PREFIX = 'textEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Text_Text');
			$bean->fillFromPost(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
