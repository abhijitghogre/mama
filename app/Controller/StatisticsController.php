<?php

class StatisticsController extends AppController {

    public $uses = array('DialerLogs', 'UserCallflags', 'Statistic');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('calculate');
    }

    public function calculate($projectId) {

        $yesterday = date("Y-m-d", strtotime("-1 days"));

        /**
         * Filter 1 -  Number of calls to be made
         */
//fetch all user flags for the project
        $uniqueCombinations = $this->DialerLogs->getToBeCalledUsers($projectId);
//        var_dump($uniqueCombinations);
        $saveData = array();
        $saveData["Statistic"]["project_id"] = $projectId;
        $saveData["Statistic"]["date"] = $yesterday;
        $saveData["Statistic"]["filter"] = 1;
        $saveData["Statistic"]["stats"] = isset($uniqueCombinations[0][0]['count']) ? $uniqueCombinations[0][0]['count'] : "0";

        $returnedData = $this->_pastStatsMain($yesterday, $projectId, 1);
        $saveData["Statistic"]["week"] = $returnedData['week'];
        $saveData["Statistic"]["month"] = $returnedData['month'];
        $saveData["Statistic"]["year"] = $returnedData['year'];

        $existsFlag = empty($this->Statistic->find('first', array("conditions" => array('filter' => 2, 'project_id' => $projectId, 'date' => $yesterday))));

        if ($existsFlag) {
            $this->Statistic->create();
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 1 ends
         */
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
        $saveData["Statistic"]["date"] = $yesterday;
        $saveData["Statistic"]["filter"] = 2;
        $saveData["Statistic"]["stats"] = json_encode($attemptsArray, JSON_FORCE_OBJECT);

        $returnedData = $this->_pastStatsMain($yesterday, $projectId, 2);
        $saveData["Statistic"]["week"] = $returnedData['week'];
        $saveData["Statistic"]["month"] = $returnedData['month'];
        $saveData["Statistic"]["year"] = $returnedData['year'];

        $existsFlag = empty($this->Statistic->find('first', array("conditions" => array('filter' => 2, 'project_id' => $projectId, 'date' => $yesterday))));

        if ($existsFlag) {
            $this->Statistic->create();
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
        $saveData["Statistic"]["date"] = $yesterday;
        $saveData["Statistic"]["filter"] = 3;
        $saveData["Statistic"]["stats"] = json_encode($reasonsArray, JSON_FORCE_OBJECT);

        $returnedData = $this->_pastStatsMain($yesterday, $projectId, 3);
        $saveData["Statistic"]["week"] = $returnedData['week'];
        $saveData["Statistic"]["month"] = $returnedData['month'];
        $saveData["Statistic"]["year"] = $returnedData['year'];

        $existsFlag = empty($this->Statistic->find('first', array("conditions" => array('filter' => 3, 'project_id' => $projectId, 'date' => $yesterday))));
        if ($existsFlag) {
            $this->Statistic->create();
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
            $percent = ((int) $value["DialerLogs"]["duration"] / (int) $totalLength) * 100;
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
        $saveData["Statistic"]["date"] = $yesterday;
        $saveData["Statistic"]["filter"] = 4;
        $saveData["Statistic"]["stats"] = json_encode($durationArray, JSON_FORCE_OBJECT);

        $returnedData = $this->_pastStatsMain($yesterday, $projectId, 4);
        $saveData["Statistic"]["week"] = $returnedData['week'];
        $saveData["Statistic"]["month"] = $returnedData['month'];
        $saveData["Statistic"]["year"] = $returnedData['year'];

        $existsFlag = empty($this->Statistic->find('first', array("conditions" => array('filter' => 4, 'project_id' => $projectId, 'date' => $yesterday))));
        if ($existsFlag) {
            $this->Statistic->create();
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
        $saveData["Statistic"]["date"] = $yesterday;
        $saveData["Statistic"]["filter"] = 5;
        $saveData["Statistic"]["stats"] = json_encode($reasonsArray, JSON_FORCE_OBJECT);

        $returnedData = $this->_pastStatsMain($yesterday, $projectId, 5);
        $saveData["Statistic"]["week"] = $returnedData['week'];
        $saveData["Statistic"]["month"] = $returnedData['month'];
        $saveData["Statistic"]["year"] = $returnedData['year'];

        $existsFlag = empty($this->Statistic->find('first', array("conditions" => array('filter' => 5, 'project_id' => $projectId, 'date' => $yesterday))));
        if ($existsFlag) {
            $this->Statistic->create();
            $this->Statistic->save($saveData);
        }
        /**
         * Filter 5 ends
         */
        exit;
    }

    public function show() {
        $requestData = $this->request->data;
        $filter = $requestData['filter'];
        $type = $requestData['statstype'];
        switch ((int) $filter) {
            case 1: // filter 1 - Number of calls to be made
                switch ((int) $type) {
                    case 1: //day
                        echo $this->_getFilter1StatsByType($requestData, 'stats');
                        break;
                    case 2: //week
                        echo $this->_getFilter1StatsByType($requestData, 'week');
                        break;
                    case 3: //month
                        echo $this->_getFilter1StatsByType($requestData, 'month');
                        break;
                    case 4: //year
                        echo $this->_getFilter1StatsByType($requestData, 'year');
                        break;
                }

                exit;
            case 2: //filter 2
                switch ((int) $type) {
                    case 1: //day
                        echo $this->_getFilter2StatsByType($requestData, 'stats');
                        break;
                    case 2: //week
                        echo $this->_getFilter2StatsByType($requestData, 'week');
                        break;
                    case 3: //month
                        echo $this->_getFilter2StatsByType($requestData, 'month');
                        break;
                    case 4: //year
                        echo $this->_getFilter2StatsByType($requestData, 'year');
                        break;
                }
                exit;
            case 3: //filter 3 - reasons for not being called
                switch ((int) $type) {
                    case 1: //day
                        echo $this->_getFilter3StatsByType($requestData, 'stats');
                        break;
                    case 2: //week
                        echo $this->_getFilter3StatsByType($requestData, 'week');
                        break;
                    case 3: //month
                        echo $this->_getFilter3StatsByType($requestData, 'month');
                        break;
                    case 4: //year
                        echo $this->_getFilter3StatsByType($requestData, 'year');
                        break;
                }
                exit;
            case 4:
                switch ((int) $type) {
                    case 1: //day
                        echo $this->_getFilter4StatsByType($requestData, 'stats');
                        break;
                    case 2: //week
                        echo $this->_getFilter4StatsByType($requestData, 'week');
                        break;
                    case 3: //month
                        echo $this->_getFilter4StatsByType($requestData, 'month');
                        break;
                    case 4: //year
                        echo $this->_getFilter4StatsByType($requestData, 'year');
                        break;
                }
                exit;
            case 5:
                switch ((int) $type) {
                    case 1: //day
                        echo $this->_getFilter5StatsByType($requestData, 'stats');
                        break;
                    case 2: //week
                        echo $this->_getFilter5StatsByType($requestData, 'week');
                        break;
                    case 3: //month
                        echo $this->_getFilter5StatsByType($requestData, 'month');
                        break;
                    case 4: //year
                        echo $this->_getFilter5StatsByType($requestData, 'year');
                        break;
                }
                exit;
        }

        exit;
    }

    public function _getFilter5StatsByType($requestData, $type) {
        $stats = $this->Statistic->getStoredFilterStats($requestData, $type);
        foreach ($stats as $key => $val) {
            if ($val["Statistic"]['stats'] === NULL || $val["Statistic"]['stats'] === "") {
                unset($stats[$key]);
            }
        }
        $data = array();
        $result = array();
        $keys = array();
        foreach ($stats as $stat) {
            $obj = json_decode($stat["Statistic"]["stats"], TRUE);
            foreach ($obj as $k => $o) {
                if (!in_array($k, $keys)) {
                    array_push($keys, $k);
                }
            }
            if ($type === 'stats') {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            } else {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"] . '- 1 ' . $type . ' + 1 day')) . ' to ' . date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            }
            array_push($data, $obj);
        }

        $result['data'] = $data;
        $result['labels'] = $keys;
        $result['keys'] = $keys;
        return json_encode($result);
    }

    public function _getFilter4StatsByType($requestData, $type) {
        $stats = $this->Statistic->getStoredFilterStats($requestData, $type);
        foreach ($stats as $key => $val) {
            if ($val["Statistic"]['stats'] === NULL || $val["Statistic"]['stats'] === "") {
                unset($stats[$key]);
            }
        }
        $data = array();
        $result = array();
        $labels = array();
        foreach ($stats as $stat) {
            $obj = json_decode($stat["Statistic"]["stats"], TRUE);
            foreach ($obj as $k => $o) {
                if (!in_array($k, $labels)) {
                    array_push($labels, $k);
                }
            }
            if ($type === 'stats') {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            } else {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"] . '- 1 ' . $type . ' + 1 day')) . ' to ' . date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            }
            array_push($data, $obj);
        }

        $result['data'] = $data;
        $result['labels'] = $labels;
        $result['keys'] = $labels;
        return json_encode($result);
    }

    public function _getFilter3StatsByType($requestData, $type) {
        $stats = $this->Statistic->getStoredFilterStats($requestData, $type);
        foreach ($stats as $key => $val) {
            if ($val["Statistic"]['stats'] === NULL || $val["Statistic"]['stats'] === "") {
                unset($stats[$key]);
            }
        }
        $data = array();
        $result = array();
        $labels = array();
        foreach ($stats as $stat) {

            $obj = json_decode($stat["Statistic"]["stats"], TRUE);
            foreach ($obj as $k => $o) {
                if (!in_array($k, $labels)) {
                    array_push($labels, $k);
                }
            }
            if ($type === 'stats') {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            } else {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"] . '- 1 ' . $type . ' + 1 day')) . ' to ' . date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            }
            array_push($data, $obj);
        }

        $result['data'] = $data;
        $result['labels'] = $labels;
        $result['keys'] = $labels;
        return json_encode($result);
    }

    public function _getFilter2StatsByType($requestData, $type) {
        $stats = $this->Statistic->getStoredFilterStats($requestData, $type);
        foreach ($stats as $key => $val) {
            if ($val["Statistic"]['stats'] === NULL || $val["Statistic"]['stats'] === "") {
                unset($stats[$key]);
            }
        }

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
            if ($type === 'stats') {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            } else {
                $obj['x'] = date("d-m-Y", strtotime($stat["Statistic"]["date"] . '- 1 ' . $type . ' + 1 day')) . ' to ' . date("d-m-Y", strtotime($stat["Statistic"]["date"]));
            }

            array_push($data, $obj);
        }
        $labels = array();
        $keys = array();
        for ($i = 0; $i <= $maxAttempts; $i++) {
            if (!in_array('Attempt ' . $i, $labels)) {
                array_push($labels, 'Attempt ' . $i);
            }
            if (!in_array($i, $keys)) {
                array_push($keys, $i);
            }
        }

        $result['data'] = $data;
        $result['labels'] = $labels;
        $result['keys'] = $keys;

        return json_encode($result);
    }

    public function _getFilter1StatsByType($requestData, $type) {
        $stats = $this->Statistic->getStoredFilterStats($requestData, $type);
        foreach ($stats as $key => $val) {
            if ($val["Statistic"]['stats'] === NULL || $val["Statistic"]['stats'] === "") {
                unset($stats[$key]);
            }
        }
        $result = array();
        foreach ($stats as $stat) {
            $obj = array();
            $obj['x'] = $stat["Statistic"]['date'];
            $obj['count'] = $stat["Statistic"]['stats'];
            array_push($result, $obj);
        }
//        var_dump($result);exit;
        return json_encode($result);
    }

    public function _pastStatsMain($yesterday, $projectId, $filter) {
//------------------------------------------------------------------------------------//
        $returnArray = array('week' => null, 'month' => null, 'year' => null);
// if yesterday is a sunday, add stats for last week and store in weeks column
        $day = date('l', strtotime($yesterday));
        if ($day === "Sunday") {
//go back a week from yesterday
            $dateLastWeek = date('Y-m-d', strtotime($yesterday . ' - 1 week + 1 day'));
            $weekStats = $this->Statistic->getStatsByDateRange($dateLastWeek, $yesterday, $filter, $projectId);
            $returnArray["week"] = $this->_calculatePastStats($weekStats, $filter);
        }

// if yesterday is 1st, add stats for last month and store in months column
        $date = date('d', strtotime($yesterday));
        if ($date === "01") {
//go back a month from yesterday
            $dateLastMonth = date('Y-m-d', strtotime($yesterday . ' - 1 month + 1 day'));
            $monthStats = $this->Statistic->getStatsByDateRange($dateLastMonth, $yesterday, $filter, $projectId);
            $returnArray["month"] = $this->_calculatePastStats($monthStats, $filter);
        }

//if yesterday is january 1st calculate yearly stats
        $date = date('d', strtotime($yesterday));
        $month = date('m', strtotime($yesterday));
        if ($month === '01' && $date === "01") {
//go back a year from yesterday
            $dateLastYear = date('Y-m-d', strtotime($yesterday . ' - 1 month + 1 day'));
            $yearStats = $this->Statistic->getStatsByDateRange($dateLastYear, $yesterday, $filter, $projectId);
            $returnArray["year"] = $this->_calculatePastStats($yearStats, $filter);
        }
//------------------------------------------------------------------------------------------------------------------//
        return $returnArray;
    }

    public function _calculatePastStats($statsParam, $filter) {
        $temp = array_column($statsParam, 'Statistic');
        $temp = array_column($temp, 'stats');
        if ((int) $filter === 1) {
            return array_sum($temp);
        }
        $result = array();
//        var_dump($temp);exit;
        foreach ($temp as $tkey => $tval) {
            $temp[$tkey] = json_decode($tval, true);
        }
        foreach ($temp as $tval) {
            foreach ($tval as $ttkey => $ttval) {
                if (!array_key_exists($ttkey, $result)) {
                    $result[$ttkey] = 0;
                }
                $result[$ttkey] = $result[$ttkey] + $ttval;
            }
        }

        return json_encode($result, JSON_FORCE_OBJECT);
    }

}
