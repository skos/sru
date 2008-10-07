<?
/**
 * front controller api
 */
class UFctl_SruApi_Front
extends UFctl {

	protected function parseParameters() {
		$req = $this->_srv->get('req');
		$get = $req->get;
		
		$segCount = $req->segmentsCount();
		$get->view = '-';
		if ($segCount>0) {
			switch ($req->segment(1)) {
				case 'dhcp-stud':
					$get->view = 'dhcp/stud';
					break;
				case 'dhcp-adm':
					$get->view = 'dhcp/adm';
					break;
				case 'dhcp-org':
					$get->view = 'dhcp/org';
					break;
			}
		}
	}

	protected function chooseView($view = null) {
		$req = $this->_srv->get('req');
		$get = $req->get;

		switch ($get->view) {
			case 'dhcp/stud':
				return 'SruApi_DhcpStuds';
			case 'dhcp/adm':
				return 'SruApi_DhcpAdm';
			case 'dhcp/org':
				return 'SruApi_DhcpOrg';
			default:
				return 'SruApi_Error404';
		}
	}
}
