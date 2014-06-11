<?php

class StatisticsController extends AppController {

    public $uses = array('DialerLogs', 'UserCallflags', 'Statistic');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function show() {
        $requestData = $this->request->data;
        $stats = array();
        $filter = $requestData['filter'];
        switch ((int) $filter) {
            case 2:
                $stats = $this->Statistic->getFilterStats2($requestData);
                $data = array();
                $result = array();
                $maxAttempts = 0;
                foreach ($stats as $stat) {
                    $obj = json_decode($stat["Statistic"]["stats"], TRUE);
                    foreach ($obj as $k => $o) {
                        if ($k > $maxAttempts) {
                            $maxAttempts = $k;
                        }
                    }
                    $obj['x'] = $stat["Statistic"]["date"];
                    array_push($data, $obj);
                }
                $labels = array();
                $keys = array();
                for ($i = 0; $i <= $maxAttempts; $i++) {
                    array_push($labels, 'Attempt ' . $i);
                    array_push($keys, $i);
                }
                $result['data'] = $data;
                $result['labels'] = $labels;
                $result['keys'] = $keys;
                echo json_encode($result);
                exit;
            case 3:
                $stats = $this->Statistic->getFilterStats3($requestData);
                $data = array();
                $result = array();
                $labels = array();
                foreach ($stats as $stat) {
                    $obj = json_decode($stat["Statistic"]["stats"], TRUE);
                    foreach ($obj as $k => $o) {
                        array_push($labels, $k);
                    }
                    $obj['x'] = $stat["Statistic"]["date"];
                    array_push($data, $obj);
                }

                $result['data'] = $data;
                $result['labels'] = $labels;
                $result['keys'] = $labels;
                echo json_encode($result);
                exit;
            case 4:
                $stats = $this->Statistic->getFilterStats4($requestData);
                $data = array();
                $result = array();
                $labels = array();
                foreach ($stats as $stat) {
                    $obj = json_decode($stat["Statistic"]["stats"], TRUE);
                    foreach ($obj as $k => $o) {
                        array_push($labels, $k);
                    }
                    $obj['x'] = $stat["Statistic"]["date"];
                    array_push($data, $obj);
                }

                $result['data'] = $data;
                $result['labels'] = $labels;
                $result['keys'] = $labels;
                echo json_encode($result);
                exit;
        }

        exit;
    }

    public function calculate($projectId) {

        /**
         * Filter 2 -  Number of calls actually made, and which attempt
         */
        //fetch all user flags for the project
        $uniqueCombinations = $this->DialerLogs->getUsersByProjectId($projectId);
        $attemptsArray = array();

        foreach ($uniqueCombinations as $uc) {
            $user_id = $uc["DialerLogs"]["user_id"];
            $flags = $this->UserCallflags->getUserFlagsByUserId($user_id);
            $flagsArray = json_decode($flags[0]["UserCallflags"]["flag"], true);
            $attempts = $flagsArray[$uc["DialerLogs"]["gest_age"]]["attempts"];

            if (isset($attemptsArray[$attempts])) {
                $attemptsArray[$attempts] ++;
            } else {
                $attemptsArray[$attempts] = 0;
            }
        }
        $saveData = array();
        $saveData["Statistic"]["project_id"] = $projectId;
        $saveData["Statistic"]["date"] = date("Y-m-d", time());
        $saveData["Statistic"]["filter"] = 2;
        $saveData["Statistic"]["stats"] = json_encode($attemptsArray, JSON_FORCE_OBJECT);

        if (empty($this->Statistic->find('first', array("conditions" => array('filter' => 2, 'project_id' => $projectId, 'date' => date("Y-m-d", time())))))) {
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 2 ends
         */
        /**
         * Filter 3 - Number of unsuccessful calls with reason
         */
        $usersNotCalled = $this->DialerLogs->getFailedCallUsers($projectId);
        $reasonsArray = array();
        foreach ($usersNotCalled as $value) {
            $reasonsArray[$value["DialerLogs"]["message"]] = $value[0]["count"];
        }
        $saveData = array();
        $saveData["Statistic"]["project_id"] = $projectId;
        $saveData["Statistic"]["date"] = date("Y-m-d", time());
        $saveData["Statistic"]["filter"] = 3;
        $saveData["Statistic"]["stats"] = json_encode($reasonsArray, JSON_FORCE_OBJECT);

        if (empty($this->Statistic->find('first', array("conditions" => array('filter' => 3, 'project_id' => $projectId, 'date' => date("Y-m-d", time())))))) {
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 3 ends
         */
        /**
         * Filter 4 - Number of women listening the message(20%,50%,100%)
         */
        $users = $this->DialerLogs->getSuccessfulCallUsers($projectId);
        $durationArray = array('20' => 0, '50' => 0, '100' => 0);
        foreach ($users as $value) {
            $totalLength = 50;
//            echo 'value = '.$value["DialerLogs"]["duration"];
            $percent = ((int) $value["DialerLogs"]["duration"] / (int) $totalLength) * 100;
//            var_dump($percent);exit;
            if ($percent <= 20) {
                $durationArray['20'] ++;
            } elseif ($percent > 20 && $percent <= 50) {
                $durationArray['50'] ++;
            } else {
                $durationArray['100'] ++;
            }
        }
        $saveData = array();
        $saveData["Statistic"]["project_id"] = $projectId;
        $saveData["Statistic"]["date"] = date("Y-m-d", time());
        $saveData["Statistic"]["filter"] = 4;
        $saveData["Statistic"]["stats"] = json_encode($durationArray, JSON_FORCE_OBJECT);

        if (empty($this->Statistic->find('first', array("conditions" => array('filter' => 4, 'project_id' => $projectId, 'date' => date("Y-m-d", time())))))) {
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 4 ends
         */
        /**
         * Filter 5 - Number of missed calls and call backs
         */
        $users = $this->DialerLogs->getMissedCallUsers($projectId);
        $reasonsArray = array('0' => 0, '1' => 0);
        foreach ($users as $value) {
            $reasonsArray[$value["DialerLogs"]["reason"]] = $value[0]["count"];
        }
        $saveData = array();
        $saveData["Statistic"]["project_id"] = $projectId;
        $saveData["Statistic"]["date"] = date("Y-m-d", time());
        $saveData["Statistic"]["filter"] = 5;
        $saveData["Statistic"]["stats"] = json_encode($reasonsArray, JSON_FORCE_OBJECT);

        if (empty($this->Statistic->find('first', array("conditions" => array('filter' => 5, 'project_id' => $projectId, 'date' => date("Y-m-d", time())))))) {
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 5 ends
         */
        exit;
    }

}
