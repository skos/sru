<?

/**
 * edycja danych wlasnego komputera
 */
class UFact_Sru_Computer_Edit
extends UFact {

	const PREFIX = 'computerEdit';

	public function go() {
		try {
			$bean = UFra::factory('UFbean_Sru_Computer');
			$bean->getByUserIdPK((int)$this->_srv->get('session')->auth, (int)$this->_srv->get('req')->get->computerId);
			$bean->fillFromPost(self::PREFIX, null, array('mac', 'availableTo'));
			if ($bean->availableTo > $bean->availableMaxTo) {
				$bean->availableTo = $bean->availableMaxTo;
			}
			if ($bean->availableTo < NOW) {
				$bean->availableTo = NOW;
			}
			if (!$bean->active && $bean->availableTo > NOW) {
				// przywrocenie aktywnosci komputera, jezeli podano
				// przyszla date waznosci rejestracji
				$bean->active = true;
				$bean->availableMaxTo = $bean->availableTo;
				//aktualizacja lokalizacji komputera
				//$user = UFra::factory('UFbean_Sru_User');
				//$user->getByPK($bean->userId);
				//$bean->locationId = $user->locationId;
			}
			if ($bean->availableTo <= NOW) {
				$bean->active = false;
			}
			$bean->modifiedById = null;
			$bean->modifiedAt = NOW;
			$bean->save();

			$user = UFra::factory('UFbean_Sru_User');
			$user->getByPK($bean->userId);

			$conf = UFra::shared('UFconf_Sru');
			if ($conf->sendEmail) {
				// wyslanie maila do usera
				$box = UFra::factory('UFbox_Sru');
				$bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
				$title = $box->hostChangedMailTitle($bean);
				$body = $box->hostChangedMailBody($bean, self::PREFIX);
				$headers = $box->hostChangedMailHeaders($bean);
				mail($user->email, '=?UTF-8?B?'.base64_encode($title).'?=', $body, $headers);
			}

			$this->postDel(self::PREFIX);
			$this->markOk(self::PREFIX);
		} catch (UFex_Dao_DataNotValid $e) {
			$this->markErrors(self::PREFIX, $e->getData());
		} catch (UFex $e) {
			UFra::error($e);
		}
	}
}
