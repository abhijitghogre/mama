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
            $result = $this->Project->find('first', array('conditions' => array('Project.id' => $projectId)));
            $stage_template = $result['Project']['template'];
            $savedata["UserCallflag"]["flag"] = $stage_template;
            /* calculating stage */
            date_default_timezone_set('Asia/Calcutta');
            $current_time = time();
            $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
            $structure = json_decode($result['Project']['stage_structure'], true);
            if ($savedata["User"]["delivery"] == 0) {
                if (isset($savedata["User"]["lmp"])) {
                    $date1 = strtotime($savedata["User"]["lmp"]);
                    $gest_age = 0;
                } else {
                    $date1 = strtotime($savedata["User"]["registration_date"]);
                    $gest_age = $savedata["User"]["enroll_gest_age"];
                }
            } elseif ($savedata["User"]["delivery"] == 1) {
                $date1 = strtotime($savedata["User"]["delivery_date"]);
            }
            $date2 = $current_time;
            $stageno = 1;
            foreach ($structure as $key => $struct) {
                $callfrequency = $struct['callfrequency'];
                $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
                $stagestart = $struct['stageduration']['start'];
                $stageend = $struct['stageduration']['end'];
                if ($presentgestage >= $stagestart && $presentgestage <= $stageend) {
                    $userstage = $stageno;
                }
                $stageno++;
            }
            $savedata["User"]["stage"] = $userstage;
            /* end of stage calculation */

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

    public function delete($id) {
        //redirect on unauthorized route /delete
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        $this->User->deleteUser($id);
        $this->Session->setFlash(__('User deleted.'));
        $this->redirect($this->referer());
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

    function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
        /*
          $interval can be:
          yyyy - Number of full years
          q - Number of full quarters
          m - Number of full months
          y - Difference between day numbers
          (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
          d - Number of full days
          w - Number of full weekdays
          ww - Number of full weeks
          h - Number of full hours
          n - Number of full minutes
          s - Number of full seconds (default)
         */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; // Difference in seconds

        switch ($interval) {

            case 'yyyy': // Number of full years

                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters

                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months

                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                //$months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers

                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days

                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays

                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks

                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours

                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes

                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)

                $datediff = $difference;
                break;
        }

        return $datediff;
    }

}
