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
            $customFields['1407483198'] = $r['u']['rid'];
            $customFields['1407483223'] = $r['u']['village'];
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

    public function changecallflag($callflag) {
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
            $stage = 4;
            $freq = "w";
            $index = $stage . "." . $freq . $key;
            $flagvalue = array("reason" => $val['flag'], "attempts" => $val['attempts'], "startdatetime" => "", "duration" => 0, "missedcall" => 0);
            $newflag[$index] = $flagvalue;
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