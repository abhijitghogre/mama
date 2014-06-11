<?php

App::uses('AppModel', 'Model');

class Statistic extends AppModel {

    /**
     * Filter 2 -  Number of calls actually made, and which attempt
     */
    public function getFilterStats2($data) {
        return $this->query('SELECT Statistic.stats,Statistic.date FROM statistics Statistic WHERE Statistic.date BETWEEN "' . date("Y-m-d", strtotime($data['statsDateFrom'])) . '" AND "' . date("Y-m-d", strtotime($data['statsDateTo'])) . '" AND Statistic.project_id = ' . $data['projectId'] . ' AND Statistic.filter = ' . $data['filter'] . '');
    }

    /**
     * Filter 3 - Number of unsuccessful calls with reason
     */
    public function getFilterStats3($data) {
        return $this->query('SELECT Statistic.stats,Statistic.date FROM statistics Statistic WHERE Statistic.date BETWEEN "' . date("Y-m-d", strtotime($data['statsDateFrom'])) . '" AND "' . date("Y-m-d", strtotime($data['statsDateTo'])) . '" AND Statistic.project_id = ' . $data['projectId'] . ' AND Statistic.filter = ' . $data['filter'] . '');
    }
    
    /**
     * Filter 4 - Number of women listening the message(20%,50%,100%)
     */
    public function getFilterStats4($data) {
        return $this->query('SELECT Statistic.stats,Statistic.date FROM statistics Statistic WHERE Statistic.date BETWEEN "' . date("Y-m-d", strtotime($data['statsDateFrom'])) . '" AND "' . date("Y-m-d", strtotime($data['statsDateTo'])) . '" AND Statistic.project_id = ' . $data['projectId'] . ' AND Statistic.filter = ' . $data['filter'] . '');
    }

}
