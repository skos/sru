<?php
class UFview_SruAdmin_Admins
extends UFview_SruAdmin {

	public function fillData() {
		$box  = UFra::shared('UFbox_SruAdmin');

		$this->append('title', $box->titleAdmins());
		$this->append('body', $box->admins());
		
		//@todo: w zaleznosci od uprawnien te boxy:?
			$this->append('body', $box->bots());//@todo:kim, czym beda boty? beda mialy jid, czy telefon?:P 
												//moze warto im dac odzielna tabele?
			$this->append('body', $box->inactiveAdmins());
	}
}

?>