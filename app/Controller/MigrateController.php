<?php

class MigrateController extends AppController {

    var $uses = array('Migrate', 'User', 'UserCallflags', 'UserMeta', 'Project');

    function beforeFilter() {
        $this->Auth->allow();
    }

    function index() {
        $this->Migrate->setDataSource('default2');
        $result = $this->Migrate->getUser();
        echo "<pre>";
        //print_r($result);exit;
        foreach ($result as $r) {
            $customFields = array();
            //localhost custom field values
           /* $customFields['1407389364'] = $r['u']['rid'];
            $customFields['1407749850'] = $r['u']['village'];
            $customFields['1407483295'] = $r['um']['work_engagement'];
            $customFields['1407483322'] = $r['um']['house_hold_size'];
            $customFields['1407483357'] = $r['um']['monthly_family_income'];
            $customFields['1407483409'] = $r['um']['recieve_calls'];
            $customFields['1407483449'] = $r['um']['make_calls'];
            $customFields['1407483479'] = $r['um']['recieve_sms'];
            $customFields['1407483528'] = $r['um']['read_sms'];
            $customFields['1407483553'] = $r['um']['send_sms'];
            $customFields['1407483584'] = $r['um']['edd'];
            $customFields['1407483772'] = array($r['um']['complications']);
            $customFields['1407483801'] = $r['um']['medical_illness'];
            $customFields['1407483856'] = array($r['um']['personal_history']);
            $customFields['1407483921'] = $r['um']['shg'];
            */
             //server custom field values
            $customFields['1407394954'] = $r['u']['rid'];
            $customFields['1407394981'] = $r['u']['village'];
            $customFields['1407395036'] = $r['um']['work_engagement'];
            $customFields['1407395081'] = $r['um']['house_hold_size'];
            $customFields['1407395099'] = $r['um']['monthly_family_income'];
            $customFields['1407395131'] = $r['um']['recieve_calls'];
            $customFields['1407395170'] = $r['um']['make_calls'];
            $customFields['1407395200'] = $r['um']['recieve_sms'];
            $customFields['1407395230'] = $r['um']['read_sms'];
            $customFields['1407395262'] = $r['um']['send_sms'];
            $customFields['1407395291'] = $r['um']['edd'];
            $customFields['1407395427'] = array($r['um']['complications']);
            $customFields['1407395505'] = $r['um']['medical_illness'];
            $customFields['1407395566'] = array($r['um']['personal_history']);
            $customFields['1407395604'] = $r['um']['shg'];
            $savedata = array();
            $this->User->create();
            $savedata["User"]["channel_id"] = 1;
            $savedata["User"]["name"] = $r['u']['name'];
            $savedata["User"]["phone_no"] = $r['u']['phone_no'];
            $savedata["User"]["lmp"] = $r['um']['lmp'];
            $savedata["User"]["enroll_gest_age"] = $r['u']['enroll_gest_age'];
            $savedata["User"]["project_id"] = 1;
            $savedata["User"]["manager_id"] = $r['u']['manager_id'];
            $savedata["User"]["call_slots"] = $r['u']['call_slots'];
            $savedata["User"]["delivery"] = $r['u']['delivery'];
            $savedata["User"]["language"] = $r['u']['language'];
            $savedata["User"]["registration_date"] = $r['u']['registration_date'];
            $savedata["User"]["delivery_date"] = $r['u']['delivery_date'];
            $savedata["User"]["entry_date"] = $r['u']['entry_date'];
            $savedata["User"]["deleted"] = 0;
            $savedata["User"]["phone_type"] = $r['u']['phone_type'];
            $savedata["User"]["phone_code"] = $r['u']['phone_code'];
            $savedata["UserMeta"]["age"] = $r['u']['age'];
            $savedata["UserMeta"]["education"] = $r['um']['education'];
            $savedata["UserMeta"]["phone_owner"] = $r['um']['phone_owner'];
            $savedata["UserMeta"]["alternate_no"] = $r['u']['phone_no'];
            $savedata["UserMeta"]["alternate_no_owner"] = $r['um']['alternate_owner1'];
            $savedata["UserMeta"]["birth_place"] = $r['um']['birth_place'];
            $savedata["UserMeta"]["custom_fields"] = '';
            $savedata["UserMeta"]["custom_fields"] = json_encode($customFields);
            //save default call flags
            $result = $this->Project->find('first', array('conditions' => array('Project.id' => 1)));
            $stage_template = $result['Project']['template'];
            $savedata["UserCallflag"]["flag"] = $this->changecallflag($r['uc']['flag']);
            
            /* calculating stage */
            date_default_timezone_set('Asia/Calcutta');
            $current_time = time();
            $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
            $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
            $structure = json_decode($result['Project']['stage_structure'], true);
             $gest_age = 0;
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
            $userstage = 0;
            foreach ($structure as $key => $struct) {
                $callfrequency = $struct['callfrequency'];
                $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
                $stagestart = $struct['stageduration'][$frequency[$callfrequency] . '_start'];
                $stageend = $struct['stageduration'][$frequency[$callfrequency] . '_end'];
                if ($presentgestage >= $stagestart && $presentgestage <= $stageend) {
                    $userstage = $stageno;
                }
                $stageno++;
            }
            $savedata["User"]["stage"] = $userstage;
            /* end of stage calculation */

            if ($this->User->saveAll($savedata)) {
                $this->Migrate->changeDeleted($r['u']['id']);
                echo json_encode(array('type' => 'success'));
            }
        }
        exit;
    }

    /* Migrating data from mMitra db to mMitra-MAMA */

    public function changecallflag1($callflag) {
        $oldflag = json_decode($callflag, true);
        $newflag = array();
        $stage = 1;
        $freq = "";
        foreach ($oldflag[0] as $key => $val) {
            $stage == 1;
            $freq = "w";
            if ($key >= 11) {
                if (array_key_exists('first_call', $val)) {
                    $index = $stage . "." . $freq . $key . ".1";
                    $flagvalue = array("reason" => $val['first_call']['flag'], "attempts" => $val['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                    $newflag[$index] = $flagvalue;
                }
                if (array_key_exists('second_call', $val)) {
                    $index = $stage . "." . $freq . $key . ".2";
                    $flagvalue = array("reason" => $val['second_call']['flag'], "attempts" => $val['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                    $newflag[$index] = $flagvalue;
                }
            }
        }
        $newflag["1.w39.1"] = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag["1.w39.2"] = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        foreach ($oldflag[1][0] as $key => $val) {
            $stage = 2;
            $freq = "d";
            $index = $stage . "." . $freq . $key;
            $flagvalue = array("reason" => $val['flag'], "attempts" => $val['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
            $newflag[$index] = $flagvalue;
        }
        foreach ($oldflag[1][1] as $key => $val) {
            $stage = 3;
            $freq = "w";
            if (array_key_exists('first_call', $val)) {
                $index = $stage . "." . $freq . $key . ".1";
                $flagvalue = array("reason" => $val['first_call']['flag'], "attempts" => $val['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                $newflag[$index] = $flagvalue;
            }
            if (array_key_exists('second_call', $val)) {
                $index = $stage . "." . $freq . $key . ".2";
                $flagvalue = array("reason" => $val['second_call']['flag'], "attempts" => $val['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                $newflag[$index] = $flagvalue;
            }
        }
        foreach ($oldflag[1][2] as $key => $val) {
            //print_r($val);exit;
            if ($key <= 52) {
            $stage = 4;
            $freq = "w";
            $index = $stage . "." . $freq . $key;
            $flagvalue = array("reason" => $val['flag'], "attempts" => $val['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
            $newflag[$index] = $flagvalue;
            }
        }
        //print_r($newflag);exit;
        /* foreach($oldflag[1][3] as $key=>$val){
          //print_r($val);exit;
          $stage = 5;
          $freq = "m";
          $index = $stage.".".$freq.$key;
          $flagvalue = array("reason" => $val['flag'], "attempts" => $val['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
          $newflag[$index] = $flagvalue;
          } */
        $changedflag = json_encode($newflag);
        return $changedflag;
    }
    public function changecallflag($callflag) {
        $old = json_decode($callflag, true);
        $newflag = array();

        //stage1
        $newflag['1.w11.1'] = array("reason" => $old[0][11]['first_call']['flag'], "attempts" => $old[0][11]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w11.2'] = array("reason" => $old[0][11]['second_call']['flag'], "attempts" => $old[0][11]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w12.1'] = array("reason" => $old[0][12]['first_call']['flag'], "attempts" => $old[0][12]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w12.2'] = array("reason" => $old[0][12]['second_call']['flag'], "attempts" => $old[0][12]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w13.1'] = array("reason" => $old[0][13]['first_call']['flag'], "attempts" => $old[0][13]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w13.2'] = array("reason" => $old[0][13]['second_call']['flag'], "attempts" => $old[0][13]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w14.1'] = array("reason" => $old[0][14]['first_call']['flag'], "attempts" => $old[0][14]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w14.2'] = array("reason" => $old[0][14]['second_call']['flag'], "attempts" => $old[0][14]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w15.1'] = array("reason" => $old[0][15]['first_call']['flag'], "attempts" => $old[0][15]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w15.2'] = array("reason" => $old[0][15]['second_call']['flag'], "attempts" => $old[0][15]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w16.1'] = array("reason" => $old[0][16]['first_call']['flag'], "attempts" => $old[0][16]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w16.2'] = array("reason" => $old[0][16]['second_call']['flag'], "attempts" => $old[0][16]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w17.1'] = array("reason" => $old[0][17]['first_call']['flag'], "attempts" => $old[0][17]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w17.2'] = array("reason" => $old[0][17]['second_call']['flag'], "attempts" => $old[0][17]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w18.1'] = array("reason" => $old[0][18]['first_call']['flag'], "attempts" => $old[0][18]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w18.2'] = array("reason" => $old[0][18]['second_call']['flag'], "attempts" => $old[0][18]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w19.1'] = array("reason" => $old[0][19]['first_call']['flag'], "attempts" => $old[0][19]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w19.2'] = array("reason" => $old[0][19]['second_call']['flag'], "attempts" => $old[0][19]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w20.1'] = array("reason" => $old[0][20]['first_call']['flag'], "attempts" => $old[0][20]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w20.2'] = array("reason" => $old[0][20]['second_call']['flag'], "attempts" => $old[0][20]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w21.1'] = array("reason" => $old[0][21]['first_call']['flag'], "attempts" => $old[0][21]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w21.2'] = array("reason" => $old[0][21]['second_call']['flag'], "attempts" => $old[0][21]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w22.1'] = array("reason" => $old[0][22]['first_call']['flag'], "attempts" => $old[0][22]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w22.2'] = array("reason" => $old[0][22]['second_call']['flag'], "attempts" => $old[0][22]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w23.1'] = array("reason" => $old[0][23]['first_call']['flag'], "attempts" => $old[0][23]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w23.2'] = array("reason" => $old[0][23]['second_call']['flag'], "attempts" => $old[0][23]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w24.1'] = array("reason" => $old[0][24]['first_call']['flag'], "attempts" => $old[0][24]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w24.2'] = array("reason" => $old[0][24]['second_call']['flag'], "attempts" => $old[0][24]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w25.1'] = array("reason" => $old[0][25]['first_call']['flag'], "attempts" => $old[0][25]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w25.2'] = array("reason" => $old[0][25]['second_call']['flag'], "attempts" => $old[0][25]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w26.1'] = array("reason" => $old[0][26]['first_call']['flag'], "attempts" => $old[0][26]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w26.2'] = array("reason" => $old[0][26]['second_call']['flag'], "attempts" => $old[0][26]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w27.1'] = array("reason" => $old[0][27]['first_call']['flag'], "attempts" => $old[0][27]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w27.2'] = array("reason" => $old[0][27]['second_call']['flag'], "attempts" => $old[0][27]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w28.1'] = array("reason" => $old[0][28]['first_call']['flag'], "attempts" => $old[0][28]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w28.2'] = array("reason" => $old[0][28]['second_call']['flag'], "attempts" => $old[0][28]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w29.1'] = array("reason" => $old[0][29]['first_call']['flag'], "attempts" => $old[0][29]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w29.2'] = array("reason" => $old[0][29]['second_call']['flag'], "attempts" => $old[0][29]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w30.1'] = array("reason" => $old[0][30]['first_call']['flag'], "attempts" => $old[0][30]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w30.2'] = array("reason" => $old[0][30]['second_call']['flag'], "attempts" => $old[0][30]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w31.1'] = array("reason" => $old[0][31]['first_call']['flag'], "attempts" => $old[0][31]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w31.2'] = array("reason" => $old[0][31]['second_call']['flag'], "attempts" => $old[0][31]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w32.1'] = array("reason" => $old[0][32]['first_call']['flag'], "attempts" => $old[0][32]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w32.2'] = array("reason" => $old[0][32]['second_call']['flag'], "attempts" => $old[0][32]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w33.1'] = array("reason" => $old[0][33]['first_call']['flag'], "attempts" => $old[0][33]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w33.2'] = array("reason" => $old[0][33]['second_call']['flag'], "attempts" => $old[0][33]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w34.1'] = array("reason" => $old[0][34]['first_call']['flag'], "attempts" => $old[0][34]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w34.2'] = array("reason" => $old[0][34]['second_call']['flag'], "attempts" => $old[0][34]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w35.1'] = array("reason" => $old[0][35]['first_call']['flag'], "attempts" => $old[0][35]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w35.2'] = array("reason" => $old[0][35]['second_call']['flag'], "attempts" => $old[0][35]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w36.1'] = array("reason" => $old[0][36]['first_call']['flag'], "attempts" => $old[0][36]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w36.2'] = array("reason" => $old[0][36]['second_call']['flag'], "attempts" => $old[0][36]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w37.1'] = array("reason" => $old[0][37]['first_call']['flag'], "attempts" => $old[0][37]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w37.2'] = array("reason" => $old[0][37]['second_call']['flag'], "attempts" => $old[0][37]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w38.1'] = array("reason" => $old[0][38]['first_call']['flag'], "attempts" => $old[0][38]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w38.2'] = array("reason" => $old[0][38]['second_call']['flag'], "attempts" => $old[0][38]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w39.1'] = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['1.w39.2'] = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        
        //stage2
        $newflag['2.d1'] = array("reason" => $old[1][0][1]['flag'], "attempts" => $old[1][0][1]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d2'] = array("reason" => $old[1][0][2]['flag'], "attempts" => $old[1][0][2]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d3'] = array("reason" => $old[1][0][3]['flag'], "attempts" => $old[1][0][3]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d4'] = array("reason" => $old[1][0][4]['flag'], "attempts" => $old[1][0][4]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d5'] = array("reason" => $old[1][0][5]['flag'], "attempts" => $old[1][0][5]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d6'] = array("reason" => $old[1][0][6]['flag'], "attempts" => $old[1][0][6]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['2.d7'] = array("reason" => $old[1][0][7]['flag'], "attempts" => $old[1][0][7]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        //stage3
        $newflag['3.w2.1'] = array("reason" => $old[1][1][2]['first_call']['flag'], "attempts" => $old[1][1][2]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w2.2'] = array("reason" => $old[1][1][2]['second_call']['flag'], "attempts" => $old[1][1][2]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w3.1'] = array("reason" => $old[1][1][3]['first_call']['flag'], "attempts" => $old[1][1][3]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w3.2'] = array("reason" => $old[1][1][3]['second_call']['flag'], "attempts" => $old[1][1][3]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w4.1'] = array("reason" => $old[1][1][4]['first_call']['flag'], "attempts" => $old[1][1][4]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w4.2'] = array("reason" => $old[1][1][4]['second_call']['flag'], "attempts" => $old[1][1][4]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w5.1'] = array("reason" => $old[1][1][5]['first_call']['flag'], "attempts" => $old[1][1][5]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w5.2'] = array("reason" => $old[1][1][5]['second_call']['flag'], "attempts" => $old[1][1][5]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w6.1'] = array("reason" => $old[1][1][6]['first_call']['flag'], "attempts" => $old[1][1][6]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w6.2'] = array("reason" => $old[1][1][6]['second_call']['flag'], "attempts" => $old[1][1][6]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w7.1'] = array("reason" => $old[1][1][7]['first_call']['flag'], "attempts" => $old[1][1][7]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w7.2'] = array("reason" => $old[1][1][7]['second_call']['flag'], "attempts" => $old[1][1][7]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w8.1'] = array("reason" => $old[1][1][8]['first_call']['flag'], "attempts" => $old[1][1][8]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w8.2'] = array("reason" => $old[1][1][8]['second_call']['flag'], "attempts" => $old[1][1][8]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w9.1'] = array("reason" => $old[1][1][9]['first_call']['flag'], "attempts" => $old[1][1][9]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w9.2'] = array("reason" => $old[1][1][9]['second_call']['flag'], "attempts" => $old[1][1][9]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w10.1'] = array("reason" => $old[1][1][10]['first_call']['flag'], "attempts" => $old[1][1][10]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w10.2'] = array("reason" => $old[1][1][10]['second_call']['flag'], "attempts" => $old[1][1][10]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w11.1'] = array("reason" => $old[1][1][11]['first_call']['flag'], "attempts" => $old[1][1][11]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w11.2'] = array("reason" => $old[1][1][11]['second_call']['flag'], "attempts" => $old[1][1][11]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w12.1'] = array("reason" => $old[1][1][12]['first_call']['flag'], "attempts" => $old[1][1][12]['first_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['3.w12.2'] = array("reason" => $old[1][1][12]['second_call']['flag'], "attempts" => $old[1][1][12]['second_call']['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        //stage4
        $newflag['4.w13'] = array("reason" => $old[1][2][13]['flag'], "attempts" => $old[1][2][13]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w14'] = array("reason" => $old[1][2][14]['flag'], "attempts" => $old[1][2][14]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w15'] = array("reason" => $old[1][2][15]['flag'], "attempts" => $old[1][2][15]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w16'] = array("reason" => $old[1][2][16]['flag'], "attempts" => $old[1][2][16]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w17'] = array("reason" => $old[1][2][17]['flag'], "attempts" => $old[1][2][17]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w18'] = array("reason" => $old[1][2][18]['flag'], "attempts" => $old[1][2][18]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w19'] = array("reason" => $old[1][2][19]['flag'], "attempts" => $old[1][2][19]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w20'] = array("reason" => $old[1][2][20]['flag'], "attempts" => $old[1][2][20]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w21'] = array("reason" => $old[1][2][21]['flag'], "attempts" => $old[1][2][21]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w22'] = array("reason" => $old[1][2][22]['flag'], "attempts" => $old[1][2][22]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w23'] = array("reason" => $old[1][2][23]['flag'], "attempts" => $old[1][2][23]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w24'] = array("reason" => $old[1][2][24]['flag'], "attempts" => $old[1][2][24]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w25'] = array("reason" => $old[1][2][25]['flag'], "attempts" => $old[1][2][25]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w26'] = array("reason" => $old[1][2][26]['flag'], "attempts" => $old[1][2][26]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w27'] = array("reason" => $old[1][2][27]['flag'], "attempts" => $old[1][2][27]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w28'] = array("reason" => $old[1][2][28]['flag'], "attempts" => $old[1][2][28]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w29'] = array("reason" => $old[1][2][29]['flag'], "attempts" => $old[1][2][29]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w30'] = array("reason" => $old[1][2][30]['flag'], "attempts" => $old[1][2][30]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w31'] = array("reason" => $old[1][2][31]['flag'], "attempts" => $old[1][2][31]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w32'] = array("reason" => $old[1][2][32]['flag'], "attempts" => $old[1][2][32]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w33'] = array("reason" => $old[1][2][33]['flag'], "attempts" => $old[1][2][33]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w34'] = array("reason" => $old[1][2][34]['flag'], "attempts" => $old[1][2][35]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w35'] = array("reason" => $old[1][2][35]['flag'], "attempts" => $old[1][2][35]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w36'] = array("reason" => $old[1][2][36]['flag'], "attempts" => $old[1][2][36]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w37'] = array("reason" => $old[1][2][37]['flag'], "attempts" => $old[1][2][37]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w38'] = array("reason" => $old[1][2][38]['flag'], "attempts" => $old[1][2][38]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w39'] = array("reason" => $old[1][2][39]['flag'], "attempts" => $old[1][2][39]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w40'] = array("reason" => $old[1][2][40]['flag'], "attempts" => $old[1][2][40]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w41'] = array("reason" => $old[1][2][41]['flag'], "attempts" => $old[1][2][41]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w42'] = array("reason" => $old[1][2][42]['flag'], "attempts" => $old[1][2][42]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w43'] = array("reason" => $old[1][2][43]['flag'], "attempts" => $old[1][2][43]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w44'] = array("reason" => $old[1][2][44]['flag'], "attempts" => $old[1][2][44]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w45'] = array("reason" => $old[1][2][45]['flag'], "attempts" => $old[1][2][45]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w46'] = array("reason" => $old[1][2][46]['flag'], "attempts" => $old[1][2][46]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w47'] = array("reason" => $old[1][2][47]['flag'], "attempts" => $old[1][2][47]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w48'] = array("reason" => $old[1][2][48]['flag'], "attempts" => $old[1][2][48]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w49'] = array("reason" => $old[1][2][49]['flag'], "attempts" => $old[1][2][49]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w50'] = array("reason" => $old[1][2][50]['flag'], "attempts" => $old[1][2][50]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w51'] = array("reason" => $old[1][2][51]['flag'], "attempts" => $old[1][2][51]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $newflag['4.w52'] = array("reason" => $old[1][2][52]['flag'], "attempts" => $old[1][2][52]['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
        $changedflag = json_encode($newflag);
        return $changedflag;
    }

    public function update_status(){
        $presentgestage=0;
        $list=array();
        date_default_timezone_set('Asia/Calcutta');
        $this->Migrate->setDataSource('default');
        $result = $this->User->query("SELECT * FROM users u join projects p on u.project_id=p.id");
        $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
        $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
        foreach($result as $r){
            $stage = "stage" . $r['u']['stage'];
            if($r['u']['stage']>0) {
                if(!empty($r['p']['stage_structure'])){
                    $structure = json_decode($r['p']['stage_structure'],true);
                    $callfrequency = $structure[$stage]['callfrequency'];
                }
                if ($r['u']['delivery'] == 0) {
                    if (isset($r['u']['lmp'])) {
                        $date1 = strtotime($r['u']['lmp']);
                        $gest_age = 0;
                    } else {
                        $date1 = strtotime($r['u']['registration_date']);
                        $gest_age = $r['u']['enroll_gest_age'];
                    }
                } elseif ($r['u']['delivery'] == 1) {
                    $gest_age = 0;
                    $date1 = strtotime($r['u']['delivery_date']);
                }
                $date2 = time();
                $cf = $frequency[$callfrequency];
                $presentgestage =$this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
            }
            if($presentgestage>39 && $r['u']['delivery'] == 0)
            {
                    $list[]=array(
                        "id"  =>$r['u']['id'],
                        "name"=>$r['u']['name'],
                        "project_id"=>$r['p']['id']
                        );
            }
        }
        $this->set('users', $list);
        //print_r($list);exit;
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

?>