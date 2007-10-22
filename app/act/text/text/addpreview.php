<?

/**
 * podglad dodania strony tekstowej
 */
class UFact_Text_Text_AddPreview
extends UFact {

	const PREFIX = 'textAdd';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Text_Text');
			$bean->fillFromPost(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		}
	}
}
