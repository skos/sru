<?
/**
 * token
 */
class UFbean_Sru_Token
extends UFbeanSingle {

	const CONFIRM = 0;	// token aktywujacy konto
	const RECOVER = 1;	// token przywracajacy haslo (pozawala zalogowac sie na konto bez jego znajomosci)

	protected function chooseTemplate() {
		return;
	}
}
