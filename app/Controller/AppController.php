<?php

/*
  Custom fields sample:

  {
  "1401272353": {
  "type": "text",
  "label": "field label",
  "name": "somename",
  "value": "default_value",
  "required": true,
  "disabled": true,
  "rows": 30,
  "cols": 30,
  "checkboxes": [
  {
  "checked": true,
  "disabled": true,
  "label": "check label1"
  },
  {
  "checked": false,
  "disabled": false,
  "label": "check label2"
  }
  ],
  "radios": [
  {
  "checked": true,
  "disabled": true,
  "label": "radio label1"
  },
  {
  "checked": false,
  "disabled": false,
  "label": "radio label2"
  }
  ],
  "options": [
  {
  "value": "optionvalue1",
  "selected": true,
  "disabled": false
  },
  {
  "value": "optionvalue1",
  "selected": true,
  "disabled": false
  }
  ]
  }
  }


 */
/* ------------------------------------------------------------------------------------------------------ */
App::uses('Controller', 'Controller');

class AppController extends Controller {

    public $uses = array('Project');
    public $components = array(
        'Session',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'managers',
                'action' => 'login'
            ),
            'loginRedirect' => array('controller' => 'home', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'home', 'action' => 'index'),
        )
    );

    public function beforeFilter() {
        $this->Auth->authenticate = array(
            'Basic' => array('userModel' => 'Manager'),
            'Form' => array('userModel' => 'Manager')
        );
        if ($this->Auth->user('role') === 'admin') {
            $this->set("role", 'admin');
        } elseif ($this->Auth->user('role') === 'user') {
            $this->set("role", 'user');
        } elseif ($this->Auth->user('role') === 'superadmin') {
            $this->set("role", 'superadmin');
        } else {
            $this->set("role", '');
        }

        $projects = $this->Project->getAllProjects();
        $this->set('projects', $projects);
    }

    /**
     * CUSTOM USER FUNCTIONS 
     */
    protected function _customFieldsArrayToDom($array, $showRemoveButton = true) {
        $dom = '<div class="row">
    <div class="col-sm-8">';
        if (!isset($array)) {
            $array = array();
        }
        foreach ($array as $key => $field) {
            switch ($field['type']) {
                case "text":
                    $dom = $dom . '<div class="form-group formField" data-index="' . $key . '" data-type="' . $field['type'] . '" data-name="' . $field['name'] . '">
                                        <div class="hide json-data">' . json_encode($field) . '</div>
                                        <label for="' . $field['name'] . '" class="col-sm-2 control-label">' . $field['label'] . ($field['required'] === 'true' ? '*' : '') . ':</label>
                                        <div class="col-sm-10">
                                            <input value="' . $field['value'] . '" name="' . $field['name'] . '" id="' . $field['name'] . '" class="form-control custom-field-input" type="text" ' . ($field['required'] === 'true' ? 'required' : '') . '>
                                        </div>
                                        ' . ($showRemoveButton ? '<div class="edit-button" data-type="customField"><i class="glyphicon glyphicon-edit"></i></div>
                                            <div class="close-button" data-type="customField"><i class="glyphicon glyphicon-remove"></i></div>
                                            ' : "") . '
                                                
                                    </div>';
                    break;
                case "radio":
                    $dom = $dom . '<div class="form-group formField" data-index="' . $key . '" data-type="' . $field['type'] . '" data-name="' . $field['name'] . '">
                                        <div class="hide json-data">' . json_encode($field) . '</div>
                                        <label class="col-md-2 control-label">' . $field['label'] . ($field['required'] === 'true' ? '*' : '') . ':</label>
                                        <div class="col-md-10">';
                    foreach ($field['radios'] as $k => $radio) {
                        $dom = $dom . '<div class="radio">
                                                <label>
                                                    <input class="custom-field-input" value="' . $radio['label'] . '" type="radio" ' . ($radio['disabled'] === 'true' ? 'disabled="disabled"' : '') . ' ' . ($radio['checked'] === 'true' ? 'checked="checked"' : '') . ' name="' . $field['name'] . '">
                                                    ' . $radio['label'] . '</label>
                                            </div>';
                    }
                    $dom = $dom . '</div>
                                        ' . ($showRemoveButton ? '<div class="edit-button" data-type="customField"><i class="glyphicon glyphicon-edit"></i></div>
                                            <div class="close-button" data-type="customField"><i class="glyphicon glyphicon-remove"></i></div>
                                            ' : "") . '
                                </div>';

                    break;
                case "checkbox":
                    $dom = $dom . '<div class="form-group formField" data-index="' . $key . '" data-type="' . $field['type'] . '" data-name="' . $field['name'] . '">
                                        <div class="hide json-data">' . json_encode($field) . '</div>
                                        <label class="col-md-2 control-label">' . $field['label'] . ($field['required'] === 'true' ? '*' : '') . ':</label>
                                        <div class="col-md-10">';
                    foreach ($field['checkboxes'] as $k => $checkbox) {
                        $dom = $dom . '   <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" ' . ($checkbox['disabled'] === 'true' ? 'disabled="disabled"' : '') . ' ' . ($checkbox['checked'] === 'true' ? 'checked="checked"' : '') . '" value="' . $checkbox['label'] . '" class="custom-field-input" name="' . $field['name'] . '">
                                                    ' . $checkbox['label'] . ' </label>
                                            </div>';
                    }
                    $dom = $dom . '</div>
                                    ' . ($showRemoveButton ? '<div class="edit-button" data-type="customField"><i class="glyphicon glyphicon-edit"></i></div>
                                            <div class="close-button" data-type="customField"><i class="glyphicon glyphicon-remove"></i></div>
                                            ' : "") . '
                                </div>';
                    break;
                case "select":
                    $dom = $dom . '<div class="form-group formField" data-index="' . $key . '" data-type="' . $field['type'] . '" data-name="' . $field['name'] . '">
                                    <div class="hide json-data">' . json_encode($field) . '</div>
                                    <label class="col-md-2 control-label">' . $field['label'] . ($field['required'] === 'true' ? '*' : '') . '</label>
                                    <div class="col-md-10">
                                       <select class="form-control custom-field-input" type="select" name="' . $field['name'] . '">';
                    foreach ($field['options'] as $option) {
                        $dom = $dom . '<option value="' . $option['value'] . '" ' . ($option['disabled'] === 'true' ? "disabled" : "") . ' ' . ($option['selected'] === 'true' ? "selected" : "") . '>' . $option['value'] . '</option>';
                    }
                    $dom = $dom . '</select> 
                                    </div>
                                    ' . ($showRemoveButton ? '<div class="edit-button" data-type="customField"><i class="glyphicon glyphicon-edit"></i></div>
                                            <div class="close-button" data-type="customField"><i class="glyphicon glyphicon-remove"></i></div>
                                            ' : "") . '
                                </div>';
                    break;
                case "textarea":

                    $dom = $dom . ' <div class="form-group formField" data-index="' . $key . '" data-type="' . $field['type'] . '" data-name="' . $field['name'] . '">
                                        <div class="hide json-data">' . json_encode($field) . '</div>
                                        <label class="col-sm-2 control-label">' . $field['label'] . ($field['required'] === 'true' ? '*' : '') . ':</label>
                                        <div class="col-sm-10">
                                            <textarea type="textarea" class="form-control custom-field-input" name="' . $field['name'] . '" rows="' . $field['rows'] . '" cols="' . $field['cols'] . '" ' . ($field['disabled'] === 'true' ? 'disabled="disabled"' : '') . '>' . $field['value'] . '</textarea>
                                        </div>
                                        ' . ($showRemoveButton ? '<div class="edit-button" data-type="customField"><i class="glyphicon glyphicon-edit"></i></div>
                                            <div class="close-button" data-type="customField"><i class="glyphicon glyphicon-remove"></i></div>
                                            ' : "") . '
                                    </div>';
                    break;
            }
        }

        return $dom.'</div></div>';
    }

}
