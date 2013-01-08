<?php
/**
 * controller
 *
 * rozdziela zadania pomiedzy inne controlery, uruchamia akcje i wybiera widok
 */
abstract class UFctl
extends UFlib_ClassWithService {

	public function __construct(&$srv = null) {
		parent::__construct($srv);
		$this->req = $this->_srv->get('req');
		$this->acl = $this->_srv->get('acl');
	}
	
	/**
	 * uruchamia po kolei wszstkie funkcje kontrolera
	 */
	public function go() {
		if (false === $this->parseParameters()) {
			return;
		}
		$this->filterBefore();

		$act = $this->goAction($this->chooseAction());

		$this->filterMiddle();

		$repeat = true;
		$changes = 0;
		
		$max = $this->_srv->get('conf')->maxViewChanges;
		try {
			while ($repeat) {
				try {
					if ($act) {	// akcja mogla zmienic parametry
						$this->parseParameters();
					}
					$view = $this->chooseView();
					$this->goView($view);
					$repeat = false;
				} catch (UFex_Core_ViewChange $e) {
					UFra::debug('View '.$view.' changed');
					if (++$changes < $max) {
						$repeat = true;
					} else {
						$repeat = false;
						UFra::error('Max view changes ('.$max.') reached.');
						echo 'Internal error';
					}
				}
			}
		} catch (Exception $e) {
			UFra::error('View failed: '.print_r($e, true));
		}

		$this->filterAfter();
	}

	/**
	 * ustawia zmienne srodowiska na podstawie requestu
	 *
	 * @return false/null - false oznacza koniec przetwarzania kodu kontrolera - uzywane przy uruchamianiu innych kontrolerow
	 */
	protected function parseParameters() {
	}

	/**
	 * wybiera akcje do uruchomienia
	 * 
	 * @param string/null $action - nazwa dotychczas wybranej akcji
	 * @return string/null - wybrana akcja
	 */
	protected function chooseAction($action = null) {
		return $action;
	}

	/**
	 * uruchomienie akcji
	 * 
	 * @param string/null $actionName - akcja do uruchomienia
	 * @return bool - czy jakas akcja zostala wykonana
	 */
	protected function goAction($actionName) {
		if (is_null($actionName)) {
			return false;
		}
		if (!is_string($actionName)) {
			throw UFra::factory('UFex_Core_NoParameter', 'Action name');
		}
		UFra::debug('Action: '.$actionName.' / '.get_class(($this)));
		$actionName = 'UFact_'.$actionName;
		$action = UFra::factory($actionName);
		$action->go();
		return true;
	}

	/**
	 * wybiera widok
	 * 
	 * @param string/null $view - nazwa dotychczas wybranego widoku
	 * @return string/null - wybrany widok
	 */
	protected function chooseView($view = null) {
		return $view;
	}

	/**
	 * uruchomienie widoku
	 * 
	 * @param string/null $viewName - widok do uruchomienia
	 */
	protected function goView($viewName) {
		if (is_null($viewName)) {
			return;
		}
		if (!is_string($viewName)) {
			throw UFra::factory('UFex_Core_NoParameter', 'View name');
		}
		UFra::debug('View: '.$viewName.' / '.get_class(($this)));
		$viewName = 'UFview_'.$viewName;
		$view = UFra::factory($viewName);
		$view->go();
	}

	/**
	 * filtry przed akcja
	 */
	protected function filterBefore() {
	}

	/**
	 * filtry pomiedzy akcja a widokiem
	 */
	protected function filterMiddle() {
	}

	/**
	 * filtry po widoku
	 */
	protected function filterAfter() {
	}
}
