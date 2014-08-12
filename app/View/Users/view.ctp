<div class="content-box-header">
    <div class="panel-title">User Details</div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered user-details-table">
                    <tbody>
                        <tr><td>Name</td><td><?php echo $user[0]['User']['name']; ?></td></tr>
                        <tr><td>Phone no.</td><td><?php echo $user[0]['User']['phone_no']; ?></td></tr>
                        <tr><td>LMP</td><td><?php if(!empty($user[0]['User']['lmp'])){echo date('d-m-Y', strtotime($user[0]['User']['lmp']));} ?></td></tr>
                        <tr><td>Gestational age</td><td><?php echo $user[0]['User']['enroll_gest_age']; ?></td></tr>
                        <tr><td>Current Gestational age</td>
                            <td>
                                <?php 
                                    // $frequency = array("daily" => "d", "weekly" => "w", "monthly" => "m", "yearly" => "y");
                                    // $diff = array("daily" => "d", "weekly" => "ww", "monthly" => "m", "yearly" => "yyyy");
                                    // $stage = "stage" . $user[0]['User']['stage'];
                                    // if($user[0]['User']['stage']>0) {
                                    //     if(!empty($user[0]['Project']['stage_structure'])){
                                    //         $structure = json_decode($user[0]['Project']['stage_structure'],true);
                                    //         //echo"<pre>";print_r($structure);exit;
                                    //         $callfrequency = $structure[$stage]['callfrequency'];
                                    //     }
                                    //     if ($user[0]['User']['delivery'] == 0) {
                                    //         if (isset($user[0]['User']['lmp'])) {
                                    //             $date1 = strtotime($user[0]['User']['lmp']);
                                    //             $gest_age = 0;
                                    //         } else {
                                    //             $date1 = strtotime($user[0]['User']['registration_date']);
                                    //             $gest_age = $user[0]['User']['enroll_gest_age'];
                                    //         }
                                    //     } elseif ($user[0]['User']['delivery'] == 1) {
                                    //         $gest_age = 0;
                                    //         $date1 = strtotime($user[0]['User']['delivery_date']);
                                    //     }
                                    //     $date2 = time();
                                    //     //echo $callfrequency;exit;
                                    //     $cf = $frequency[$callfrequency];
                                    //     $presentgestage = datediff($diff[$callfrequency], $date1, $date2, true) + $gest_age;
                                    //     echo $stage . "." . $cf . "" . $presentgestage;
                                    // }
                                date_default_timezone_set('Asia/Calcutta');
                                if(!empty($user[0]['User']['lmp'])){
                                $date1 = strtotime($user[0]['User']['lmp']);
                                $gest_age = 0;
                                } else {
                                $date1 = strtotime($user[0]['User']['registration_date']);
                                $gest_age = $user[0]['User']['enroll_gest_age'];
                                }
                                $date2 = time();
                                $presentgestage = datediff('ww', $date1, $date2, true) + $gest_age ;

                                if($user[0]['User']['delivery'] == 0 && $presentgestage > 0 && $presentgestage <= 38){
                                echo $presentgestage;
                                } else if($user[0]['User']['delivery'] == 1 ){
                                $date1 = strtotime($user[0]['User']['delivery_date']);
                                $date2 = time();
                                $weekdiff = datediff('ww',$date1 , $date2, true);
                                $monthdiff = datediff('m',$date1 , $date2, true);
                                $yeardiff = datediff('yyyy',$date1 , $date2, true);

                                if($monthdiff > 12 && $yeardiff <= 5){
                                //check for corresponding monthflag in forth category
                                echo "m".$monthdiff;
                                } else if($monthdiff > 3 && $monthdiff <= 12) {
                                //check for corresponding weekflag in third category
                                echo "w".$weekdiff;

                                } else if($monthdiff <= 3 && $weekdiff > 1){
                                //check for corresponding weekflag in second category
                                echo "w".$weekdiff;
                                } else if($weekdiff <= 1){
                                $daydiff = datediff('d',$date1 , $date2, true) + 1;
                                //check for corresponding weekflag in first category
                                echo "d".$daydiff;
                                }
                                } else{
                                echo $presentgestage;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr><td>Preferred call slot</td><td><?php echo $user[0]['User']['call_slots']; ?></td></tr>
                        <tr><td>Delivery Status</td><td><?php echo $user[0]['User']['delivery']; ?></td></tr>
                        <tr><td>Language</td><td><?php echo $user[0]['User']['language']; ?></td></tr>
                        <tr><td>Registration Date</td><td><?php if(!empty($user[0]['User']['registration_date'])){echo date('d-m-Y', strtotime($user[0]['User']['registration_date']));} ?></td></tr>
                        <tr><td>Delivery Date</td><td><?php if(!empty($user[0]['User']['delivery_date'])){echo date('d-m-Y', strtotime($user[0]['User']['delivery_date']));} ?></td></tr>
                        <tr><td>Entry Date</td><td><?php echo date('d-m-Y H:i:s', strtotime($user[0]['User']['entry_date'])); ?></td></tr>
                        <tr><td>Phone Type</td><td><?php echo $user[0]['User']['phone_type']; ?></td></tr>
                        <tr><td>Phone Code</td><td><?php echo $user[0]['User']['phone_code']; ?></td></tr>
                        <tr><td>Age</td><td><?php echo $user[0]['UserMeta']['age']; ?></td></tr>
                        <tr><td>Education</td><td><?php echo $user[0]['UserMeta']['education']; ?></td></tr>
                        <tr><td>Phone Owner</td><td><?php echo $user[0]['UserMeta']['phone_owner']; ?></td></tr>
                        <tr><td>Alternate Phone</td><td><?php echo $user[0]['UserMeta']['alternate_no']; ?></td></tr>
                        <tr><td>Alternate Phone Owner</td><td><?php echo $user[0]['UserMeta']['alternate_no_owner']; ?></td></tr>
                        <tr><td>Birth Place</td><td><?php echo $user[0]['UserMeta']['birth_place']; ?></td></tr>

                        <!--CUSTOM FIELDS BELOW-->
                        <?php
                        $custom_fields_data = json_decode($user[0]['UserMeta']['custom_fields'], true);
                        if(!empty($custom_fields_data)){
                            foreach ($custom_fields as $key => $value) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $value['label']; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($value['type'] === 'checkbox') {
                                            $cvals = '';
                                            foreach ($custom_fields_data[$key] as $val) {
                                                $cvals = $cvals . $val . ',';
                                            }
                                            $cvals = rtrim($cvals, ",");
                                            echo $cvals;
                                        } else {
                                            echo $custom_fields_data[$key];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }   
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <?php
                        echo $this->Html->link(
                                'Edit Details', array('controller' => 'users', 'action' => 'edit', $user_id, $projectId), array('class' => 'btn btn-primary')
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
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

        switch ($interval) {

            case 'yyyy': // Number of full years

                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters

                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months

                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                //$months_difference--;
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