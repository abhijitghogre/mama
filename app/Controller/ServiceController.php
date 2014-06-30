<?php

class ServiceController extends AppController {

    public $uses = array('User', 'DialerLogs', 'UserCallflags', 'Missedcall', 'Project');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('get_list', 'update', 'outbound', 'missedcall');
    }

    /* stage structure for each project */

    public function stage_template() {
        $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
        $result = $this->Project->find('all');
        foreach ($result as $r) {
            $resultarray = array();
            $pid = $r["Project"]["id"];
            echo "project" . $pid . "</br>";
            $structure = json_decode($r["Project"]["stage_structure"], true);
            $stageno = 1;
            foreach ($structure as $key => $struct) {
                $stagestart = $structure[$key]['stageduration']['start'];
                $stageend = $structure[$key]['stageduration']['end'];
                $callfrequency = $structure[$key]['callfrequency'];
                $cf = $frequency[$callfrequency];
                for ($diff = $stagestart; $diff <= $stageend; $diff++) {
                    for ($msg = 1; $msg <= $structure[$key]['numberofcalls']; $msg++) {
                        $index = $stageno . "." . $cf . $diff . "." . $msg;
                        $flagvalue = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                        $resultarray[$index] = $flagvalue;
                    }
                }
                $stageno++;
            }
            $encoded = json_encode($resultarray);
            $result = $this->Project->query("UPDATE projects SET template = '$encoded' WHERE id = $pid");
            echo "<pre>";
            print_r($resultarray);
        }
        exit;
    }

    /* cron to update stage for each woman */

    public function update_stage() {
        date_default_timezone_set('Asia/Calcutta');
        $current_time = time();
        $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
        $user = $this->User->find('all', array('recursive' => 0));
        foreach ($user as $u) {
            $stage = "stage" . $u['User']['stage'];
            $user_id = $u['User']['id'];
            $structure = json_decode($u["Project"]["stage_structure"], true);
            if ($u['User']['delivery'] == 0) {
                if (isset($u['User']['lmp'])) {
                    $date1 = strtotime($u['User']['lmp']);
                    $gest_age = 0;
                } else {
                    $date1 = strtotime($u['User']['registration_date']);
                    $gest_age = $u['User']['enroll_gest_age'];
                }
            } elseif ($u['User']['delivery'] == 1) {
                $date1 = strtotime($u['User']['delivery_date']);
            }
            $date2 = $current_time;
            $stageno = 1;
            foreach ($structure as $key => $struct) {
                $callfrequency = $structure[$key]['callfrequency'];
                $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
                $stagestart = $structure[$key]['stageduration']['start'];
                $stageend = $structure[$key]['stageduration']['end'];
                if ($presentgestage >= $stagestart && $presentgestage <= $stageend) {
                    $userstage = $stageno;
                }
                $stageno++;
            }
            echo $user_id . "-" . $userstage . "</br>";
            $this->User->updateStage($user_id, $userstage);
        }
        exit;
    }

    /* list of calls to be made */

    public function get_list() {
        date_default_timezone_set('Asia/Calcutta');
        $current_time = time();
        $current_slot = ((int) date("G", $current_time));
        $current_day = date("D", $current_time);
        $result = $this->Project->find('all');
        $resultarray = array();
        $index = "";
        $stdcode = "";
        $languages = array("1" => "english", "2" => "hindi", "3" => "marathi");
        $project = array("1" => "dfid", "2" => "glenmark");
        $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
        $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
        foreach ($result as $r) {
            $callsarray = array();
            $structure = json_decode($r["Project"]["stage_structure"], true);
            foreach ($r['User'] as $u) {
                $stage = "stage" . $u['stage'];
                $user_id = $u['id'];
                $phoneno = $u['phone_no'];
                if ($u["phone_type"] == 2) {
                    $stdcode = "0";
                } else if ($u["phone_type"] == 4) {
                    $stdcode = $u["phone_code"];
                }
                $project_id = $u['project_id'];
                $lang = $u['language'];
                $entry_date = date("d-m-y", strtotime($u['entry_date']));
                $callfrequency = $structure[$stage]['callfrequency'];
                $cf = $frequency[$callfrequency];
                if ($u['delivery'] == 0) {
                    if (isset($u["lmp"])) {
                        $date1 = strtotime($u["lmp"]);
                        $gest_age = 0;
                    } else {
                        $date1 = strtotime($u["registration_date"]);
                        $gest_age = $u["enroll_gest_age"];
                    }
                } elseif ($u['delivery'] == 1) {
                    $date1 = strtotime($u['delivery_date']);
                }
                $date2 = $current_time;
                $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
                $uc = $this->UserCallflags->find('first', array('conditions' => array('UserCallflags.user_id' => $user_id)));
                $intro_call = $uc['UserCallflags']['intro_call'];
                $callflag = json_decode($uc['UserCallflags']['flag'], true);
            }
            $stagestart = $structure[$stage]['stageduration']['start'];
            $stageend = $structure[$stage]['stageduration']['end'];
            if ($r['Project']['id'] == $project_id && $presentgestage >= $stagestart && $presentgestage <= $stageend) {
                $timearray = explode(':', date('H:i', $current_time));
                $current_slot = $timearray[0] . $timearray[1];
                $stage_day = $structure[$stage]['callslotsdays'][$current_day];
                foreach ($stage_day as $key => $day) {
                    if ($current_slot >= $day['start'] && $current_slot < $day['end'] && $u['call_slots'] == $key) {
                        if (($entry_date == date("d-m-y", $current_time)) && ($intro_call == 0)) {
                            $index = "intro";
                            $callsarray[] = array(
                                "gest_age" => $index,
                                "user_id" => $user_id,
                                "phoneno" => $stdcode . $phoneno,
                                "media" => $project[$project_id] . $languages[$lang] . $index
                            );
                        } else {
                            for ($i = 1; $i <= $structure[$stage]['numberofcalls']; $i++) {
                                if (($structure[$stage]['callvolume']['call' . $i]['attempt' . $i] == $current_day) || ($structure[$stage]['callvolume']['call' . $i]['recall' . $i] == $current_day)) {
                                    $index = $u['stage'] . "." . $cf . $presentgestage . "." . $i;
                                    $newflag = array();
                                    if (array_key_exists($index, $callflag)) {
                                        if ($callflag[$index]['reason'] == 0 && $callflag[$index]['attempts'] < 6) {
                                            $callsarray[] = array(
                                                "gest_age" => $index,
                                                "user_id" => $user_id,
                                                "phoneno" => $stdcode . $phoneno,
                                                "media" => $project[$project_id] . $languages[$lang] . $u['stage'] . $cf . $presentgestage . $i
                                            );
                                        }
                                    } else {
                                        $callsarray[] = array(
                                            "gest_age" => $index,
                                            "user_id" => $user_id,
                                            "phoneno" => $stdcode . $phoneno,
                                            "media" => $project[$project_id] . $languages[$lang] . $u['stage'] . $cf . $presentgestage . $i
                                        );
                                        $newflag = array("reason" => 0, "attempts" => 0, "startdatetime" => "", "duration" => 0, "missedcall" => 0);
                                        $callflag[$index] = $newflag;
                                        $encodedflag = json_encode($callflag);
                                        $this->UserCallflags->addFlag($encodedflag, $user_id);
                                    }
                                }
                            }
                        }
                        foreach ($callsarray as $callarr) {
                            array_push($resultarray, $callarr);
                        }
                    }
                }
            }
        }
        echo "<pre>";
        print_r($resultarray);
        $calltype = 1;
        $mid = 0;
        $this->outbound($resultarray, $calltype, $mid);
        exit;
    }

    public function missedcall() {
        date_default_timezone_set('Asia/Calcutta');
        $phoneno = $_GET['msisdn'];
        $missedcalltime = date('Y-m-d H:i:s');
        $missedcallno = 1;

        $current_time = time();
        $current_slot = ((int) date("G", $current_time));
        $current_day = date("D", $current_time);

        $languages = array("1" => "english", "2" => "hindi", "3" => "marathi");
        $project = array("1" => "dfid", "2" => "glenmark");
        $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
        $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
        $resultarray = array();
        $index = "";
        $resultarray = array();
        $u = $this->User->find('first', array('conditions' => array('User.phone_no' => $phoneno), 'recursive' => 0));
        if (!empty($u)) {
            $stdcode = "";
            if ($u["User"]["phone_type"] == 2) {
                $stdcode = "0";
            } else if ($u["User"]["phone_type"] == 4) {
                $stdcode = $u["User"]["phone_code"];
            }
            $stage = "stage" . $u['User']['stage'];
            $user_id = $u['User']['id'];
            $phoneno = $u['User']['phone_no'];
            $project_id = $u['User']['project_id'];
            $lang = $u['User']['language'];
            $entry_date = date("d-m-y", strtotime($u['User']['entry_date']));
            $structure = json_decode($u['Project']['stage_structure'], true);
            $stagestart = $structure[$stage]['stageduration']['start'];
            $stageend = $structure[$stage]['stageduration']['end'];
            $callfrequency = $structure[$stage]['callfrequency'];
            $cf = $frequency[$callfrequency];
            if ($u['User']['delivery'] == 0) {
                if (isset($u['User']["lmp"])) {
                    $date1 = strtotime($u['User']["lmp"]);
                    $gest_age = 0;
                } else {
                    $date1 = strtotime($u['User']["registration_date"]);
                    $gest_age = $u['User']["enroll_gest_age"];
                }
            } elseif ($u['delivery'] == 1) {
                $date1 = strtotime($u['User']['delivery_date']);
            }
            $date2 = $current_time;
            $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
            $intro_call = $u['UserCallflag']['intro_call'];
            $callflag = json_decode($u['UserCallflag']['flag'], true);
            if ($presentgestage >= $stagestart && $presentgestage <= $stageend) {
                if (($entry_date == date("d-m-y", $current_time)) && ($intro_call == 0)) {
                    $index = "intro";
                    $resultarray[] = array(
                        "gest_age" => $index,
                        "user_id" => $user_id,
                        "phoneno" => $stdcode . $phoneno,
                        "media" => $project[$project_id] . $languages[$lang] . $index
                    );
                } else {
                    for ($i = 1; $i <= $structure[$stage]['numberofcalls']; $i++) {
                        if (($structure[$stage]['callvolume']['call' . $i]['attempt' . $i] == $current_day) || ($structure[$stage]['callvolume']['call' . $i]['recall' . $i] == $current_day)) {
                            $index = $u['User']['stage'] . "." . $cf . $presentgestage . "." . $i;
                            $resultarray[] = array(
                                "gest_age" => $index,
                                "user_id" => $user_id,
                                "phoneno" => $stdcode . $phoneno,
                                "media" => $project[$project_id] . $languages[$lang] . $u['User']['stage'] . $cf . $presentgestage . $i
                            );
                        }
                    }
                }
            }
            $calltype = 2;
            $result = $this->Missedcall->registeredUser($phoneno, $missedcalltime, $missedcallno, $index);
            $mid = $result['mid'];
            $this->outbound($resultarray, $calltype, $mid);
        } else {
            $index = 0;
            $this->Missedcall->unregisteredUser($phoneno, $missedcalltime, $missedcallno, $index);
        }
        echo "<pre>";
        print_r($resultarray);
        exit;
    }

    public function outbound($resultarray, $calltype, $mid) {
        foreach ($resultarray as $call) {
            // Resource URL of the API
            $url = "http://api-openhouse.imimobile.com/1/obd/thirdpartycall/callSessions";

            // Service Provider's unique access key. Replace the secure Key associated with the registered service from your account on the website
            $key = 'd4a0bbc5-b665-4df7-813c-77eb035da0a3';

            $address = $call["phoneno"];

            //Optional Parameters
            $callbackurl = "http://herohelpline.org/mMitra-MAMA/service/update?phoneno=" . $address . "&index=" . $call['gest_age'] . "&calltype=" . $calltype;

            //If Mode is media,uncomment the below line 
            $rawdata = "address=!address!&mode=Media&callbackurl=!callbackurl!&medianame=!medianame!";

            $rawdata = str_replace("!address!", "$address", $rawdata);
            $rawdata = str_replace("!callbackurl!", "$callbackurl", $rawdata);
            //$rawdata = str_replace("!sendername!", "$sendername", $rawdata);
            //Uncomment the below two lines if mode is media
            $medianame = $call["media"];
            //$medianame = "Test";
            $rawdata = str_replace("!medianame!", "$medianame", $rawdata);

            $headers = array('Content-Type: application/x-www-form-urlencoded', 'key: ' . $key);

            //Curl variable to store headers and X-www-form-urlencoded field.
            $ch = curl_init($url);
            //1 stands for posting.
            curl_setopt($ch, CURLOPT_POST, 1);
            //Replace the secure Key associated with the registered service from your account on the website 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $rawdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $date = date('Y-m-d H:i:s');

            echo "=================================================================";
            echo "</br>Current date and time of execution is: " . $date;
            echo "</br>Resource URL    : " . $url;
            echo "</br>Headers         : " . $headers[0] . " , " . $headers[1];
            echo "</br>Input Request   : " . $rawdata;
            echo "</br>Response        : " . $response;
            echo "</br>=================================================================";

            $this->dialer_entry($call, $response, $calltype, $mid);
        }
    }

    public function dialer_entry($call, $response, $calltype, $mid) {
        $user_id = $call["user_id"];
        $startdatetime = date('Y-m-d H:i:s');
        $gest_age = $call["gest_age"];
        $phoneno = $call["phoneno"];

        preg_match("/^(Success).*/", $response, $success);
        if ($success) {
            $data = explode(',', $response);
            $reason = 0;
            $tid = $data[1];
            $message = "";
        } else {
            $reason = 1;
            $tid = 0;
            $message = $response;
        }
        $this->DialerLogs->makeEntry($startdatetime, $phoneno, $gest_age, $reason, $message, $user_id, $tid, $calltype, $mid);
    }

    /* update user callflags */

    public function update() {
        date_default_timezone_set('Asia/Calcutta');
        ini_set("memory_limit", "256M");
        set_time_limit(0);

        $body = file_get_contents('php://input');
        $xml = simplexml_load_string($body);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        $callsummary = $array['evt-notification']['evt-info'];
        $phoneno = $_GET['phoneno'];
        $index = $_GET['index'];
        $calltype = $_GET['calltype'];
        $callstatus = 0;
        $tid = $callsummary['esbtransid'];
        $callstatus = $callsummary['drop-type'];
        $dropreason = $callsummary['drop-reason'];
        $duration = $callsummary['call-duration'];
        $startdatetime = date("Y-m-d H:i:s", strtotime($callsummary['answered-on']));
        $enddatetime = date("Y-m-d H:i:s", strtotime($callsummary['released-on']));

        $r = $this->User->find('first', array('conditions' => array('User.phone_no' => $phoneno), 'recursive' => 0));
        $user_id = $r['User']['id'];
        $intro_call = $r['UserCallflag']['intro_call'];
        $callflag = json_decode($r['UserCallflag']['flag'], true);
        if ($index == "index") {
            if ($callstatus == 0) {
                $intro_call = 1;
            }
        } else {
            if ($calltype == 1) {
                if ($callstatus == 0) {
                    $callflag[$index]['reason'] = 1;
                    $callflag[$index]['startdatetime'] = $startdatetime;
                    $callflag[$index]['duration'] = $duration;
                } else {
                    $callflag[$index]['attempts'] ++;
                }
            } elseif ($calltype == 2) {
                $callflag[$index]['missedcall'] ++;
            }
        }
        $encodedflag = json_encode($callflag);
        $this->UserCallflags->updateFlag($encodedflag, $startdatetime, $intro_call, $user_id);
        $this->DialerLogs->updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus, $dropreason);

        $filename = WWW_ROOT . "mamaLog.txt";
        $curdatetime = date("d m Y H:i:s");
        file_put_contents($filename, "Raw XML String: " . $body . " XML object: " . $xml . " Time: " . $curdatetime . " phoneno: " . $phoneno . "\n", FILE_APPEND);
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
