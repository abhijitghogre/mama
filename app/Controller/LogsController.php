<?php
class LogsController extends AppController {
    public $uses = array('User', 'DialerLogs', 'UserCallflags', 'Missedcall', 'Project');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }
    
    public function index() {
        $current_week_monday = date('Y-m-d',strtotime('monday this week'));
        $projects = $this->Project->find('all');
        $users = $this->User->find('all',array('conditions' => array('User.deleted <>'=>1), 'order' => array('User.name')));
        $this->set('$projects',$projects);
        $this->set('users',$users);
        
        $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
                                        join projects p on u.project_id = p.id
                                        join user_callflags uc on u.id = uc.user_id
                                        WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
        $this->set('result',$result);
        //echo "<pre>";
        //print_r($result);exit;
    }
    public function generate_report() {
        date_default_timezone_set('Asia/Calcutta');
        $current_week_monday = date('Y-m-d',strtotime('monday -7 days'));
        $this->layout = 'blank';
        if (isset($_POST)) {
            $projectId = $_POST['projectId'];
            $userId = $_POST['userId'];
            $callCode = $_POST['callCode'];
            $fromDate = $_POST['fromDate'];
            $toDate = $_POST['toDate'];

            $filter = "";
            if($projectId != 0){
                $filter = "u.project_id = $projectId";
            }
            if($userId != 0){
                $filter = !empty($filter)?"$filter and u.id = $userId":"u.id = $userId";
            }
            if($callCode == 0){
                $filter = !empty($filter)?"$filter and d.callstatus =$callCode":"d.callstatus =$callCode";
            }
            if(!empty($fromDate) && !empty($toDate)){
                $from = date("Y-m-d", strtotime($fromDate));
                $to = date("Y-m-d", strtotime($toDate));
                if($fromDate == $toDate){
                    $filter = !empty($filter)?"$filter and date(d.startdatetime) = '$from'":"date(d.startdatetime) = '$from'";
                }else{
                    $filter = !empty($filter)?"$filter and date(d.startdatetime) BETWEEN '$from' and '$to'":"date(d.startdatetime) BETWEEN '$from' and '$to'";
                }
            }
            if(!empty($fromDate) && empty($toDate)){
                $from = date("Y-m-d", strtotime($fromDate));
                $filter = !empty($filter)?"$filter and date(d.startdatetime) >= '$from'":"date(d.startdatetime) >= '$from'";
            }
            if(empty($fromDate) && !empty($toDate)){
                $to = date("Y-m-d", strtotime($toDate));
                $filter = !empty($filter)?"$filter and date(d.startdatetime) <= '$to'":"date(d.startdatetime) <= '$to'";
            }
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
                                            join projects p on u.project_id = p.id
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE ".$filter." GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result',$result);
        }else {
            $result = $this->DialerLogs->query("SELECT * FROM dialer_logs d join users u on u.id = d.userid
                                            join user_meta um on u.id = um.user_id
                                            join projects p on u.project_id = p.id 
                                            join user_callflags uc on u.id = uc.user_id
                                            WHERE d.startdatetime >= '$current_week_monday' GROUP BY d.startdatetime,u.phone_no ORDER BY d.startdatetime DESC");
            $this->set('result',$result);
        }
    }
}
?>
