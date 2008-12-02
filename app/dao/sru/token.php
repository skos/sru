<?
/**
 * token
 */
class UFdao_Sru_Token
extends UFdao {

	public function getByToken($token) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->token, $token);
		$query->where($mapping->validTo, NOW, $query->GTE);

		return $this->doSelectFirst($query);
	}
}
