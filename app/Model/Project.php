<?php

App::uses('AppModel', 'Model');

class Project extends AppModel {

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

        return $this->find('all', array(
                    'fields' => array(
                        '`id` AS `id`',
                        '`project_name` AS `Name`'
                    )
        ));
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
