<?php 
class DialerLogs extends AppModel {
    var $name = 'DialerLogs';
    public $belongsTo = array('User');
    
    public function makeEntry($startdatetime, $phoneno, $gest_age, $reason, $message, $userid, $tid, $calltype, $mid){
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, message, user_id, tid, calltype, missedcall_id) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$message', '$userid', '$tid', '$calltype', '$mid')");
        return TRUE;
    }
    public function updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus, $dropreason){
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus, dropreason='$dropreason' WHERE tid = '$tid'");
        return TRUE;
    }
}
?>
