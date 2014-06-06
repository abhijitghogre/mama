<?php

class LogsController extends AppController {

    public $uses = array('User', 'DialerLogs', 'UserCallflags', 'Missedcall', 'Project');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function index() {
        $current_week_monday = date('Y-m-d', strtotime('monday this week'));
        $projects = $this->Project->find('all');
        $users = $this->User->find('all', array('conditions' => array('User.deleted <>' => 1), 'order' => array('User.name')));
        $this->set('$projects', $projects);
        $this->set('users', $users);

        $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
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
            if ($callCode == 0) {
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
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
                                            join projects p on u.project_id = p.id
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE " . $filter . " GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        } else {
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
                                            join user_meta um on u.id = um.user_id
                                            join projects p on u.project_id = p.id 
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result', $result);
        }
    }

    public function statistics() {
        $projects = $this->Project->getAllProjects();
        $this->set('projects', $projects);
        
        if (!empty($_POST)) {
            $projectId = $_POST['projectId'];
            $statsDate = $_POST['statsDate'];
            $filter = $_POST['filter'];
            if ($filter == 1) {
                $count = array('9:00 AM - 12:00 PM' => '0', '12:00 PM - 3:00 PM' => '0', '3:00 PM - 6:00 PM' => '0', '6:00 PM - 9:00 PM' => '0', '9:00 PM - 11:00 PM' => '0');
                $result = $this->DialerLogs->find('all', array('conditions' => array('date(DialerLogs.startdatetime)' =>'2014-06-03')));
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
                foreach ($count as $k => $v) {
                    array_push($data,array('slot'=>$k,'calls'=>  intval($v)));
                }
                $array_final = preg_replace('/"([a-zA-Z_]+[a-zA-Z0-9_]*)":/','$1:',  json_encode($data));
                echo json_encode($array_final); exit;
//                return $statsdata;
            }
        }
    }

}

?>
