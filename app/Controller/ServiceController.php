<?php
class ServiceController extends AppController {
    public $uses = array('User','DialerLogs','UserCallflags','Missedcall');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('get_user','update','outbound','get_missedcall_info','call_summary');
    }
    
    public function get_user() {
        ini_set("memory_limit", "256M");
        set_time_limit(0);
        date_default_timezone_set('Asia/Calcutta'); 
        $current_time = time();
        $current_slot = ((int)date("G",$current_time));
        $current_day = date("D", $current_time);
        $languages = array("1"=>"english","2"=>"hindi", "3"=>"marathi");
        $project = array("1"=>"dfid","2"=>"glenmark");
        $result = array();
        $callsarray = array();
        //changing call time window according to current day
        if($current_day == "Sun"){
            if($current_slot >= 9 && $current_slot<12){
                $callslot = 1;
            }elseif($current_slot >= 12 && $current_slot<13){
                $callslot = 2;
            }elseif($current_slot >= 13 && $current_slot<18){
                $callslot = 3;
            }elseif($current_slot >= 18 && $current_slot<21){
                $callslot = 4;
            }else{
                $callslot = 0;
            }
            $result = $this->User->find('all', array('conditions' => array(array('User.call_slots' => $callslot),array('User.deleted' => 0)),array('contain' => array('UserCallflags'))));
        }else{
            $window = (date("H:i",$current_time));
            if(($window >= "18:30") && ($window <= "23:00")){
                $result = $this->User->find('all', array('conditions' => array('User.deleted' => 0)),array('contain' => array('UserCallflags')));
            }
        }
        foreach($result as $r){
            $stdcode = "";
            if($r["User"]["phone_type"] == 2){
                    $stdcode = "0";
            } else if($r["User"]["phone_type"] == 4){
                    $stdcode = $r["User"]["phone_code"];
            }
            
            if(isset($r["User"]["lmp"])){
                    $date1 = strtotime($r["User"]["lmp"]);
                    $gest_age = 0;
            } else {
                    $date1 = strtotime($r["User"]["registration_date"]);
                    $gest_age = $r["User"]["enroll_gest_age"];
            }
            $date2 = $current_time;
            $presentgestage = $this->datediff('ww', $date1, $date2, true) + $gest_age ;
            $call_flag = json_decode($r["UserCallflags"]["flag"], true);
            $slot = $r["User"]["call_slots"];
            $intro_call_flag = $r["UserCallflags"]["intro_call"];
            $gestage = "";
            //intro call
            if((date("d-m-y",strtotime($r["User"]["entry_date"])) == date("d-m-y",$date2)) && ($intro_call_flag == 0)){
                $gestage = "intro";
                $callsarray[] = array(
                                    "gest_age" => $gestage,
                                    "userid" => $r["User"]["id"],
                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."intro"
                                );
            //First Call after registeration (RGFC)
            }elseif($intro_call_flag == 1){
                $entry_day = date("D", strtotime($r["User"]["entry_date"]));
                if((($r["User"]["project_id"] == 1 && $presentgestage >= 11) || ($r["User"]["project_id"] == 2 && $presentgestage >= 6)) && $presentgestage <= 39){
                    if($current_day == "Mon" && ($entry_day == "Fri" || $entry_day == "Sat" || $entry_day == "Sun")){
                        $gestage = $presentgestage.".1";
                        $callsarray[] = array(
                                            "gest_age" => $gestage,
                                            "userid" => $r["User"]["id"],
                                            "phone_no" => $stdcode.$r["User"]["phone_no"],
                                            "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."1"
                                        );
                    }elseif($current_day == "Thu" && ($entry_day == "Mon" || $entry_day == "Tue" || $entry_day == "Wed")){
                        $gestage = $presentgestage.".2";
                        $callsarray[] = array(
                                            "gest_age" => $gestage,
                                            "userid" => $r["User"]["id"],
                                            "phone_no" => $stdcode.$r["User"]["phone_no"],
                                            "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                        );
                    }
                }elseif($r["User"]["delivery"] == 1 ){
                    $date1 = strtotime($r["User"]["delivery_date"]);
                    $date2 = $current_time;
                    $daydiff = $this->datediff('d',$date1 , $date2, true) + 1;
                    $weekdiff = $this->datediff('ww',$date1 , $date2, true) +1;
                    $monthdiff = $this->datediff('m',$date1 , $date2, true) +1;
                    $yeardiff = $this->datediff('yyyy',$date1 , $date2, true) +1;
                    //STAGE 5
                    if($monthdiff > 12 && $yeardiff <= 5){
                        $gestage = "m".$monthdiff;
                        if(($current_day == "Mon" && ($entry_day == "Fri" || $entry_day == "Sat" || $entry_day == "Sun")) || ($current_day == "Thu" && ($entry_day == "Mon" || $entry_day == "Tue" || $entry_day == "Wed"))){
                            $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."m".$monthdiff
                                                ); 
                        }
                    }elseif($monthdiff > 4 && $monthdiff <= 12){
                        $gestage = "w".$monthdiff;
                        if(($current_day == "Mon" && ($entry_day == "Fri" || $entry_day == "Sat" || $entry_day == "Sun")) || ($current_day == "Thu" && ($entry_day == "Mon" || $entry_day == "Tue" || $entry_day == "Wed"))){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                        }
                    }elseif($weekdiff <= 12 && $weekdiff > 1){
                        if($current_day == "Mon" && ($entry_day == "Fri" || $entry_day == "Sat" || $entry_day == "Sun")){
                            $gestage = "w".$weekdiff.".1";
                            $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."1"
                                                ); 
                        }elseif($current_day == "Thu" && ($entry_day == "Mon" || $entry_day == "Tue" || $entry_day == "Wed")){
                            $gestage = "w".$weekdiff.".2";
                            $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                ); 
                        }
                    }elseif($weekdiff == 1){
                        $gestage = "d".$daydiff;
                        if(($current_day == "Mon" && ($entry_day == "Fri" || $entry_day == "Sat" || $entry_day == "Sun")) || ($current_day == "Thu" && ($entry_day == "Mon" || $entry_day == "Tue" || $entry_day == "Wed"))){
                            $callsarray[] = array(
                                            "gest_age" => $gestage,
                                            "userid" => $r["User"]["id"],
                                            "phone_no" => $stdcode.$r["User"]["phone_no"],
                                            "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."d".$daydiff
                                    ); 
                        }
                    }
                }
            // Call Scheduling
            }elseif($intro_call_flag == 2){
                //STAGE 1
                if($r["User"]["delivery"] == 0 && (($r["User"]["project_id"] == 1 && $presentgestage >= 11) || ($r["User"]["project_id"] == 2 && $presentgestage >= 6)) && $presentgestage <= 39){
                    //first call
                    if($call_flag[0][$presentgestage]["second_call"]["flag"] == 0 && $call_flag[0][$presentgestage]["first_call"]["flag"] == 0 && $call_flag[0][$presentgestage]["first_call"]["attempts"] < 6){
                        $gestage = $presentgestage.".1";
                        if((($current_day == "Sun" && $call_flag[0][$presentgestage]["first_call"]["attempts"] < 3) || $current_day == "Mon") && ($slot == 1 || $slot == 2 || $slot == 3)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."1"
                                            );
                        }elseif((($current_day == "Tue" && $call_flag[0][$presentgestage]["first_call"]["attempts"] < 3) || $current_day == "Wed") && ($slot == 4)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."1"
                                            );
                        }
                    //second call
                    }elseif($call_flag[0][$presentgestage]["second_call"]["flag"] == 0 && $call_flag[0][$presentgestage]["second_call"]["attempts"] < 6){
                        $gestage = $presentgestage.".2";
                        if((($current_day == "Wed" && $call_flag[0][$presentgestage]["second_call"]["attempts"] < 3) || $current_day == "Thu") && ($slot == 1)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                            );
                        }elseif((($current_day == "Thu" && $call_flag[0][$presentgestage]["second_call"]["attempts"] < 3) || $current_day == "Fri") && ($slot == 2)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                            );
                        }elseif((($current_day == "Fri" && $call_flag[0][$presentgestage]["second_call"]["attempts"] < 3) || $current_day == "Sat") && ($slot == 3)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                            );
                        }elseif((($current_day == "Sat" && $call_flag[0][$presentgestage]["second_call"]["attempts"] < 3) || $current_day == "Sun") && ($slot == 4)){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                            );
                        }

                    }
                }elseif($r["User"]["delivery"] == 1 ){
                    $date1 = strtotime($r["User"]["delivery_date"]);
                    $date2 = $current_time;
                    $daydiff = $this->datediff('d',$date1 , $date2, true) + 1;
                    $weekdiff = $this->datediff('ww',$date1 , $date2, true) +1;
                    $monthdiff = $this->datediff('m',$date1 , $date2, true) +1;
                    $yeardiff = $this->datediff('yyyy',$date1 , $date2, true) +1;
                    //STAGE 5
                    if($monthdiff > 12 && $yeardiff <= 5){
                        $gestage = "m".$monthdiff;
                        if($call_flag[1][3][$monthdiff]["flag"] == 0 && $call_flag[1][3][$monthdiff]["attempts"] < 48){
                            $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."m".$monthdiff
                                            ); 
                        }
                    //STAGE 4
                    }elseif($monthdiff > 4 && $monthdiff <= 12){
                        $gestage = "w".$monthdiff;
                        if($call_flag[1][2][$weekdiff]["flag"] == 0 && $call_flag[1][2][$weekdiff]["attempts"] < 6){
                            if((($current_day == "Sun" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Mon") && ($slot == 1)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Mon" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Tue") && ($slot == 2)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Tue" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Wed") && ($slot == 3)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Wed" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Thu") && ($slot == 4)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Thu" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Fri") && ($slot == 5)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Fri" && $call_flag[1][2][$weekdiff]["attempts"] < 3)|| $current_day == "Sat") && ($slot == 6)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }elseif((($current_day == "Sat" && $call_flag[1][2][$weekdiff]["attempts"] < 3) || $current_day == "Sun") && ($slot == 7)){
                                $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                            }
                        }
                    //STAGE 3
                    }elseif($weekdiff <= 12 && $weekdiff > 1){
                        //first call
                        if($call_flag[1][1][$weekdiff]["second_call"]["flag"] == 0 && $call_flag[1][1][$weekdiff]["first_call"]["flag"] == 0 && $call_flag[1][1][$weekdiff]["first_call"]["attempts"] < 6 ){
                            $gestage = "w".$weekdiff.".1";
                            if((($current_day == "Sun" && $call_flag[1][1][$weekdiff]["first_call"]["attempts"] < 3) || $current_day == "Mon") && ($slot == 1 || $slot == 2 || $slot == 3)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."1"
                                                ); 
                            }elseif((($current_day == "Tue" && $call_flag[1][1][$weekdiff]["first_call"]["attempts"] < 3) || $current_day == "Wed") && ($slot == 4)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."1"
                                                ); 
                            }
                        //second call
                        }elseif($call_flag[1][1][$weekdiff]["second_call"]["flag"] == 0 && $call_flag[1][1][$weekdiff]["second_call"]["attempts"] < 6){
                            $gestage = "w".$weekdiff.".2";
                            if((($current_day == "Wed" && $call_flag[1][1][$weekdiff]["second_call"]["attempts"] < 3) || $current_day == "Thu") && ($slot == 1)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                );
                            }elseif((($current_day == "Thu" && $call_flag[1][1][$weekdiff]["second_call"]["attempts"] < 3) || $current_day == "Fri") && ($slot == 2)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                );
                            }elseif((($current_day == "Fri" && $call_flag[1][1][$weekdiff]["second_call"]["attempts"] < 3) || $current_day == "Sat") && ($slot == 3)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                );
                            }elseif((($current_day == "Sat" && $call_flag[1][1][$weekdiff]["second_call"]["attempts"] < 3) || $current_day == "Sun") && ($slot == 4)){
                                $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                );
                            }
                        }
                    //STAGE 2
                    }elseif($weekdiff == 1){
                        if($call_flag[1][0][$daydiff]["flag"] == 0 && $call_flag[1][0][$daydiff]["attempts"] < 3){
                            $gestage = "d".$daydiff;
                            $callsarray[] = array(
                                            "gest_age" => $gestage,
                                            "userid" => $r["User"]["id"],
                                            "phone_no" => $stdcode.$r["User"]["phone_no"],
                                            "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."d".$daydiff
                                    ); 
                        }
                    }
                }
            }
            
        }
        echo "<pre>";
        print_r($callsarray);
        //$this->outbound($callsarray);
        exit;
    }
    public function get_missedcall_info(){
        date_default_timezone_set('Asia/Calcutta');
        //get data from the query string
        $phoneno = $_GET['msisdn'];
        $missedcalltime = date('Y-m-d H:i:s');
        $missedcallno = 1;
        
        $current_time = time();
        $current_slot = ((int)date("G",$current_time));
        $current_day = date("D", $current_time);
        $languages = array("1"=>"english","2"=>"hindi", "3"=>"marathi");
        $project = array("1"=>"dfid","2"=>"glenmark");
        $callsarray = array();
        
        //get details of that user from phone number
        $r = $this->User->getUserFromPhone($phoneno);
        if(!empty($r)){
            $stdcode = "";
            if($r["User"]["phone_type"] == 2){
                    $stdcode = "0";
            } else if($r["User"]["phone_type"] == 4){
                    $stdcode = $r["User"]["phone_code"];
            }
            
            if(isset($r["User"]["lmp"])){
                    $date1 = strtotime($r["User"]["lmp"]);
                    $gest_age = 0;
            } else {
                    $date1 = strtotime($r["User"]["registration_date"]);
                    $gest_age = $r["User"]["enroll_gest_age"];
            }
            $date2 = $current_time;
            $presentgestage = $this->datediff('ww', $date1, $date2, true) + $gest_age ;
            $call_flag = json_decode($r["UserCallflags"]["flag"], true);
            $slot = $r["User"]["call_slots"];
            $gestage = "";
            //STAGE 1
            if($r["User"]["delivery"] == 0 && (($r["User"]["project_id"] == 1 && $presentgestage >= 11) || ($r["User"]["project_id"] == 2 && $presentgestage >= 6)) && $presentgestage <= 39){
                //first call
                if($current_day == "Sun" || $current_day == "Mon" || $current_day == "Tue"){
                    $gestage = $presentgestage.".1";
                    $callsarray[] = array(
                                        "gest_age" => $gestage,
                                        "userid" => $r["User"]["id"],
                                        "phone_no" => $stdcode.$r["User"]["phone_no"],
                                        "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."1"
                                    );
                //second call
                }elseif($current_day == "Wed" || $current_day == "Thu" || $current_day == "Fri" || $current_day == "Sat"){
                    $gestage = $presentgestage.".2";
                    $callsarray[] = array(
                                        "gest_age" => $gestage,
                                        "userid" => $r["User"]["id"],
                                        "phone_no" => $stdcode.$r["User"]["phone_no"],
                                        "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]].$presentgestage."2"
                                    );
                }
            }elseif($r["User"]["delivery"] == 1 ){
                    $date1 = $date1 = strtotime($r["User"]["delivery_date"]);
                    $date2 = $current_time;
                    $daydiff = $this->datediff('d',$date1 , $date2, true) + 1;
                    $weekdiff = $this->datediff('ww',$date1 , $date2, true) +1;
                    $monthdiff = $this->datediff('m',$date1 , $date2, true) +1;
                    $yeardiff = $this->datediff('yyyy',$date1 , $date2, true) +1;
                    //STAGE 5
                    if($monthdiff > 12 && $yeardiff <= 5){
                        $gestage = "m".$monthdiff;
                        $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."m".$monthdiff
                                            ); 
                    //STAGE 4
                    }elseif($monthdiff > 4 && $monthdiff <= 12){
                        $gestage = "w".$monthdiff;
                        $callsarray[] = array(
                                                "gest_age" => $gestage,
                                                "userid" => $r["User"]["id"],
                                                "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."ws".$weekdiff
                                            );
                    //STAGE 3
                    }elseif($weekdiff <= 12 && $weekdiff > 1){
                        //first call
                        if($current_day == "Sun" || $current_day == "Mon" || $current_day == "Tue"){
                           $gestage = "w".$weekdiff.".1";
                           $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."1"
                                                ); 
                        //second call
                        }elseif($current_day == "Wed" || $current_day == "Thu" || $current_day == "Fri" || $current_day == "Sat"){
                            $gestage = "w".$weekdiff.".2";
                            $callsarray[] = array(
                                                    "gest_age" => $gestage,
                                                    "userid" => $r["User"]["id"],
                                                    "phone_no" => $stdcode.$r["User"]["phone_no"],
                                                    "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."w".$weekdiff."2"
                                                );
                        }
                    //STAGE 2
                    }elseif($weekdiff == 1){
                        $gestage = "d".$daydiff;
                        $callsarray[] = array(
                                        "gest_age" => $gestage,
                                        "userid" => $r["User"]["id"],
                                        "phone_no" => $stdcode.$r["User"]["phone_no"],
                                        "media" => $project[$r["User"]["project_id"]].$languages[$r["User"]["language"]]."d".$daydiff
                                );
                    }
            }
            $this->outbound($callsarray);
            $this->Missedcall->registeredUser($phoneno,$missedcalltime,$missedcallno,$gestage);
        }else{
            $gestage = 0;
            $this->Missedcall->unregisteredUser($phoneno,$missedcalltime,$missedcallno,$gestage);
        }
        exit;
    }
    public function outbound($callsarray){
        foreach($callsarray as $call){
            // Resource URL of the API
            $url = "http://api-openhouse.imimobile.com/1/obd/thirdpartycall/callSessions";

            // Service Provider's unique access key. Replace the secure Key associated with the registered service from your account on the website
            $key = 'd4a0bbc5-b665-4df7-813c-77eb035da0a3';
            
            $address = $call["phone_no"];

            //Optional Parameters
            $callbackurl = "http://herohelpline.org/mMitra-MAMA/service/update?phoneno=".$address;

            //If Mode is media,uncomment the below line 
            $rawdata="address=!address!&mode=Media&callbackurl=!callbackurl!&medianame=!medianame!";

            $rawdata = str_replace("!address!", "$address", $rawdata);
            $rawdata = str_replace("!callbackurl!", "$callbackurl", $rawdata);
            //$rawdata = str_replace("!sendername!", "$sendername", $rawdata);

            //Uncomment the below two lines if mode is media
            $medianame = $call["media"];
            //$medianame = "Test";
            $rawdata=str_replace("!medianame!","$medianame" ,$rawdata);

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
            echo "</br>Headers         : " . $headers[0] . " , " .$headers[1];
            echo "</br>Input Request   : " . $rawdata;
            echo "</br>Response        : " . $response;
            echo "</br>=================================================================";
            
            $this->dialer_entry($call,$response);
        }
    }
    public function dialer_entry($call,$response){
        $userid = $call["userid"];
        $startdatetime = date('Y-m-d H:i:s');
        $gest_age = $call["gest_age"];
        $phoneno = $call["phone_no"];
        
        preg_match("/^(Success).*/", $response, $success);
        if($success){
            $data = explode(':', $response);
            $reason = 0;
            $tid = $data[2];
        }else{
            $reason = 1;
            $tid = 0;
        }
        $this->DialerLogs->makeEntry($startdatetime, $phoneno, $gest_age, $reason, $userid, $tid);
    }
    public function update(){
        if (isset($_GET)) {
            $phoneno = $_GET['phoneno'];
            $result = $this->DialerLogs->getTid($phoneno);
            $tid = $result[0]["dialer_logs"]["tid"];
            $callsummary = $this->call_summary($tid);
            $callstatus = $callsummary['status'];
            $msisdn = $callsummary['msisdn'];
            $duration = $callsummary['callduration'];
            $startdatetime = date("Y-m-d H:i:s",strtotime($callsummary['answered_on']));
            $enddatetime = date("Y-m-d H:i:s",strtotime($callsummary['released_on']));
            
            ini_set("memory_limit", "256M");
            set_time_limit(0);
            date_default_timezone_set('Asia/Calcutta'); 
            $current_time = time();
            $current_day = date("D", $current_time);
            $languages = array("1"=>"english","2"=>"hindi", "3"=>"marathi");
            $project = array("1"=>"dfid","2"=>"glenmark");
            $r = $this->User->find('first', array('conditions' => array('User.phone_no' => $phoneno),array('contain' => array('UserCallflags'))));

            if(isset($r["User"]["lmp"])){
                $date1 = strtotime($r["User"]["lmp"]);
                $gest_age = 0;
            }else{
                $date1 = strtotime($r["User"]["registration_date"]);
                $gest_age = $r["User"]["enroll_gest_age"];
            }
            $date2 = $current_time;
            $presentgestage = $this->datediff('ww', $date1, $date2, true) + $gest_age ;
            $decodedflag = json_decode($r["UserCallflags"]["flag"], true);
            $slot = $r["User"]["call_slots"];
            $intro_call_flag = $r["UserCallflags"]["intro_call"];
            $userid =  $r["User"]["id"];
            //intro call
            if((date("d-m-y",strtotime($r["User"]["entry_date"])) == date("d-m-y",$date2)) && ($intro_call_flag == 0)){
                $intro_call_flag = 1;
            //First Call after registration RGFC
            }elseif($intro_call_flag == 1){
                $intro_call_flag = 2;
                //STAGE 1
                if($r["User"]["delivery"] == 0 && $presentgestage >0 && $presentgestage <= 39){
                    //first call
                    if($current_day == "Mon"){
                            if($callstatus == 0){
                                $decodedflag[0][$presentgestage]["first_call"]["flag"] = 1;
                            }else{
                                $decodedflag[0][$presentgestage]["first_call"]["attempts"]++;
                            }
                    //second call
                    }elseif($current_day == "Thu"){
                        if($callstatus == 0){
                            $decodedflag[0][$presentgestage]["second_call"]["flag"] = 1;
                        }  else {
                            $decodedflag[0][$presentgestage]["second_call"]["attempts"]++;
                        }
                    }
                }elseif($r["User"]["delivery"] == 1 ){
                    $date1 = strtotime($r["User"]["delivery_date"]);
                    $date2 = $current_time;
                    $daydiff = $this->datediff('d',$date1 , $date2, true) + 1;
                    $weekdiff = $this->datediff('ww',$date1 , $date2, true) +1;
                    $monthdiff = $this->datediff('m',$date1 , $date2, true) +1;
                    $yeardiff = $this->datediff('yyyy',$date1 , $date2, true) +1;
                    //STAGE 5
                    if($monthdiff > 12 && $yeardiff <= 5){
                        if($callstatus == 0){
                            $decodedflag[1][3][$monthdiff]["flag"] = 1;
                        } else {
                            $decodedflag[1][3][$monthdiff]["attempts"]++;
                        }
                    //STAGE 4
                    }elseif($monthdiff > 4 && $monthdiff <= 12){
                        $gestage = "w".$monthdiff;
                        if($current_day == "Mon" || $current_day == "Thu"){
                            if($callstatus == 0){
                                $decodedflag[1][2][$weekdiff]["flag"] = 1;
                            }else{
                                $decodedflag[1][2][$weekdiff]["attempts"]++;
                            }
                        }
                    //STAGE 3
                    }elseif($weekdiff <= 12 && $weekdiff > 1){
                        //first call
                        if($current_day == "Mon"){
                            if($callstatus == 0){
                                $decodedflag[1][1][$weekdiff]["first_call"]["flag"] = 1;
                            } else {
                                $decodedflag[1][1][$weekdiff]["first_call"]["attempts"]++;
                            }
                        //second call
                        }elseif($current_day == "Thu"){
                            if($callstatus == 0){
                                $decodedflag[1][1][$weekdiff]["second_call"]["flag"] = 1;
                            } else {
                                $decodedflag[1][1][$weekdiff]["second_call"]["attempts"]++;
                            }
                        }
                    //STAGE 2
                    }elseif($weekdiff == 1){
                        if($callstatus == 0){
                            $decodedflag[1][0][$daydiff]["flag"] = 1;
                        }else{
                            $decodedflag[1][0][$daydiff]["attempts"]++;
                        }
                    }
                }
            // Call schecdule
            }else{
                //STAGE 1
                if($r["User"]["delivery"] == 0 && $presentgestage >0 && $presentgestage <= 39){
                    //first call
                    if((($current_day == "Sun" || $current_day == "Mon") && ($slot == 1 || $slot == 2 || $slot == 3)) || (($current_day == "Tue" || $current_day == "Wed") && ($slot == 4))){
                        if($callstatus == 0){
                            $decodedflag[0][$presentgestage]["first_call"]["flag"] = 1;
                        }else{
                            $decodedflag[0][$presentgestage]["first_call"]["attempts"]++;
                        }
                    //second call
                    }elseif((($current_day == "Wed" || $current_day == "Thu") && ($slot == 1)) || (($current_day == "Thu" || $current_day == "Fri") && ($slot == 2)) || (($current_day == "Fri" || $current_day == "Sat") && ($slot == 3)) || (($current_day == "Sat" || $current_day == "Sun") && ($slot == 4))){
                        if($callstatus == 0){
                            $decodedflag[0][$presentgestage]["second_call"]["flag"] = 1;
                        }  else {
                            $decodedflag[0][$presentgestage]["second_call"]["attempts"]++;
                        }
                    }
                }elseif($r["User"]["delivery"] == 1 ){
                        $date1 = strtotime($r["User"]["delivery_date"]);
                        $date2 = $current_time;
                        $daydiff = $this->datediff('d',$date1 , $date2, true) + 1;
                        $weekdiff = $this->datediff('ww',$date1 , $date2, true) +1;
                        $monthdiff = $this->datediff('m',$date1 , $date2, true) +1;
                        $yeardiff = $this->datediff('yyyy',$date1 , $date2, true) +1;
                        //STAGE 5
                        if($monthdiff > 12 && $yeardiff <= 5){
                            if($callstatus == 0){
                                $decodedflag[1][3][$monthdiff]["flag"] = 1;
                            } else {
                                $decodedflag[1][3][$monthdiff]["attempts"]++;
                            }
                        //STAGE 4
                        }elseif($monthdiff > 4 && $monthdiff <= 12){
                            $gestage = "w".$monthdiff;
                            if((($current_day == "Sun" || $current_day == "Mon") && ($slot == 1)) || (($current_day == "Mon" || $current_day == "Tue") && ($slot == 2)) || (($current_day == "Tue" || $current_day == "Wed") && ($slot == 3)) || (($current_day == "Wed" || $current_day == "Thu") && ($slot == 4)) || (($current_day == "Thu" || $current_day == "Fri") && ($slot == 5)) || (($current_day == "Fri" || $current_day == "Sat") && ($slot == 6)) || (($current_day == "Sat" || $current_day == "Sun") && ($slot == 7))){
                                if($callstatus == 0){
                                    $decodedflag[1][2][$weekdiff]["flag"] = 1;
                                }else{
                                    $decodedflag[1][2][$weekdiff]["attempts"]++;
                                }
                            }
                        //STAGE 3
                        }elseif($weekdiff <= 12 && $weekdiff > 1){
                            //first call
                            if((($current_day == "Sun" || $current_day == "Mon") && ($slot == 1 || $slot == 2 || $slot == 3)) || (($current_day == "Tue" || $current_day == "Wed") && ($slot == 4))){
                                if($callstatus == 0){
                                    $decodedflag[1][1][$weekdiff]["first_call"]["flag"] = 1;
                                } else {
                                    $decodedflag[1][1][$weekdiff]["first_call"]["attempts"]++;
                                }
                            //second call
                            }elseif((($current_day == "Wed" || $current_day == "Thu") && ($slot == 1)) || (($current_day == "Thu" || $current_day == "Fri") && ($slot == 2)) || (($current_day == "Fri" || $current_day == "Sat") && ($slot == 3)) || (($current_day == "Sat" || $current_day == "Sun") && ($slot == 4))){
                                if($callstatus == 0){
                                    $decodedflag[1][1][$weekdiff]["second_call"]["flag"] = 1;
                                } else {
                                    $decodedflag[1][1][$weekdiff]["second_call"]["attempts"]++;
                                }
                            }
                        //STAGE 2
                        }elseif($weekdiff == 1){
                            if($callstatus == 0){
                                $decodedflag[1][0][$daydiff]["flag"] = 1;
                            }else{
                                $decodedflag[1][0][$daydiff]["attempts"]++;
                            }
                        }
                    }
                }   
                $encondedflag = json_encode($decodedflag);
                $this->UserCallflags->updateFlag($encondedflag, $current_time, $intro_call_flag, $userid);
                $this->DialerLogs->updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus);
                /*$filename = WWW_ROOT."mamaLog.txt";
                $curdatetime = date("d m Y H:i:s");
                $logstring = "";
                $logstring .= $callstatus . " : " . $msisdn ." : ".$callduration." : ".$pulse." \n";
                $logstring = "Update callback url".$phoneno. " \n"; 
                file_put_contents($filename, $logstring."\n",FILE_APPEND);*/
        }
    }
    public function call_summary($tid)
    {
        $transid = 'urn:uuid:'.$tid;

        // Service Provider's unique access key. Replace the secure Key associated with the registered service from your account on the website
        $key = 'd4a0bbc5-b665-4df7-813c-77eb035da0a3';
        $url = 'http://api-openhouse.imimobile.com/1/voice/callstatus?tid='.$transid;
        $headers = array('key: ' . $key);

        $ch = curl_init($url);
        //Replace the secure Key associated with the registered service from your account on the website 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        echo $response;

        //echo htmlspecialchars($response);

        //converts response string to xml object
        $xml = simplexml_load_string($response);
        curl_close($ch);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $callsummary = $array['evt-info']['address'];
        
        return $callsummary;
        
        /*$filename = WWW_ROOT."mamaLog.txt";
        $curdatetime = date("d m Y H:i:s");

        file_put_contents($filename, "Raw XML String: ".$response." XML object: ".$xml." Time: ".$curdatetime."\n",FILE_APPEND);
        echo "=================================================================";
        echo "</br>Current date and time of execution is: " . $curdatetime;
        echo "</br>Resource URL    : " . $url;
        echo "</br>Headers         : " . $headers;
        echo "</br>Response        : " . print_r($xml);
        echo "</br>=================================================================";*/

        exit;

    }
    public function mamaTest(){
        // Get HTTP/HTTPS (the possible values for this vary from server to server)
        $myUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
        // Get domain portion
        $myUrl .= '://'.$_SERVER['HTTP_HOST'];
        // Get path to script
        //$myUrl .= $_SERVER['REQUEST_URI'];
        // Add path info, if any
        //if (!empty($_SERVER['PATH_INFO'])) $myUrl .= $_SERVER['PATH_INFO'];
        // Add query string, if any (some servers include a ?, some don't)
        if (!empty($_SERVER['QUERY_STRING'])) $myUrl .= '?'.ltrim($_SERVER['REQUEST_URI'],'?');
        $myUrl .= " ".date("d m Y H:i:s");
        $myUrl .= " get ".json_encode($_GET)." ";
        $myUrl .= " post ".json_encode($_POST)." ";
        $filename = WWW_ROOT."mamaLog.txt";
        file_put_contents($filename, $myUrl."\n",FILE_APPEND);
        exit;

        /*if (isset($_GET)) {
            $callstatus = $_GET['callstatus'];
            $msisdn = $_GET['msisdn'];
            $callduration = $_GET['callduration'];
            $pulse = $_GET['pulse'];
            $filename = WWW_ROOT."mamaLog.txt";
            $curdatetime = date("d m Y H:i:s");
            $logstring = "";
            $logstring .= $callstatus . " : " . $msisdn ." : ".$callduration." : ".$pulse." \n";
            file_put_contents($filename, $logstring."\n",FILE_APPEND);
            exit;
        }*/
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

        switch($interval) {

        case 'yyyy': // Number of full years

            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                $years_difference--;
            }
            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                $years_difference++;
            }
            $datediff = $years_difference;
            break;

        case "q": // Number of full quarters

            $quarters_difference = floor($difference / 8035200);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }
            $quarters_difference--;
            $datediff = $quarters_difference;
            break;

        case "m": // Number of full months

            $months_difference = floor($difference / 2678400);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
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
