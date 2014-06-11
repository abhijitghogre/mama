<?php
class ServiceController extends AppController {
    public $uses = array('User','DialerLogs','UserCallflags','Missedcall','Project');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('get_user','update','outbound','get_missedcall_info','call_summary','get_list');
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
            }elseif($current_slot >= 12 && $current_slot<15){
                $callslot = 2;
            }elseif($current_slot >= 15 && $current_slot<18){
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
            $call_flag = json_decode($r["UserCallflag"]["flag"], true);
            $slot = $r["User"]["call_slots"];
            $intro_call_flag = $r["UserCallflag"]["intro_call"];
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
        /*$callsarray[] = array(
                "gest_age" => 18,
                "userid" => 1,
                "phone_no" => 99877660711,
                "media" => "dfidhindi181"
        ); */
        echo "<pre>";
        print_r($callsarray);
        $this->outbound($callsarray);
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
            $call_flag = json_decode($r["UserCallflag"]["flag"], true);
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
            $data = explode(',', $response);
            $reason = 0;
            $tid = $data[1];
        }else{
            $reason = 1;
            $tid = 0;
        }
        $this->DialerLogs->makeEntry($startdatetime, $phoneno, $gest_age, $reason, $userid, $tid);
    }
    public function update(){
        date_default_timezone_set('Asia/Calcutta');
        $body = file_get_contents('php://input');
        $xml = simplexml_load_string($body);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $callsummary = $array['evt-notification']['evt-info'];
        $phoneno = $_GET['phoneno'];
        $tid = $callsummary['esbtransid'];
        $callstatus = $callsummary['drop-type'];
        $duration = $callsummary['call-duration'];
        $startdatetime = date("Y-m-d H:i:s",strtotime($callsummary['answered-on']));
        $enddatetime = date("Y-m-d H:i:s",strtotime($callsummary['released-on']));

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
        $decodedflag = json_decode($r["UserCallflag"]["flag"], true);
        $slot = $r["User"]["call_slots"];
        $intro_call_flag = $r["UserCallflag"]["intro_call"];
        $userid =  $r["User"]["id"];
        //intro call
        if((date("d-m-y",strtotime($r["User"]["entry_date"])) == date("d-m-y",$date2)) && ($intro_call_flag == 0)){
            $intro_call_flag = 1;
        //First Call after registration RGFC
        }elseif($intro_call_flag == 1){
            if($callstatus == 0){
                $intro_call_flag = 2;
            }
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

            $filename = WWW_ROOT."mamaLog.txt";
            $curdatetime = date("d m Y H:i:s");
            file_put_contents($filename, "Raw XML String: ".$body." XML object: ".$xml." Time: ".$curdatetime." phoneno: ".$phoneno."\n",FILE_APPEND);
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
    /* new code */
    public function stage_template(){
        $frequency = array("daily"=>"d","weekly"=>"w","monthly"=>"m","yearly"=>"y");
        $result = $this->Project->find('all');
        foreach($result as $r){
            $resultarray = array();
            $pid = $r["Project"]["id"];
            echo "project".$pid."</br>";
            $structure = json_decode($r["Project"]["stage_structure"], true);
            $stageno = 1;
            foreach($structure as $key=>$struct){
                $stagestart = $structure[$key]['stageduration']['start'];
                $stageend = $structure[$key]['stageduration']['end'];
                $callfrequency = $structure[$key]['callfrequency'];
                $cf = $frequency[$callfrequency];
                for($diff=$stagestart;$diff<=$stageend;$diff++){
                    for($msg=1;$msg<=$structure[$key]['numberofcalls'];$msg++){
                        $index = $stageno.".".$cf.$diff.".".$msg;
                        $flagvalue = array("reason"=>0,"attempts"=>0,"startdatetime"=>"","duration"=>0);
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
    public function get_list(){
        date_default_timezone_set('Asia/Calcutta'); 
        $current_time = time();
        $current_slot = ((int)date("G",$current_time));
        $current_day = date("D", $current_time);
        $result = $this->Project->find('all');
        $resultarray = array();
        $index = "";
        $languages = array("1"=>"english","2"=>"hindi", "3"=>"marathi");
        $project = array("1"=>"dfid","2"=>"glenmark");
        $frequency = array("daily"=>"d","weekly"=>"w","monthly"=>"m","yearly"=>"y");
        $diff = array("daily"=>"d","weekly"=>"ww","monthly"=>"m","yearly"=>"yyyy");
        foreach ($result as $r){
            $callsarray = array();
            $structure = json_decode($r["Project"]["stage_structure"], true);
            foreach($r['User'] as $u){
                $stage = "stage".$u['stage'];
                $user_id = $u['id'];
                $phoneno = $u['phone_no'];
                $project_id = $u['project_id'];
                $lang = $u['language'];
                $entry_date = date("d-m-y",strtotime($u['entry_date']));
                $callfrequency = $structure[$stage]['callfrequency'];
                $cf = $frequency[$callfrequency];
                if($u['delivery'] == 0){
                    if(isset($u["lmp"])){
                        $date1 = strtotime($u["lmp"]);
                        $gest_age = 0;
                    }else{
                        $date1 = strtotime($u["registration_date"]);
                        $gest_age = $u["enroll_gest_age"];
                    }
                }elseif($u['delivery'] == 1){
                    $date1 = strtotime($u['delivery_date']);
                }
                $date2 = $current_time;
                $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age ;
                $uc = $this->UserCallflags->find('first', array('conditions'=>array('UserCallflags.user_id'=>$user_id)));
                $intro_call = $uc['UserCallflags']['intro_call'];
                $callflag = json_decode($uc['UserCallflags']['flag'], true);
            }
            $stagestart = $structure[$stage]['stageduration']['start'];
            $stageend = $structure[$stage]['stageduration']['end'];
            if($r['Project']['id'] == $project_id && $presentgestage >= $stagestart && $presentgestage <= $stageend){
                $timearray = explode(':',date('H:i', $current_time));
                $current_slot = $timearray[0].$timearray[1];
                $stage_day = $structure[$stage]['callslotsdays'][$current_day];
                foreach($stage_day as $key=>$day){
                    if($current_slot >= $day['start'] && $current_slot < $day['end'] && $u['call_slots'] == $key){
                         if(($entry_date == date("d-m-y",$current_time)) && ($intro_call == 0)){
                            $index = "intro";
                            $callsarray[] = array(
                                            "gest_age" => $index,
                                            "user_id" => $user_id,
                                            "phoneno" => $phoneno,
                                            "media" => $project[$project_id].$languages[$lang].$index
                                        );
                        }else{
                            for($i=1;$i<=$structure[$stage]['numberofcalls'];$i++){
                                if(($structure[$stage]['callvolume']['call'.$i]['attempt'.$i] == $current_day) || ($structure[$stage]['callvolume']['call'.$i]['recall'.$i] == $current_day)){
                                    $index = $u['stage'].".".$cf.$presentgestage.".".$i;
                                    $newflag = array(); 
                                    if(array_key_exists($index , $callflag)){
                                        if($callflag[$index]['reason'] == 0 && $callflag[$index]['attempts'] < 6){
                                            $callsarray[] = array(
                                                    "gest_age" => $index,
                                                    "user_id" => $user_id,
                                                    "phoneno" => $phoneno,
                                                    "media" => $project[$project_id].$languages[$lang].$u['stage'].$cf.$presentgestage.$i
                                            );
                                        }
                                    }else{
                                        $callsarray[] = array(
                                                "gest_age" => $index,
                                                "user_id" => $user_id,
                                                "phoneno" => $phoneno,
                                                "media" => $project[$project_id].$languages[$lang].$u['stage'].$cf.$presentgestage.$i
                                        );
                                        $newflag = array("reason"=>0,"attempts"=>0,"startdatetime"=>"","duration"=>0);
                                        $callflag[$index] = $newflag;
                                        $encodedflag = json_encode($callflag);
                                        $this->UserCallflags->addFlag($encodedflag,$user_id);
                                    }
                                }
                            }
                        }
                        foreach ($callsarray as $callarr){
                            array_push($resultarray, $callarr);
                        }
                    }
                }
            }
        }
        echo "<pre>";
        print_r($resultarray);
        $calltype = "1";
        $this->outbound1($resultarray,$calltype);
        exit;
    }
    public function missedcall(){
        date_default_timezone_set('Asia/Calcutta'); 
         $phoneno = $_GET['msisdn'];
        $missedcalltime = date('Y-m-d H:i:s');
        $missedcallno = 1;
        
        $current_time = time();
        $current_slot = ((int)date("G",$current_time));
        $current_day = date("D", $current_time);
        
        $languages = array("1"=>"english","2"=>"hindi", "3"=>"marathi");
        $project = array("1"=>"dfid","2"=>"glenmark");
        $frequency = array("daily"=>"d","weekly"=>"w","monthly"=>"m","yearly"=>"y");
        $diff = array("daily"=>"d","weekly"=>"ww","monthly"=>"m","yearly"=>"yyyy");
        $resultarray = array();
        $index = "";
        $resultarray = array();
        $u = $this->User->find('first',array('conditions'=>array('User.phone_no'=>$phoneno), 'recursive' => 0));
        if(!empty($u)){
            $stage = "stage".$u['User']['stage'];
            $user_id = $u['User']['id'];
            $phoneno = $u['User']['phone_no'];
            $project_id = $u['User']['project_id'];
            $lang = $u['User']['language'];
            $entry_date = date("d-m-y",strtotime($u['User']['entry_date']));
            $structure = json_decode($u['Project']['stage_structure'], true);
            $stagestart = $structure[$stage]['stageduration']['start'];
            $stageend = $structure[$stage]['stageduration']['end'];
            $callfrequency = $structure[$stage]['callfrequency'];
            $cf = $frequency[$callfrequency];
            if($u['User']['delivery'] == 0){
                if(isset($u['User']["lmp"])){
                    $date1 = strtotime($u['User']["lmp"]);
                    $gest_age = 0;
                }else{
                    $date1 = strtotime($u['User']["registration_date"]);
                    $gest_age = $u['User']["enroll_gest_age"];
                }
            }elseif($u['delivery'] == 1){
                $date1 = strtotime($u['User']['delivery_date']);
            }
            $date2 = $current_time;
            $presentgestage = $this->datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age ;
            $intro_call = $u['UserCallflag']['intro_call'];
            $callflag = json_decode($u['UserCallflag']['flag'], true);
            if($presentgestage >= $stagestart && $presentgestage <= $stageend){
                if(($entry_date == date("d-m-y",$current_time)) && ($intro_call == 0)){
                    $index = "intro";
                    $resultarray[] = array(
                                    "gest_age" => $index,
                                    "user_id" => $user_id,
                                    "phoneno" => $phoneno,
                                    "media" => $project[$project_id].$languages[$lang].$index
                                );
                }else{
                    for($i=1;$i<=$structure[$stage]['numberofcalls'];$i++){
                        if(($structure[$stage]['callvolume']['call'.$i]['attempt'.$i] == $current_day) || ($structure[$stage]['callvolume']['call'.$i]['recall'.$i] == $current_day)){
                            $index = $u['User']['stage'].".".$cf.$presentgestage.".".$i;
                            $resultarray[] = array(
                                        "gest_age" => $index,
                                        "user_id" => $user_id,
                                        "phoneno" => $phoneno,
                                        "media" => $project[$project_id].$languages[$lang].$u['User']['stage'].$cf.$presentgestage.$i
                                );
                        }
                    }
                }
            }
            $calltype = "2";
            $this->outbound1($resultarray, $calltype);
            $this->Missedcall->registeredUser1($phoneno,$missedcalltime,$missedcallno,$index);
        }else{
            $index = 0;
            $this->Missedcall->unregisteredUser1($phoneno,$missedcalltime,$missedcallno,$index);
        }
        echo "<pre>";
        print_r($resultarray);exit;
    }
    public function outbound1($resultarray, $calltype){
        foreach($resultarray as $call){
            // Resource URL of the API
            $url = "http://api-openhouse.imimobile.com/1/obd/thirdpartycall/callSessions";

            // Service Provider's unique access key. Replace the secure Key associated with the registered service from your account on the website
            $key = 'd4a0bbc5-b665-4df7-813c-77eb035da0a3';
            
            $address = $call["phoneno"];

            //Optional Parameters
            $callbackurl = "http://herohelpline.org/mMitra-MAMA/service/update?phoneno=".$address."&index=".$call['gest_age'];

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
            
            $this->dialer_entry1($call,$response,$calltype);
        }
    }
    public function dialer_entry1($call,$response,$calltype){
        $user_id = $call["user_id"];
        $startdatetime = date('Y-m-d H:i:s');
        $gest_age = $call["gest_age"];
        $phoneno = $call["phoneno"];
        
        preg_match("/^(Success).*/", $response, $success);
        if($success){
            $data = explode(',', $response);
            $reason = 0;
            $tid = $data[1];
            $message = "";
        }else{
            $reason = 1;
            $tid = 0;
            $message = $response;
        }
        $this->DialerLogs->makeEntry1($startdatetime, $phoneno, $gest_age, $reason, $message, $user_id, $tid, $calltype);
    }
    public function update1(){
        date_default_timezone_set('Asia/Calcutta');
        ini_set("memory_limit", "256M");
        set_time_limit(0);
        
        $body = file_get_contents('php://input');
        $xml = simplexml_load_string($body);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $callsummary = $array['evt-notification']['evt-info'];
        $phoneno = $_GET['phoneno'];
        $index = $_GET['index'];
        $callstatus = 0;
        $tid = $callsummary['esbtransid'];
        $callstatus = $callsummary['drop-type'];
        $dropreason = $callsummary['drop-reason'];
        $duration = $callsummary['call-duration'];
        $startdatetime = date("Y-m-d H:i:s",strtotime($callsummary['answered-on']));
        $enddatetime = date("Y-m-d H:i:s",strtotime($callsummary['released-on']));

        $r = $this->User->find('first',array('conditions'=>array('User.phone_no'=>$phoneno), 'recursive' => 0));
        $user_id = $r['User']['id'];
        $intro_call = $r['UserCallflag']['intro_call'];
        $callflag = json_decode($r['UserCallflag']['flag'], true);
        if($index == "index"){
            if($callstatus == 0){
                $intro_call = 1;
            }
        }else{
            if($callstatus == 0){
                $callflag['reason'] = 1;
                $callflag['startdatetime'] = $startdatetime;
                $callflag['duration'] = $duration;
            }else{
                $callflag['attempts']++;
            }
        }
        $encodedflag = json_encode($callflag);
        $this->UserCallflags->updateFlag1($encodedflag, $startdatetime, $intro_call, $user_id);
        $this->DialerLogs->updateEntry1($startdatetime, $enddatetime, $duration, $tid, $callstatus, $dropreason);

        $filename = WWW_ROOT."mamaLog.txt";
        $curdatetime = date("d m Y H:i:s");
        file_put_contents($filename, "Raw XML String: ".$body." XML object: ".$xml." Time: ".$curdatetime." phoneno: ".$phoneno."\n",FILE_APPEND);
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
