<?php

App::uses('AppModel', 'Model');

class Channel extends AppModel {

    public $hasMany = array('User');
    public $validate = array(
        'channel_name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A channel name is required'
            ),
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'A channel with same name already exists.'
            )
        )
    );

    public function getAllChannels() {
        return $this->query("SELECT Channel.id, Channel.channel_name AS Name,Channel.channel_type AS Type, count(User.id) AS count FROM channels Channel LEFT JOIN users User ON Channel.id = User.channel_id AND User.deleted = 0 GROUP BY Channel.id"
        );
    }

    public function getChannelDetails($id) {
        return $this->find('all', array(
                    'fields' => array(
                        '`channel_name` AS `Name`',
                        '`channel_type` AS `Type`'
                    ),
                    'conditions' => array(
                        'id' => $id
                    )
        ));
    }
    
    public function updateChannelDetails($id, $data) {
        $this->id = (int) $id;
        $this->save($data, false);
    }

    public function deleteChannel($id) {
        $this->delete($id);
    }
    
}
