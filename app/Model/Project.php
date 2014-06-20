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
	
	 public function getStages($id) {

        return $this->find('first', array(
                    'fields' => array(
                        'project_name','stage_structure',
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
	
	public function saveallstages($pid,$data) {
		$this->id = (int) $pid;
		$this->saveField('stage_structure',$data);
	}
	
	public function getStagestemplate() {
		return '{"1":{"type":"0","callfrequency":"weekly","stageduration":{"start":"1","end":"10"},"numberofcalls":"1","callvolume":{"1":{"attempt1":"sun","call1recall":"tue"}},"callslotsnumber":"1","callslotsdays":{"sun":{"1":{"start":"11","end":"1230"}},"mon":{"1":{"start":"11","end":"1230"}},"tue":{"1":{"start":"11","end":"1230"}},"wed":{"1":{"start":"11","end":"1230"}},"thu":{"1":{"start":"11","end":"1230"}},"fri":{"1":{"start":"11","end":"1230"}},"sat":{"1":{"start":"11","end":"1230"}}}},"2":{"type":"0","callfrequency":"weekly","stageduration":{"start":"11","end":"39"},"numberofcalls":"2","callvolume":{"1":{"attempt1":"sun","call1recall":"tue"},"2":{"attempt1":"wed","call2recall":"undefined"}},"callslotsnumber":"1","callslotsdays":{"sun":{"1":{"start":"11","end":"1230"}}}}}';
	}

}
