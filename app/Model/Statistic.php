<?php

App::uses('AppModel', 'Model');

class Statistic extends AppModel {

    public function getStoredFilterStats($data, $type = 'stats') {
        return $this->query('SELECT Statistic.' . $type . ' AS stats,Statistic.date FROM statistics Statistic WHERE Statistic.date BETWEEN "' . date("Y-m-d", strtotime($data['statsDateFrom'])) . '" AND "' . date("Y-m-d", strtotime($data['statsDateTo'])) . '" AND Statistic.project_id = ' . $data['projectId'] . ' AND Statistic.filter = ' . $data['filter'] . '');
    }

    public function getStatsByDateRange($from, $to, $filter, $projectId) {
        return $this->query('SELECT Statistic.stats,Statistic.date FROM statistics Statistic WHERE Statistic.date BETWEEN "' . date("Y-m-d", strtotime($from)) . '" AND "' . date("Y-m-d", strtotime($to)) . '" AND Statistic.project_id = ' . $projectId . ' AND Statistic.filter = ' . $filter . '');
    }

}
