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

    public function updateFlag($encondedflag, $current_time, $intro_call_flag, $userid) {
        $result = $this->query("UPDATE user_callflags SET flag = '$encondedflag', last_check = '$current_time', intro_call = $intro_call_flag WHERE user_id = $userid");
        return TRUE;
    }

    /* New */

    public function updateFlag1($encodedflag, $current_time, $intro_call, $user_id) {
        $result = $this->query("UPDATE user_callflags SET flag = '$encodedflag', last_check = '$current_time', intro_call = $intro_call_flag WHERE user_id = $userid");
        return TRUE;
    }

    public function addFlag($encodedflag, $user_id) {
        $result = $this->query("UPDATE user_callflags SET flag = '$encodedflag' WHERE user_id = $user_id");
        return TRUE;
    }

    public function getUserFlagsByUserId($userId) {
        return $this->find('all', array(
                    'fields' => array(
                        'UserCallflags.flag'
                    ),
                    'conditions' => array(
                        'UserCallflags.user_id' => $userId
                    ))
        );
    }

}

?>
