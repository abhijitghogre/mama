<?php

class UsersController extends AppController {

    public $uses = array('Project', 'User');

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->Auth->allow('index');
    }

    public function add($projectId) {
        if ($this->request->is('post')) {
            $formData = array();
            parse_str($this->request->data['formData'], $formData);
            $customFields = array();
            if (isset($this->request->data['customFields'])) {
                $customFields = $this->request->data['customFields'];
            }
            $savedata = array();

            if (isset($this->request->data['user_id'])) {
                $savedata["User"]["id"] = $this->request->data['user_id'];
                $savedata["UserMeta"]["id"] = $this->request->data['user_meta_id'];
                $savedata["UserCallflag"]["id"] = $this->request->data['user_callflag_id'];
            } else {
                $this->User->create();
            }

            $savedata["User"]["name"] = $formData['mand-name'];
            $savedata["User"]["phone_no"] = $formData['mand-phone'];
            $savedata["User"]["lmp"] = $formData['mand-lmp'];
            $savedata["User"]["enroll_gest_age"] = $formData['mand-gest-age'];
            $savedata["User"]["project_id"] = $projectId;
            $savedata["User"]["manager_id"] = $this->Auth->user('id');
            $savedata["User"]["call_slots"] = $formData['mand-call-slot'];
            $savedata["User"]["delivery"] = $formData['mand-delivery-status'];
            $savedata["User"]["language"] = $formData['mand-language'];
            $savedata["User"]["registration_date"] = $formData['mand-reg-date'];
            if (isset($formData['mand-delivery-date'])) {
                $savedata["User"]["delivery_date"] = $formData['mand-delivery-date'];
            }
            $savedata["User"]["entry_date"] = date('Y-m-d H:i:s');
            $savedata["User"]["deleted"] = 0;
            $savedata["User"]["phone_type"] = $formData['mand-phone-type'];
            if (isset($formData['mand-phone-code'])) {
                $savedata["User"]["phone_code"] = $formData['mand-phone-code'];
            }
            $savedata["UserMeta"]["age"] = $formData['mand-age'];
            $savedata["UserMeta"]["education"] = $formData['mand-education'];
            if ($formData['mand-phone-owner'] === 'other') {
                $savedata["UserMeta"]["phone_owner"] = $formData['mand-phone-owner-other'];
            } else {
                $savedata["UserMeta"]["phone_owner"] = $formData['mand-phone-owner'];
            }
            $savedata["UserMeta"]["alternate_no"] = $formData['mand-phone-alt'];

            if ($formData['mand-phone-owner-alt'] === 'other') {
                $savedata["UserMeta"]["alternate_no_owner"] = $formData['mand-phone-owner-alt-other'];
            } else {
                $savedata["UserMeta"]["alternate_no_owner"] = $formData['mand-phone-owner-alt'];
            }
            if ($formData['mand-birth-place'] === 'other') {
                $savedata["UserMeta"]["birth_place"] = $formData['mand-birth-place-other'];
            } else {
                $savedata["UserMeta"]["birth_place"] = $formData['mand-birth-place'];
            }
            $savedata["UserMeta"]["custom_fields"] = json_encode($customFields);

            //save default call flags
            $result = $this->Project->find('first', array('conditions'=>array('Project.id'=>$projectId)));
            $stage_template = $result['Project']['template'];
            $savedata["UserCallflag"]["flag"] = $stage_template;

            if ($this->User->saveAll($savedata)) {
                echo json_encode(array('type' => 'success'));
            }
            exit;
        }

        $this->set('project_id', $projectId);
        $project = $this->Project->getExtraFields($projectId);
        $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
        $custom_fields_dom = $this->_customFieldsArrayToDom($custom_fields, false);
        $this->set('custom_fields', $custom_fields_dom);
    }

    public function listUsers($projectId) {
        $users = $this->User->getUsersByProject($projectId);
//        var_dump($users);exit;
        $this->set('users', $users);
        $this->set('projectId', $projectId);
    }

    public function view($id, $projectId) {
        $user = $this->User->getUserDetails($id);
        $project = $this->Project->getExtraFields($projectId);
        $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
        $this->set('user', $user);
        $this->set('user_id', $id);
        $this->set('custom_fields', $custom_fields);
        $this->set('projectId', $projectId);
    }

    public function edit($id, $projectId) {
        $this->set('project_id', $projectId);
        $project = $this->Project->getExtraFields($projectId);
        $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
        $custom_fields_dom = $this->_customFieldsArrayToDom($custom_fields, false);
        $this->set('custom_fields', $custom_fields_dom);

        $user = $this->User->getUserDetails($id);
        $this->set('user_id', $id);
        $this->set('user', $user);
//        var_dump($user);exit;
        $this->set('user_meta_id', $user[0]['UserMeta']['id']);
        $this->set('user_callflag_id', $user[0]['UserCallflag']['id']);
    }

}
