<?php 
class DialerLogs extends AppModel {
    var $name = 'DialerLogs';
    public $belongsTo = array('User');
    
    public function makeEntry($startdatetime, $phoneno, $gest_age, $reason, $userid, $tid){
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, user_id, tid) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$userid', '$tid')");
        return TRUE;
    }
    public function updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus){
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus WHERE tid = '$tid'");
        return TRUE;
    }
    /* New */
    public function makeEntry1($startdatetime, $phoneno, $gest_age, $reason, $message, $userid, $tid, $calltype){
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, message, user_id, tid, calltype) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$message', '$userid', '$tid', '$calltype')");
        return TRUE;
    }
    public function updateEntry1($startdatetime, $enddatetime, $duration, $tid, $callstatus, $dropreason){
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus, dropreason='$dropreason' WHERE tid = '$tid'");
        return TRUE;
    }
}
?>
