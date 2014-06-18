<?php

class LogsController extends AppController {

    public $uses = array('User', 'DialerLogs', 'UserCallflags', 'Missedcall', 'Project');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function index() {
        $this->set('logsActive', 1);
        $current_week_monday = date('Y-m-d', strtotime('monday this week'));
        $projects = $this->Project->find('all');
        $users = $this->User->find('all', array('conditions' => array('User.deleted' => 0), 'order' => array('User.name')));
        $this->set('$projects', $projects);
        $this->set('users', $users);

        $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.user_id
                                        join projects p on u.project_id = p.id
                                        join user_callflags uc on u.id = uc.user_id
                                        WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
        $this->set('result', $result);
        //echo "<pre>";
        //print_r($result);exit;
    }

    public function generate_report() {
        date_default_timezone_set('Asia/Calcutta');
        $current_week_monday = date('Y-m-d', strtotime('monday -7 days'));
        $this->layout = 'blank';
        if (isset($_POST)) {
            $projectId = $_POST['projectId'];
            $userId = $_POST['userId'];
            $callCode = $_POST['callCode'];
            $fromDate = $_POST['fromDate'];
            $toDate = $_POST['toDate'];

            $filter = "";
            if ($projectId != 0) {
                $filter = "u.project_id = $projectId";
            }
            if ($userId != 0) {
                $filter = !empty($filter) ? "$filter and u.id = $userId" : "u.id = $userId";
            }
            if ($callCode >= 0) {
                $filter = !empty($filter) ? "$filter and d.callstatus =$callCode" : "d.callstatus =$callCode";
            }
            if (!empty($fromDate) && !empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                $to = date("Y-m-d", strtotime($toDate));
                if ($fromDate == $toDate) {
                    $filter = !empty($filter) ? "$filter and date(d.startdatetime) = '$from'" : "date(d.startdatetime) = '$from'";
                } else {
                    $filter = !empty($filter) ? "$filter and date(d.startdatetime) BETWEEN '$from' and '$to'" : "date(d.startdatetime) BETWEEN '$from' and '$to'";
                }
            }
            if (!empty($fromDate) && empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                $filter = !empty($filter) ? "$filter and date(d.startdatetime) >= '$from'" : "date(d.startdatetime) >= '$from'";
            }
            if (empty($fromDate) && !empty($toDate)) {
                $to = date("Y-m-d", strtotime($toDate));
                $filter = !empty($filter) ? "$filter and date(d.startdatetime) <= '$to'" : "date(d.startdatetime) <= '$to'";
            }
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.user_id
                                            join projects p on u.project_id = p.id
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE " . $filter . " GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        } else {
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.user_id
                                            join user_meta um on u.id = um.user_id
                                            join projects p on u.project_id = p.id 
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        }
    }
    
    public function generate_csv($projectId,$userId,$callCode,$fromDate,$toDate) {
        date_default_timezone_set('Asia/Calcutta');
        $current_week_monday = date('Y-m-d', strtotime('monday -7 days'));
        $this->layout = 'blank';
        if ($projectId != 0 || $userId != 0 || $callCode >= 0 || !empty($fromDate) || !empty($toDate)) {
            $filter = "";
            if ($projectId != 0) {
                $filter = "u.project_id = $projectId";
            }
            if ($userId != 0) {
                $filter = !empty($filter) ? "$filter and u.id = $userId" : "u.id = $userId";
            }
            if ($callCode >= 0) {
                $filter = !empty($filter) ? "$filter and d.callstatus =$callCode" : "d.callstatus =$callCode";
            }
            if (!empty($fromDate) && !empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                $to = date("Y-m-d", strtotime($toDate));
                if ($fromDate == $toDate) {
                    $filter = !empty($filter) ? "$filter and date(d.startdatetime) = '$from'" : "date(d.startdatetime) = '$from'";
                } else {
                    $filter = !empty($filter) ? "$filter and date(d.startdatetime) BETWEEN '$from' and '$to'" : "date(d.startdatetime) BETWEEN '$from' and '$to'";
                }
            }
            if (!empty($fromDate) && empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                $filter = !empty($filter) ? "$filter and date(d.startdatetime) >= '$from'" : "date(d.startdatetime) >= '$from'";
            }
            if (empty($fromDate) && !empty($toDate)) {
                $to = date("Y-m-d", strtotime($toDate));
                $filter = !empty($filter) ? "$filter and date(d.startdatetime) <= '$to'" : "date(d.startdatetime) <= '$to'";
            }
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.user_id
                                            join projects p on u.project_id = p.id
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE " . $filter . " GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        } else {
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.user_id
                                            join user_meta um on u.id = um.user_id
                                            join projects p on u.project_id = p.id 
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        }
    }
    
    public function statistics() {
        $this->set('statsActive', 1);
        $projects = $this->Project->getAllProjects();
        $this->set('projects', $projects);
        
        if (!empty($_POST)){
            $projectId = $_POST['projectId'];
            $statsDate = $_POST['statsDate'];
            $filter = $_POST['filter'];
            $statstype = $_POST['statstype'];
            
            //Daily Statistics
            if($statstype == 1){
                $count = array('9:00 AM - 12:00 PM' => '0', '12:00 PM - 3:00 PM' => '0', '3:00 PM - 6:00 PM' => '0', '6:00 PM - 9:00 PM' => '0', '9:00 PM - 11:00 PM' => '0');
                if($filter == 1){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('date(DialerLogs.startdatetime)' =>date("Y-m-d", strtotime($statsDate))))));
                }elseif($filter == 2){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>0), array('date(DialerLogs.startdatetime)' =>date("Y-m-d", strtotime($statsDate))))));
                }elseif($filter == 3){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>1), array('date(DialerLogs.startdatetime)' =>date("Y-m-d", strtotime($statsDate))))));
                }elseif($filter == 4){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.callstatus'=>0), array('date(DialerLogs.startdatetime)' =>date("Y-m-d", strtotime($statsDate))))));
                }elseif($filter == 5){
                
                }
                foreach ($result as $r) {
                    $slot = ((int) date("G", strtotime($r['DialerLogs']['startdatetime'])));
                    if ($slot >= 9 && $slot < 12) {
                        $count['9:00 AM - 12:00 PM'] = $count['9:00 AM - 12:00 PM'] + 1;
                    } elseif ($slot >= 12 && $slot < 15) {
                        $count['12:00 PM - 3:00 PM'] = $count['12:00 PM - 3:00 PM'] + 1;
                    } elseif ($slot >= 15 && $slot < 18) {
                        $count['3:00 PM - 6:00 PM'] = $count['3:00 PM - 6:00 PM'] + 1;
                    } elseif ($slot >= 18 && $slot < 21) {
                        $count['6:00 PM - 9:00 PM'] = $count['6:00 PM - 9:00 PM'] + 1;
                    } elseif ($slot >= 21 && $slot < 24) {
                        $count['9:00 PM - 11:00 PM'] = $count['9:00 PM - 11:00 PM'] + 1;
                    }
                }
                $data = array();
                foreach ($count as $k => $v){
                    array_push($data,array('slot'=>$k,'count'=>  intval($v)));
                }
                echo json_encode($data); exit;
            //Weekly Statistics
            }elseif($statstype == 2){
                $count = array('Sun' => '0', 'Mon' => '0', 'Tue' => '0', 'Wed' => '0', 'Thu' => '0', 'Fri' => '0', 'Sat' => '0');
                if($filter == 1){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('WEEKOFYEAR(DialerLogs.startdatetime)' =>date('W',strtotime($statsDate))))));
                }elseif($filter == 2){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>0), array('WEEKOFYEAR(DialerLogs.startdatetime)' =>date('W',strtotime($statsDate))))));
                }elseif($filter == 3){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>1), array('WEEKOFYEAR(DialerLogs.startdatetime)' =>date('W',strtotime($statsDate))))));
                }elseif($filter == 4){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.callstatus'=>0), array('WEEKOFYEAR(DialerLogs.startdatetime)' =>date('W',strtotime($statsDate))))));
                }elseif($filter == 5){
                    
                }
                foreach ($result as $r) {
                    $slot = date("D", strtotime($r['DialerLogs']['startdatetime']));
                    $count[$slot] = $count[$slot]+1;
                }
                $data = array();
                foreach ($count as $k => $v){
                    array_push($data,array('slot'=>$k,'count'=>intval($v)));
                }
                echo json_encode($data); exit;
            
            //Monthly Statistics
            }elseif($statstype == 3){
                $count = array('week1' => '0', 'week2' => '0', 'week3' => '0', 'week4' => '0');
                if($filter == 1){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('month(DialerLogs.startdatetime)' =>date('n',strtotime($statsDate))))));
                }elseif($filter == 2){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>0), array('month(DialerLogs.startdatetime)' =>date('n',strtotime($statsDate))))));
                }elseif($filter == 3){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>1), array('month(DialerLogs.startdatetime)' =>date('n',strtotime($statsDate))))));
                }elseif($filter == 4){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.callstatus'=>0), array('month(DialerLogs.startdatetime)' =>date('n',strtotime($statsDate))))));
                }elseif($filter == 5){
                
                }
                foreach ($result as $r) {
                    $slot = $this->getWeeks($r['DialerLogs']['startdatetime'], "Sunday");
                    if($slot == 1){
                        $count['week1'] = $count['week1']+1;
                    }elseif($slot == 2){
                        $count['week2'] = $count['week2']+1;
                    }elseif($slot == 3){
                        $count['week3'] = $count['week3']+1;
                    }elseif($slot == 4){
                        $count['week4'] = $count['week4']+1;
                    }
                }
                $data = array();
                foreach ($count as $k => $v){
                    array_push($data,array('slot'=>$k,'count'=>intval($v)));
                }
                echo json_encode($data); exit;
            //Yearly Statistics
            }elseif($statstype == 4){
                $count = array('Jan' => '0', 'Feb' => '0', 'Mar' => '0', 'Apr' => '0', 'May' => '0', 'Jun' => '0', 'Jul' => '0', 'Aug' => '0', 'Sep' => '0','Oct' => '0', 'Nov' => '0', 'Dec' => '0');
                if($filter == 1){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('year(DialerLogs.startdatetime)' =>date('Y',strtotime($statsDate))))));
                }elseif($filter == 2){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>0), array('year(DialerLogs.startdatetime)' =>date('Y',strtotime($statsDate))))));
                }elseif($filter == 3){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.reason'=>1), array('year(DialerLogs.startdatetime)' =>date('Y',strtotime($statsDate))))));
                }elseif($filter == 4){
                    $result = $this->DialerLogs->find('all', array('contain'=>array('User'), 'conditions' => array(array('User.project_id' => $projectId), array('DialerLogs.callstatus'=>0), array('year(DialerLogs.startdatetime)' =>date('Y',strtotime($statsDate))))));
                }elseif($filter == 5){
                    
                }
                foreach ($result as $r) {
                    $slot = date("M", strtotime($r['DialerLogs']['startdatetime']));
                    $count[$slot] = $count[$slot]+1;
                }
                $data = array();
                foreach ($count as $k => $v){
                    array_push($data,array('slot'=>$k,'count'=>intval($v)));
                }
                echo json_encode($data); exit;
            }
        }
    }
    function getWeeks($date, $rollover)
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for($i; $i<=$elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }

}

?>
