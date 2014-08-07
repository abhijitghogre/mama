<?php

class ReportsController extends AppController {

    public $uses = array('User', 'DialerLogs', 'UserCallflags', 'Missedcall', 'Project', 'Channel');

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->Auth->allow();
        $this->set('reportsActive', 1);
    }

    public function index() {
        $this->set('reportsActive', 1);
        $current_week_monday = date('Y-m-d', strtotime('monday this week'));
        $projects = $this->Project->find('all');
        $channels = $this->Channel->find('all');
        $this->set('$projects', $projects);
        $this->set('channels', $channels);
    }

    public function report_data() {
        date_default_timezone_set('Asia/Calcutta');
        $current_week_monday = date('Y-m-d', strtotime('monday -7 days'));
        $this->layout = 'blank';
        if (isset($_POST)) {
            $projectId = $_POST['projectId'];
            $channelId = $_POST['channelId'];
            $other = $_POST['other'];
            $fromDate = $_POST['fromDate'];
            $toDate = $_POST['toDate'];

            $filter = "";
            if ($projectId != 0) {
                $filter = "u.project_id = $projectId";
            }
            if ($channelId != 0) {
                $filter = !empty($filter) ? "$filter and c.id = $channelId" : "c.id = $channelId";
            }
            if (!empty($fromDate) && !empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                $to = date("Y-m-d", strtotime($toDate));
                if($other == 1){
                    if ($fromDate == $toDate) {
                        $filter = !empty($filter) ? "$filter and date(u.entry_date) = '$from'" : "date(u.entry_date) = '$from'";
                    } else {
                        $filter = !empty($filter) ? "$filter and date(u.entry_date) BETWEEN '$from' and '$to'" : "date(u.entry_date) BETWEEN '$from' and '$to'";
                    }
                }
            }
            if (!empty($fromDate) && empty($toDate)) {
                $from = date("Y-m-d", strtotime($fromDate));
                if($other == 1){
                    $filter = !empty($filter) ? "$filter and date(u.entry_date) >= '$from'" : "date(u.entry_date) >= '$from'";
                }
            }
            if (empty($fromDate) && !empty($toDate)) {
                $to = date("Y-m-d", strtotime($toDate));
                if($other == 1){
                    $filter = !empty($filter) ? "$filter and date(u.entry_date) <= '$to'" : "date(u.entry_date) <= '$to'";
                }
            }
            $result = $this->User->query("SELECT * FROM users u join user_metas um on u.id=um.user_id
                                            join user_callflags uc on u.id=uc.user_id
                                            join projects p on u.project_id=p.id
                                            join channels c on u.channel_id=u.id
                                            join dialer_logs d on u.id=d.user_id WHERE ".$filter."");
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
