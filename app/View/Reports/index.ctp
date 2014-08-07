<div class="content-box-header">
    <div class="panel-title">Reports</div>
</div>
<div class="content-box-large box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2 col-md-offset-10 text-right">
                <i class="glyphicon glyphicon-download" id="reportcsv" title="Generate CSV"></i>
            </div>
        </div>
        <div class="row padding-bottom-15">
            <?php echo $this->Form->create('Reports', array('action' => 'index', 'class' => 'form-vertical', 'role' => 'form')); ?>
            <div class="col-md-2 form-group">
                <label>Project</label>
                <?php
                $project[0] = array('value' => 0, 'name' => '');
                foreach ($projects as $p) {
                    $project[] = array('value' => $p['Project']['id'], 'name' => $p['Project']['Name']);
                }
                echo $this->Form->input('project_id', array('type' => 'select', 'options' => $project, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                ?>
            </div>
            <div class="col-md-2 form-group">
                <label>Channel</label>
                <?php
                $channel[0] = array('value' => 0, 'name' => '');
                foreach ($channels as $c) {
                    $channel[] = array('value' => $c['Channel']['id'], 'name' => $c['Channel']['channel_name']);
                }
                echo $this->Form->input('channel_id', array('type' => 'select', 'options' => $channel, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                ?>
            </div>
            <div class="col-md-2 form-group">
                <label>Filter</label>
                <?php
                $call_code = array(
                    "0" => "",
                    "1" => "Number of woman enrolled"
                );
                echo $this->Form->input('filter', array('type' => 'select', 'options' => $call_code, 'label' => false, 'class' => 'form-control', 'div' => FALSE));
                ?>
            </div>
            <div class="col-md-2 form-group">
                <label>From Date</label>
                <input type="text" name ="fromdate" id="ReportsFromdate" class="form-control" placeholder="Text input">
            </div>
            <div class="col-md-2 form-group">
                <label>To Date</label>
                <input type="text" name ="todate" id="ReportsTodate" class="form-control" placeholder="Text input">
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <?php
                $options = array(
                    'label' => 'Generate Report',
                    'div' => FALSE,
                    'class' => 'form-control btn btn-primary',
                    'id' => 'reportbtn'
                );
                echo $this->Form->end($options);
                ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="reportTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Project Name</th>
                            <th>Phone No.</th>
                            <th>Call Slot</th>
                            <th>Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                <div class="loader">
                    <div class="spinner">
                        Loading...
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