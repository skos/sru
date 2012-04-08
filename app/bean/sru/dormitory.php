<?
/**
 * akademik
 */
class UFbean_Sru_Dormitory
extends UFbeanSingle {
    public $left;
    public $right;
    
    /**
     * Ustawia pola $left i $right. Potrzebne do zrobienia odnośników do sąsiednich akademików (strzalki po bokach)
     */
    
    public function leftRight(){
        $dorms = UFra::factory('UFbean_Sru_DormitoryList');
        $dorms->listAll();
        $left = null;
        $middle = null;
        $right = null;
        if($dorms->valid()){
            $left = $dorms->current();
        }
        if($left['id'] == $this->id){//brak lewego
            $left = null;
            $dorms->next();
            if($dorms->valid()){
                $right = $dorms->current();
            }
        }else{
            $dorms->next();
            if($dorms->valid()){
                $middle = $dorms->current();
            }
            while(true){
                $right = null;
                $dorms->next();
                if($dorms->valid()){
                    $right = $dorms->current();
                }
                if($this->id == $middle['id']){
                    break;
                }
                $left = $middle;
                $middle = $right;
            }
        }
        $this->left = $left;
        $this->right = $right;
    }
}
