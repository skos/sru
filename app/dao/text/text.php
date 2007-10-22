<?
/**
 * tekst
 */
class UFdao_Text_Text
extends UFdao {

	public function listAlphabetically() {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->order($mapping->alias);

		return $this->doSelect($query);
	}

	public function getByAlias($alias) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->alias, $alias);

		return $this->doSelectFirst($query);
	}
}
