<div class="row">
    <div class="col-md-12">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="report">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Project Name</th>
                    <th>Phone No.</th>
                    <th>Call Slot</th>
                    <th>Call Status</th>
                    <th>Drop reason</th>
                    <th>Call Date & Time</th>
                    <th>Call Duration</th>
                    <th>Gestational Age</th>
                    <th>Attempts</th>
                    <th>Delivery Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($result as $r): ?>
                    <tr>
                        <td><?php echo $r['u']['name']; ?></td>
                        <td><?php echo $r['p']['project_name']; ?></td>
                        <td><?php echo $r['d']['phoneno']; ?></td>
                        <td>
                            <?php
                                $slot = array('1'=>'9:00 AM to 12:00 PM', '2'=>'12:00 PM to 3:00 PM', '3'=>'3:00 PM to 6:00 PM', '4'=>'6:00 PM to 9:00 PM');
                                //echo $slot[$r['u']['call_slots']];
                                echo $r['u']['call_slots'];
                            ?>
                        </td>
                        <td>
                            <?php 
                                if(isset($r['d']['callstatus']) && $r['d']['callstatus'] == 0){
                                    echo "Success";
                                 }else{
                                    echo "Failure";
                                 }
                            ?>
                        </td>
                         <td>
                            <?php
                            if (!isset($r['d']['callstatus'])) {
                                if(!empty($r['d']['message']))
                                {
                                    echo $r['d']['message'];
                                }
                                else if(!empty($r['d']['dropreason'])){
                                           echo $r['d']['dropreason'];
                                        }
                                        else{
                                            echo "Not updated delivery status";
                                        }
                            }else{
                                echo "-";
                            }
                            ?>
                        </td>
                        <td><?php echo date("d-m-Y H:i:s D", strtotime($r['d']['startdatetime'])); ?></td>
                        <td>
                            <?php 
                                $duration = $r['d']['duration'];
                                if($duration == 0){
                                    echo "0 mins ".$duration." secs";
                                }else if($duration < 60){
                                    echo $duration." secs";
                                }else if($duration == 60){
                                    echo "1 mins 0 secs";
                                }else if($duration > 60){
                                    $mins = $duration/60;
                                    $secs = $duration%60;
                                    echo (int)$mins." mins ".$secs." secs";
                                }
                            ?>
                        </td>
                        <td><?php echo $r['d']['gest_age']; ?></td>
                        <td>
                            <?php 
                                if($r['d']['gest_age'] == "intro"){
                                    echo $attempts = 1;
                                }else{
                                    $decodedflag = json_decode($r['uc']['flag'], TRUE);
                                    echo $attempts = $decodedflag[$r['d']['gest_age']]['attempts']+1;
                                }
                            ?>
                        </td>
                        <td>
                            <?php 
                                $delivery = array('0'=>'Pregnant','1'=>'Successful','2'=>'Unsuccessful');
                                echo $delivery[$r['u']['delivery']];
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
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
                $months_difference--;
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
?>