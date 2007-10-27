<?

/**
 * sru
 */
class UFbox_Sru
extends UFbox {

	public function login() {
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function logout() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getFromSession();

		$d['user'] = $bean;

		return $this->render(__FUNCTION__, $d);
	}

	public function userAdd() {
		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		$faculties = UFra::factory('UFbean_Sru_FacultyList');
		$faculties->listAll();
		$bean = UFra::factory('UFbean_Sru_User');

		$d['user'] = $bean;
		$d['dormitories'] = $dorms;
		$d['faculties'] = $faculties;

		return $this->render(__FUNCTION__, $d);
	}

	public function userEdit() {
		$bean = UFra::factory('UFbean_Sru_User');
		$bean->getFromSession();

		$dorms = UFra::factory('UFbean_Sru_DormitoryList');
		$dorms->listAll();
		$faculties = UFra::factory('UFbean_Sru_FacultyList');
		$faculties->listAll();

		$d['user'] = $bean;
		$d['dormitories'] = $dorms;
		$d['faculties'] = $faculties;

		return $this->render(__FUNCTION__, $d);
	}
}
