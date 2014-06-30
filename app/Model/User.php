<?php

App::uses('AppModel', 'Model');

class User extends AppModel {

    public $hasOne = array('UserMeta', 'UserCallflag');
    public $hasMany = array('DialerLogs');
    public $belongsTo = array('Project');

    public function getUsersByProject($projectId) {
        return $this->find('all', array(
                    'fields' => array(
                        'User.id',
                        'User.name AS Name',
                        'User.phone_no AS Phone'
                    ),
                    'conditions' => array(
                        'User.project_id' => $projectId,
                        'User.deleted' => 0
                    )
        ));
    }

    public function getUserDetails($id) {

        return $this->find('all', array(
                    'conditions' => array(
                        'User.id' => $id,
                        'User.deleted' => 0
                    )
        ));
    }

    public function getUserFromPhone($phoneno) {
        $result = $this->find('first', array('conditions' => array('User.phone_no' => $phoneno, 'User.deleted' => 0), array('contain' => array('UserCallflags'))));
        return $result;
    }

    public function updateStage($user_id, $userstage) {
        $this->query("UPDATE users SET stage = '$userstage' WHERE id = '$user_id'");
        return TRUE;
    }

    public function deleteUser($id) {
        $this->query("UPDATE users SET deleted = 1 WHERE id = '$id'");
        return TRUE;
    }

}
