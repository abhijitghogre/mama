<?php

class HomeController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index');
    }

    public function index() {
        //return $this->redirect(array('controller' => 'projects', 'action' => 'listProjects'));
        $projects = $this->Project->getAllProjects();
        $this->set('projects', $projects);
    }

}
