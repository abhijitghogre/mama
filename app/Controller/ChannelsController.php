<?php

class ChannelsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('channelsActive', 1);
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Channel->create();
//            var_dump($this->Project->save($this->request->data));exit;
            if ($this->Channel->save($this->request->data)) {
                $this->Session->setFlash(__('New channel added'));
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
            $this->Channel->updateChannelDetails($id, $this->request->data);
            $this->Session->setFlash(
                    __('Channel information updated.')
            );
            $this->redirect($this->referer());
//            debug($this->User->getDataSource()->getLog(false, false));
        }

        $channel = $this->Channel->getChannelDetails($id);
        $this->set('channel', $channel);
    }

    public function listChannels() {
        $channels = $this->Channel->getAllChannels();
        $this->set('channels', $channels);
    }

    public function delete($id = null) {
        //redirect on unauthorized route /delete
        if ($id == null) {
            return $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        $this->Channel->deleteChannel($id);
        $this->Session->setFlash(__('Channel deleted.'));
        $this->redirect($this->referer());
    }
}
