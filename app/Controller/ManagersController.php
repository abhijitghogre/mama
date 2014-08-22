<?php

class ManagersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout');
        $this->set('volunteersActive', 1);
    }

    public function listVolunteers() {
        if ($this->Auth->user('role') != 'admin' && $this->Auth->user('role') != 'superadmin') {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        $volunteers = $this->Manager->getAllVolunteers();
        $this->set('volunteers', $volunteers);
    }

    public function listAdmins() {
        if ($this->Auth->user('role') != 'superadmin') {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        $admins = $this->Manager->getAllAdmins();
        $this->set('admins', $admins);
    }

    public function view() {
        $manager = $this->Manager->getManagerDetails($this->Auth->user('id'));
        $this->set('manager', $manager);
    }

    public function add() {

        //allow only admin and super admin to add managers
        if ($this->Auth->user('role') == 'admin' || $this->Auth->user('role') == 'superadmin') {

            if ($this->request->is('post')) {
                $savedata=array();
                $this->Manager->create();
                $savedata['Manager']['fname']=$this->request->data['Manager']['fname'];
                $savedata['Manager']['lname']=$this->request->data['Manager']['lname'];
                $savedata['Manager']['username']=$this->request->data['Manager']['fname']."_".$this->request->data['Manager']['lname'];
                $savedata['Manager']['password']='test';
                $savedata['Manager']['role']='volunteer';
                print_r($savedata);
                $this->Manager->saveAll($savedata);
                return $this->redirect(array('controller' => 'managers', 'action' => 'add'));
            }
        } else {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }
    }

    public function edit($id = null) {
        //redirect on unauthorized route /edit
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        //prevent volunteer from editing information of other volunteers
        if ($this->Auth->user('role') == 'volunteer') {
            if ($this->Auth->user('id') != $id) {
                return $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }
        }

        //prevent admin from editing information of other admins and superadmins
        if ($this->Auth->user('role') == 'admin' && $this->Auth->user('id') != $id) {
            $role = $this->Manager->getRoleById($id);
//            var_dump($role);exit;
            if ($role[0]['Manager']['role'] != 'volunteer') {
                return $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }
        }

        if ($this->request->is('post')) {
            $this->Manager->updateManagerDetails($id, $this->request->data);
            $this->Session->setFlash(
                    __('Manager information updated.')
            );
            return $this->redirect(array('controller' => 'managers', 'action' => 'edit', $id));
//            debug($this->User->getDataSource()->getLog(false, false));
        }

        $manager = $this->Manager->getManagerDetails($id);
        $this->set('manager', $manager);
    }

    public function delete($id = null) {
        //redirect on unauthorized route /delete
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        //allow delete for superadmins only
        if ($this->Auth->user('role') == 'superadmin') {
            $result = $this->Manager->deleteManager($id);
            $this->Session->setFlash(__('Manager deleted.'));
            $this->redirect($this->referer());
        } else {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }
    }

    public function promoteAdmin($id = null) {
        //redirect on unauthorized route /promote
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        //allow promote for superadmins only
        if ($this->Auth->user('role') == 'superadmin') {
            $this->Manager->promoteAdmin($id);
            $this->Session->setFlash(__('Admin promoted.'));
            $this->redirect($this->referer());
        } else {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }
    }

    public function login() {
        $this->layout = 'security';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

}
