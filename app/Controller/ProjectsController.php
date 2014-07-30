<?php

class ProjectsController extends AppController {

    public $uses = array('Project', 'Channel');
    public function beforeFilter() {
        parent::beforeFilter();
        //$this->Auth->allow('index');
        $this->set('projectsActive', 1);
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Project->create();
//            var_dump($this->Project->save($this->request->data));exit;
            if ($this->Project->save($this->request->data)) {
                $this->Session->setFlash(__('New project added'));
                return $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(
                        __('An error occured. Please, try again.')
                );
            }
        }
    }

    public function edit($id) {
        if ($this->request->is('post')) {
            $this->Project->updateProjectDetails($id, $this->request->data);
            $this->Session->setFlash(
                    __('Project information updated.')
            );
            $this->redirect($this->referer());
//            debug($this->User->getDataSource()->getLog(false, false));
        }

        $project = $this->Project->getProjectDetails($id);
        $this->set('project', $project);
    }

    public function listProjects() {
        $projects = $this->Project->getAllProjects();
//        var_dump($projects);exit;
        $this->set('projects', $projects);
    }

    public function delete($id = null) {
        //redirect on unauthorized route /delete
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        $this->Project->deleteProject($id);
        $this->Session->setFlash(__('Project deleted.'));
        $this->redirect($this->referer());
    }

    public function manageFields($id) {
        $project = $this->Project->getExtraFields($id);
        $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
        $custom_fields_dom = $this->_customFieldsArrayToDom($custom_fields);
        $this->set(array('project' => $project, 'custom_fields' => $custom_fields_dom));
        
        $channels = $this->Channel->getAllChannels();
        $this->set('channels', $channels);
    }

    public function addField($id) {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $project = $this->Project->getExtraFields($id);
            $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
            $custom_fields[time()] = $this->request->data["custom_fields"];
            $updated_fields = json_encode($custom_fields);
            $this->Project->updateCustomFields($id, $updated_fields);

            echo $this->_customFieldsArrayToDom($custom_fields);
        }
    }

    public function editField($id) {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $project = $this->Project->getExtraFields($id);
            $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
            $custom_fields[$this->request->data["index"]] = $this->request->data["custom_fields"];

            $updated_fields = json_encode($custom_fields);
            $this->Project->updateCustomFields($id, $updated_fields);
        }
    }

    public function removeField($id) {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $project = $this->Project->getExtraFields($id);
            $custom_fields = json_decode($project[0]['Project']['custom_fields'], true);
            unset($custom_fields[$this->request->data['index']]);
            $updated_fields = json_encode($custom_fields);
            $this->Project->updateCustomFields($id, $updated_fields);

            echo $this->_customFieldsArrayToDom($custom_fields);
        }
    }
	
	public function createStages($projectid)
	{
		$project_stages = $this->Project->getStages($projectid);
		if($project_stages['Project']['stage_structure'] == null || empty($project_stages['Project']['stage_structure']))
		{
			$project_stages['Project']['stage_structure'] = $this->Project->getStagestemplate();
		}
		//print_r(json_decode($project_stages['Project']['stage_structure'],true));
		$this->set('project_name',$project_stages['Project']['project_name']);
		$this->set('projectid',$projectid);
		$this->set('stage_structure',json_decode($project_stages['Project']['stage_structure'],true));
	}
	
	public function saveallstages()
	{
		$this->autoRender = false;
		$data = $_POST['data'];
		$projectid = $_POST['projectid'];
		$this->Project->saveallstages($projectid,$data);
	}
}
