<?

/**
 * wymeldowanie przez administratora Waleta całego akademika
 */
class UFact_SruWalet_User_AllDel
extends UFact {

	const PREFIX = 'allUsersDel';

	public function go() {
            try {   
                        $get = $this->_srv->get('req')->get;
                        $post = $this->_srv->get('req')->post->{self::PREFIX};
                        $tmp=$post['confirm'];
                        
                        $bean = UFra::factory('UFbean_Sru_UserList');
                        if($tmp) {
                                $tmp = array();
                                $tmp['dormitory'] = $get->dormAlias;
                                $bean->search($tmp,false,true);
                                $d['users'] = $bean;
                       
                                for($i = 0; $i < count($d['users']); $i++) {
                                        if($d['users'][$i]['active']) {
                                                $userId = (string)$d['users'][$i]['id'];
                                                
                                                $this->begin();
                                                $bean = UFra::factory('UFbean_Sru_User');
			
                                                UFlib_Helper::removePenaltyFromPort($userId);
			
                                                $bean->getByPK($userId);

                                                $bean->modifiedById = $this->_srv->get('session')->authWaletAdmin;
                                                $bean->modifiedAt = NOW;
                                                $bean->active = false;
			
                                                $bean->save();

                                                $conf = UFra::shared('UFconf_Sru');
                                                if ($conf->sendEmail && $bean->notifyByEmail() && !is_null($bean->email) && $bean->email != '') {
                                                        $sender = UFra::factory('UFlib_Sender');
                                                        $history = UFra::factory('UFbean_SruAdmin_UserHistoryList');
                                                        $history->listByUserId($bean->id, 1);
                                                        $bean->getByPK($bean->id);	// pobranie nowych danych, np. aliasu ds-u
                                                        // wyslanie maila do usera
                                                        $box = UFra::factory('UFbox_SruAdmin');
                                                        $title = $box->dataChangedMailTitle($bean);
                                                        $body = $box->dataChangedMailBody($bean, $history);
                                                        $sender->send($bean, $title, $body, self::PREFIX, $bean->dormitoryAlias);
                                                }

                                                $this->commit();
                                        }
                                }
                        
                                $this->postDel(self::PREFIX);
                                $this->markOk(self::PREFIX);
                        } else {
                                $this->postDel(self::PREFIX);
                        }
            } catch (UFex_Dao_NotFound $e) {
                    $this->markErrors(self::PREFIX, array ('users'=>'no'));
            } catch (UFex $e) {
			UFra::error($e);
            }
		
	}
}
