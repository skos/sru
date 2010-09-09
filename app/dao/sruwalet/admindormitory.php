<?
/**
 * przypisanie admina Waleta do DSu
 */
class UFdao_SruWalet_AdminDormitory
extends UFdao {

	public function getByAdminAndDorm($admin, $dorm) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->admin, $admin);
		$query->where($mapping->dormitory, $dorm);

		return $this->doSelectFirst($query);
	}
}
