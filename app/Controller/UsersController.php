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
            $customFields = $this->request->data['customFields'];

            $savedata = array();

            if (isset($this->request->data['user_id'])) {
                $savedata["User"]["id"] = $this->request->data['user_id'];
                $savedata["UserMeta"]["id"] = $this->request->data['user_meta_id'];
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
            $savedata["User"]["phone_code"] = $formData['mand-phone-code'];
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
            $savedata["UserCallflag"]["flag"] = '[{"1":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"2":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"3":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"4":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"5":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"6":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"7":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"8":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"9":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"10":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"11":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"12":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"13":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"14":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"15":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"16":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"17":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"18":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"19":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"20":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"21":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"22":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"23":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"24":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"25":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"26":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"27":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"28":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"29":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"30":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"31":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"32":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"33":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"34":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"35":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"36":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"37":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"38":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"39":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}}},[{"1":{"flag":0,"attempts":0},"2":{"flag":0,"attempts":0},"3":{"flag":0,"attempts":0},"4":{"flag":0,"attempts":0},"5":{"flag":0,"attempts":0},"6":{"flag":0,"attempts":0},"7":{"flag":0,"attempts":0}},{"2":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"3":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"4":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"5":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"6":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"7":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"8":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"9":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"10":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"11":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}},"12":{"first_call":{"flag":0,"attempts":0},"second_call":{"flag":0,"attempts":0}}},{"13":{"flag":0,"attempts":0},"14":{"flag":0,"attempts":0},"15":{"flag":0,"attempts":0},"16":{"flag":0,"attempts":0},"17":{"flag":0,"attempts":0},"18":{"flag":0,"attempts":0},"19":{"flag":0,"attempts":0},"20":{"flag":0,"attempts":0},"21":{"flag":0,"attempts":0},"22":{"flag":0,"attempts":0},"23":{"flag":0,"attempts":0},"24":{"flag":0,"attempts":0},"25":{"flag":0,"attempts":0},"26":{"flag":0,"attempts":0},"27":{"flag":0,"attempts":0},"28":{"flag":0,"attempts":0},"29":{"flag":0,"attempts":0},"30":{"flag":0,"attempts":0},"31":{"flag":0,"attempts":0},"32":{"flag":0,"attempts":0},"33":{"flag":0,"attempts":0},"34":{"flag":0,"attempts":0},"35":{"flag":0,"attempts":0},"36":{"flag":0,"attempts":0},"37":{"flag":0,"attempts":0},"38":{"flag":0,"attempts":0},"39":{"flag":0,"attempts":0},"40":{"flag":0,"attempts":0},"41":{"flag":0,"attempts":0},"42":{"flag":0,"attempts":0},"43":{"flag":0,"attempts":0},"44":{"flag":0,"attempts":0},"45":{"flag":0,"attempts":0},"46":{"flag":0,"attempts":0},"47":{"flag":0,"attempts":0},"48":{"flag":0,"attempts":0},"49":{"flag":0,"attempts":0},"50":{"flag":0,"attempts":0},"51":{"flag":0,"attempts":0},"52":{"flag":0,"attempts":0}},{"13":{"flag":0,"attempts":0},"14":{"flag":0,"attempts":0},"15":{"flag":0,"attempts":0},"16":{"flag":0,"attempts":0},"17":{"flag":0,"attempts":0},"18":{"flag":0,"attempts":0},"19":{"flag":0,"attempts":0},"20":{"flag":0,"attempts":0},"21":{"flag":0,"attempts":0},"22":{"flag":0,"attempts":0},"23":{"flag":0,"attempts":0},"24":{"flag":0,"attempts":0},"25":{"flag":0,"attempts":0},"26":{"flag":0,"attempts":0},"27":{"flag":0,"attempts":0},"28":{"flag":0,"attempts":0},"29":{"flag":0,"attempts":0},"30":{"flag":0,"attempts":0},"31":{"flag":0,"attempts":0},"32":{"flag":0,"attempts":0},"33":{"flag":0,"attempts":0},"34":{"flag":0,"attempts":0},"35":{"flag":0,"attempts":0},"36":{"flag":0,"attempts":0},"37":{"flag":0,"attempts":0},"38":{"flag":0,"attempts":0},"39":{"flag":0,"attempts":0},"40":{"flag":0,"attempts":0},"41":{"flag":0,"attempts":0},"42":{"flag":0,"attempts":0},"43":{"flag":0,"attempts":0},"44":{"flag":0,"attempts":0},"45":{"flag":0,"attempts":0},"46":{"flag":0,"attempts":0},"47":{"flag":0,"attempts":0},"48":{"flag":0,"attempts":0},"49":{"flag":0,"attempts":0},"50":{"flag":0,"attempts":0},"51":{"flag":0,"attempts":0},"52":{"flag":0,"attempts":0},"53":{"flag":0,"attempts":0},"54":{"flag":0,"attempts":0},"55":{"flag":0,"attempts":0},"56":{"flag":0,"attempts":0},"57":{"flag":0,"attempts":0},"58":{"flag":0,"attempts":0},"59":{"flag":0,"attempts":0},"60":{"flag":0,"attempts":0}}]]';

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
    }

}
