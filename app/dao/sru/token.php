<?
/**
 * token
 */
class UFdao_Sru_Token
extends UFdao {

	public function getByTokenType($token, $type) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->token, $token);
		$query->where($mapping->validTo, NOW, $query->GTE);
		$query->where($mapping->type, $type);

		return $this->doSelectFirst($query);
	}
}
