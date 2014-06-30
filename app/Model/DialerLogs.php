<?php

class DialerLogs extends AppModel {

    var $name = 'DialerLogs';
    public $belongsTo = array('User');

    public function makeEntry($startdatetime, $phoneno, $gest_age, $reason, $userid, $tid) {
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, user_id, tid) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$userid', '$tid')");
        return TRUE;
    }

    public function updateEntry($startdatetime, $enddatetime, $duration, $tid, $callstatus) {
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus WHERE tid = '$tid'");
        return TRUE;
    }

    /* New */

    public function makeEntry1($startdatetime, $phoneno, $gest_age, $reason, $message, $userid, $tid, $calltype) {
        $this->query("INSERT INTO dialer_logs (startdatetime, phoneno, gest_age, reason, message, user_id, tid, calltype) VALUES ('$startdatetime', '$phoneno', '$gest_age', '$reason', '$message', '$userid', '$tid', '$calltype')");
        return TRUE;
    }

    public function updateEntry1($startdatetime, $enddatetime, $duration, $tid, $callstatus, $dropreason) {
        $result = $this->query("UPDATE dialer_logs SET startdatetime = '$startdatetime', enddatetime = '$enddatetime', duration = '$duration', callstatus = $callstatus, dropreason='$dropreason' WHERE tid = '$tid'");
        return TRUE;
    }

    //only yesterdays data
    public function getUsersByProjectId($projectId) {
        return $this->query("SELECT DialerLogs.user_id,DialerLogs.gest_age FROM dialer_logs DialerLogs LEFT JOIN users User ON DialerLogs.user_id = User.id WHERE User.project_id = " . $projectId . " AND DATE(DialerLogs.startdatetime)=subdate(DATE(NOW()),1) GROUP BY DialerLogs.gest_age, DialerLogs.user_id");
    }

    public function getToBeCalledUsers($projectId) {
        return $this->query("SELECT count(DialerLogs.user_id) as count FROM dialer_logs DialerLogs LEFT JOIN users User ON DialerLogs.user_id = User.id WHERE User.project_id = " . $projectId . " AND DATE(DialerLogs.startdatetime)=subdate(DATE(NOW()),1) GROUP BY DialerLogs.gest_age, DialerLogs.user_id");
    }

    public function getFailedCallUsers($projectId) {
        return $this->query("SELECT count(DialerLogs.user_id) AS count,DialerLogs.message FROM dialer_logs DialerLogs LEFT JOIN users User ON DialerLogs.user_id = User.id WHERE User.project_id = " . $projectId . " AND DATE(DialerLogs.startdatetime)=subdate(DATE(NOW()),1) AND DialerLogs.reason <> 0 GROUP BY DialerLogs.message");
    }

    public function getSuccessfulCallUsers($projectId) {
        return $this->query("SELECT count(DialerLogs.user_id) AS count,DialerLogs.duration FROM dialer_logs DialerLogs LEFT JOIN users User ON DialerLogs.user_id = User.id WHERE User.project_id = " . $projectId . " AND DATE(DialerLogs.startdatetime)=subdate(DATE(NOW()),1) AND DialerLogs.duration > 0 GROUP BY DialerLogs.message, DialerLogs.user_id");
    }

    public function getMissedCallUsers($projectId) {
        return $this->query("SELECT count(DialerLogs.user_id) AS count,DialerLogs.reason FROM dialer_logs DialerLogs LEFT JOIN users User ON DialerLogs.user_id = User.id WHERE User.project_id = " . $projectId . " AND DATE(DialerLogs.startdatetime)=subdate(DATE(NOW()),1) AND DialerLogs.calltype = 2 GROUP BY DialerLogs.reason");
    }

}

?>
