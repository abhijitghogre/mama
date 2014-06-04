<?php

App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class Manager extends AppModel {

    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'repass' => array(
            'equaltofield' => array(
                'rule' => array('equaltofield', 'password'),
                'message' => 'Password does not match.',
                'on' => 'create', // Limit validation to 'create' or 'update' operations
            )
        ),
        'fname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter your first name.'
            )
        ),
        'lname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Please enter your last name.'
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                    $this->data[$this->alias]['password']
            );
        }
        
        return true;
    }

    function equaltofield($check, $otherfield) {
        //get name of field
        $fname = '';
        foreach ($check as $key => $value) {
            $fname = $key;
            break;
        }
        return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname];
    }

    public function getAllVolunteers() {

        return $this->find('all', array(
                    'fields' => array(
                        '`id` AS `id`',
                        '`username` AS `Username`',
                        '`fname` AS `First Name`',
                        '`lname` AS `Last Name`'
                    ),
                    'conditions' => array(
                        'role' => 'volunteer'
                    )
        ));
    }

    public function getAllAdmins() {

        return $this->find('all', array(
                    'fields' => array(
                        '`id` AS `id`',
                        '`username` AS `Username`',
                        '`fname` AS `First Name`',
                        '`lname` AS `Last Name`'
                    ),
                    'conditions' => array(
                        'role' => 'admin'
                    )
        ));
    }

    public function getManagerDetails($id) {

        return $this->find('all', array(
                    'fields' => array(
                        '`fname` AS `First Name`',
                        '`lname` AS `Last Name`'
                    ),
                    'conditions' => array(
                        'id' => $id
                    )
        ));
    }

    public function updateManagerDetails($id, $data) {
        $this->id = (int) $id;
        $this->save($data, false);
    }

    public function getRoleById($id) {

        return $this->find('all', array(
                    'fields' => array(
                        '`role` AS `role`'
                    ),
                    'conditions' => array(
                        'id' => $id
                    )
        ));
    }

    public function deleteManager($id) {
        
        $this->delete($id);
        
    }

    public function promoteAdmin($id) {
        
        return $this->query(
                        "UPDATE `managers` "
                        . "SET "
                        . "role = 'superadmin' "
                        . "WHERE "
                        . "`id` = " . $id
        );
    }

    public function checkIfUsernameExists($username) {
        return $this->find('all', array(
                    'fields' => array(
                        '`id` AS `id`'
                    ),
                    'conditions' => array(
                        'username' => $username
                    )
        ));
    }

}
