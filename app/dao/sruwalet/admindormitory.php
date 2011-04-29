<?
/**
 * przypisanie admina Waleta do DSu
 */
class UFdao_SruWalet_AdminDormitory
extends UFdao {

	public function getByAdminAndDorm($admin, $dorm) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->admin, $admin);
		$query->where($mapping->dormitory, $dorm);

		return $this->doSelectFirst($query);
	}

	public function listAllById($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->admin, $id);
		$query->order($mapping->dormitory);

		return $this->doSelect($query);
	}

	public function listAllByDormId($id) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitory, $id);
		$query->where($mapping->adminType, UFacl_SruAdmin_Admin::BOT, UFlib_Db_Query::LTE);
		$query->order($mapping->adminName);

		return $this->doSelect($query);
	}
}
