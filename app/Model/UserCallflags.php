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
