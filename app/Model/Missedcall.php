<?php 
class Missedcall extends AppModel {
    var $name = 'Missedcall';
    
    public function registeredUser($phoneno,$date_time,$missedcallno,$gest_age){
        $this->query("INSERT INTO missedcalls (phoneno,date_time,registered,missedcallno,gest_age) VALUES ('$phoneno','$date_time','1','$missedcallno','$gest_age')");
        return TRUE;
    }
    public function unregisteredUser($phoneno,$date_time,$missedcallno,$gest_age){
        $this->query("INSERT INTO missedcalls (phoneno,date_time,registered,missedcallno,gest_age) VALUES ('$phoneno','$date_time','0','$missedcallno','0')");
        return TRUE;
    }
    /*New code */
    public function registeredUser1($phoneno,$date_time,$missedcallno,$gest_age){
        $this->query("INSERT INTO missedcalls (phoneno,date_time,registered,missedcallno,gest_age) VALUES ('$phoneno','$date_time','1','$missedcallno','$gest_age')");
        return TRUE;
    }
    public function unregisteredUser1($phoneno,$date_time,$missedcallno,$gest_age){
        $this->query("INSERT INTO missedcalls (phoneno,date_time,registered,missedcallno,gest_age) VALUES ('$phoneno','$date_time','0','$missedcallno','0')");
        return TRUE;
    }
}
?>