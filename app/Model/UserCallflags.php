<?php 
class UserCallflags extends AppModel {
    var $name = 'UserCallflags';
    var $useTable = 'user_callflags';
     public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );
    
     public function updateFlag($encodedflag, $current_time, $intro_call, $user_id){
        $result = $this->query("UPDATE user_callflags SET flag = '$encodedflag', last_check = '$current_time', intro_call = $intro_call WHERE user_id = $user_id");
        return TRUE;
    }
    public function addFlag($encodedflag, $user_id){
        $result = $this->query("UPDATE user_callflags SET flag = '$encodedflag' WHERE user_id = $user_id");
        return TRUE;
    }
}
?>
