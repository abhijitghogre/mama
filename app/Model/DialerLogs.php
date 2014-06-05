<?php 
class DialerLogs extends AppModel {
    var $name = 'DialerLogs';
    
    public function makeEntry($startdatetime, $phoneno, $gest_age, $reason, $userid, $tid){
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, userid, tid) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$userid', '$tid')");
        return TRUE;
    }
    public function updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus){
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus WHERE tid = '$tid'");
        return TRUE;
    }
}
?>
