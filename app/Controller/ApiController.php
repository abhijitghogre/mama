<?php

class ApiController extends AppController {

    public $uses = array('Project');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('create_form');
    }

    public function create_form() {
        $project = $this->Project->find('all', array('recursive' => 0, 'fields' => array('id', 'project_name', 'custom_fields')));
        $proj = array();
        foreach ($project as $p) {
            array_push($proj, $p['Project']);
        }
        $data = '{
            "mandatory": [
                {
                    "type": "text",
                    "label": "Name",
                    "name": "mand-name",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "number",
                    "label": "Age",
                    "name": "mand-age",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "radio",
                    "label": "Education",
                    "name": "mand-education",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "0to5thStandard",
                            "value": "1"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "5thto10thStandard",
                            "value": "2"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "10thPassed",
                            "value": "3"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "12thPassed",
                            "value": "4"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Graduate",
                            "value": "5"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "PostGraduate",
                            "value": "6"
                        }
                    ]
                },
                {
                    "type": "number",
                    "label": "Phone",
                    "name": "mand-phone",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "radio",
                    "label": "Phonetype",
                    "name": "mand-phone-type",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "Mobile(Mumbai)",
                            "value": "1"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Mobile(Others)",
                            "value": "2"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Landline(Mumbai)",
                            "value": "3"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Landline(Others)",
                            "value": "4"
                        }
                    ]
                },
                {
                    "type": "text",
                    "label": "Phonecode",
                    "name": "mand-phone-code",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "radio",
                    "label": "PhoneOwner",
                    "name": "mand-phone-owner",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "Woman",
                            "value": "woman"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Husband",
                            "value": "husband"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "FamilyPhone",
                            "value": "family"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Other",
                            "value": "other"
                        }
                    ]
                },
                {
                    "type": "text",
                    "label": "Other",
                    "name": "mand-phone-owner-other",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "number",
                    "label": "AlternatePhone",
                    "name": "mand-phone-alt",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "radio",
                    "label": "AlternateNo.PhoneOwner",
                    "name": "mand-phone-owner-alt",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "Woman",
                            "value": "woman"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Husband",
                            "value": "husband"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "FamilyPhone",
                            "value": "family"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Other",
                            "value": "other"
                        }
                    ]
                },
                {
                    "type": "text",
                    "label": "Other",
                    "name": "mand-phone-owner-alt-other",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "date",
                    "label": "LMP",
                    "name": "mand-lmp",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "number",
                    "label": "Gestationalageatthetimeofenrollment",
                    "name": "mand-gest-age",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "date",
                    "label": "Registrationdate",
                    "name": "mand-reg-date",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "select",
                    "label": "PreferredCallSlot",
                    "name": "mand-call-slot",
                    "required": "true",
                    "options": [
                        {
                            "selected": "false",
                            "disabled": "false",
                            "label": "9: 00AM-12: 00PM",
                            "value": "1"
                        },
                        {
                            "selected": "false",
                            "disabled": "false",
                            "label": "12: 00PM-3: 00PM",
                            "value": "2"
                        },
                        {
                            "selected": "false",
                            "disabled": "false",
                            "label": "3: 00PM-6: 00PM",
                            "value": "3"
                        },
                        {
                            "selected": "false",
                            "disabled": "false",
                            "label": "6: 00AM-9: 00AM",
                            "value": "4"
                        },
                        {
                            "selected": "false",
                            "disabled": "false",
                            "label": "9: 00AM-12: 00AM",
                            "value": "5"
                        }
                    ]
                },
                {
                    "type": "radio",
                    "label": "Deliverystatus",
                    "name": "mand-delivery-status",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "Pregnant",
                            "value": "0"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Successfuldelivery",
                            "value": "1"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Unsuccessfuldelivery",
                            "value": "2"
                        }
                    ]
                },
                {
                    "type": "date",
                    "label": "Deliverydate",
                    "name": "mand-delivery-date",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                },
                {
                    "type": "radio",
                    "label": "Language",
                    "name": "mand-language",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "Hindi",
                            "value": "2"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Marathi",
                            "value": "3"
                        }
                    ]
                },
                {
                    "type": "radio",
                    "label": "BirthPlace",
                    "name": "mand-birth-place",
                    "required": "true",
                    "options": [
                        {
                            "checked": "true",
                            "disabled": "false",
                            "label": "SameVillage",
                            "value": "same-village"
                        },
                        {
                            "checked": "false",
                            "disabled": "false",
                            "label": "Other",
                            "value": "other"
                        }
                    ]
                },
                {
                    "type": "text",
                    "label": "Other",
                    "name": "mand-birth-place-other",
                    "value": "",
                    "required": "true",
                    "disabled": "false"
                }
            ],
            "custom":""}';
        $result = json_decode($data, true);
        $result['custom'] = $proj;
        $jsondata = json_encode($result);
        echo $jsondata;
        exit;
    }
    
    public function save_data(){
        
    }
}
