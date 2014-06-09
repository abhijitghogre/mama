<?php

App::uses('AppModel', 'Model');

class Project extends AppModel {

    public $hasMany = 'User';
    public $validate = array(
        'project_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A project name is required'
            ),
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'A project with same name already exists.'
            )
        )
    );

    public function getAllProjects() {

//        return $this->find('all', array(
//                    'fields' => array(
//                        'Project.id AS id',
//                        'Project.project_name AS Name',
//                    )
//        ));
        return $this->query("SELECT Project.id, Project.project_name AS Name, count(User.id) AS count FROM users User LEFT JOIN projects Project on Project.id = User.project_id GROUP BY Project.id"
        );
    }

    public function getProjectDetails($id) {

        return $this->find('all', array(
                    'fields' => array(
                        '`project_name` AS `Name`'
                    ),
                    'conditions' => array(
                        'id' => $id
                    )
        ));
    }

    public function updateProjectDetails($id, $data) {
        $this->id = (int) $id;
        $this->save($data, false);
    }

    public function updateCustomFields($id, $data) {
        $this->id = (int) $id;
        $object[$this->name] = array('custom_fields' => $data);
        $this->save($object, false);
    }

    public function deleteProject($id) {

        $this->delete($id);
    }

    public function getExtraFields($id) {
        return $this->find('all', array(
                    'fields' => array(
                        '`id` AS `id`',
                        '`project_name` AS `project_name`',
                        '`custom_fields` AS `custom_fields`'
                    ),
                    'conditions' => array(
                        'id' => $id
                    )
        ));
    }

}
