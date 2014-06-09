<input type="hidden" value="<?php echo $project[0]['Project']['id']; ?>" class="project_id">
<div class="row">
    <div class="col-md-12">
        <div class="content-box-header">
            <div class="panel-title">Manage Fields</div>
        </div>
        <div class="content-box-large padding-10 box-with-header">
            <div class="panel-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li><a href="#tab1" data-toggle="tab">Mandatory Fields</a></li>
                    <li class="active"><a href="#tab2" data-toggle="tab">Custom Fields</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane" id="tab1">
                        <form class="form-horizontal" role="form">
                            <div id="mand-field-cont">
                                <?php echo $this->element('mandatory_fields'); ?>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane active" id="tab2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="add-button custom-field-add btn btn-primary btn-sm" data-toggle="modal" data-target="#custom-field-modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Fields</div>
                            </div>
                        </div>
                        <form class="form-horizontal" role="form">
                            <div class="custom-fields-cont">
                                <?php echo $custom_fields; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="custom-field-modal" tabindex="-1" role="dialog" aria-labelledby="CustomFields" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="new-custom-fields" role="form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title newFieldFormTitle" id="myModalLabel">Add new custom field</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group formField">
                        <label class="col-md-2 control-label">Type of field*:</label>
                        <div class="col-md-10">
                            <select class="form-control field_type">
                                <option value="text">Text</option>
                                <option value="radio">Radio Buttons Group</option>
                                <option value="checkbox">Check-boxes Group</option>
                                <option value="select">Drop-down</option>
                                <option value="textarea">Description</option>
                            </select> 
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label class="col-sm-2 control-label">Name*:</label>
                        <div class="col-sm-10">
                            <input class="field-name form-control" type="text" required>
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label class="col-sm-2 control-label">Default value:</label>
                        <div class="col-sm-10">
                            <input class="field-value form-control" type="text">
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-textarea show-for-text show-for-radio show-for-checkbox show-for-select">
                        <label class="col-sm-2 control-label">Required?</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="field-required"> 
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-textarea show-for-text">
                        <label class="col-sm-2 control-label">Disabled?</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="field-disabled">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-textare">
                        <label class="col-sm-2 control-label">Height:</label>
                        <div class="col-sm-10">
                            <input class="field-rows form-control" type="number">
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-textare">
                        <label class="col-sm-2 control-label">Width:</label>
                        <div class="col-sm-10">
                            <input class="field-cols form-control" type="number">
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-checkbox">
                        <label class="col-md-2 control-label">Check-boxes:</label>
                        <div class="col-md-10 field-checkboxes-cont">
                            <div class="row field-checkboxes">
                                <div class="col-md-6">
                                    <input type="text" class="field-checkbox-label form-control" placeholder="Name" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-checkbox-checked">
                                        Checked? </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-checkbox-disabled">
                                        Disabled? </label>
                                </div>
                                <div class="close-button" data-type="checkbox"><i class="glyphicon glyphicon-remove"></i></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="add-button custom-field-add btn btn-primary btn-sm" data-type="checkbox"><i class="glyphicon glyphicon-plus-sign"></i> Add More</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-radio">
                        <label class="col-md-2 control-label">Radio-group:</label>
                        <div class="col-md-10 field-radios-cont">
                            <div class="row field-radios">
                                <div class="col-md-6">
                                    <input type="text" class="field-radios-label form-control" placeholder="Name" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-radios-checked">
                                        Selected? </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-radios-disabled">
                                        Disabled? </label>
                                </div>
                                <div class="close-button" data-type="radio"><i class="glyphicon glyphicon-remove"></i></div>
                            </div>
                            <div class="row field-radios">
                                <div class="col-md-6">
                                    <input type="text" class="field-radios-label form-control" placeholder="Name" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-radios-checked">
                                        Selected? </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-radios-disabled">
                                        Disabled? </label>
                                </div>
                                <div class="close-button" data-type="radio"><i class="glyphicon glyphicon-remove"></i></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="add-button custom-field-add btn btn-primary btn-sm" data-type="radio"><i class="glyphicon glyphicon-plus-sign"></i> Add More</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group formField custom-field show-for-select">
                        <label class="col-md-2 control-label">Options:</label>
                        <div class="col-md-10 field-select-cont">
                            <div class="row field-select">
                                <div class="col-md-6">
                                    <input type="text" class="field-select-label form-control" placeholder="Name" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-select-checked">
                                        Selected? </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-select-disabled">
                                        Disabled? </label>
                                </div>
                                <div class="close-button" data-type="select"><i class="glyphicon glyphicon-remove"></i></div>
                            </div>
                            <div class="row field-select">
                                <div class="col-md-6">
                                    <input type="text" class="field-select-label form-control" placeholder="Name" required/>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-select-checked">
                                        Selected? </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="field-select-disabled">
                                        Disabled? </label>
                                </div>
                                <div class="close-button" data-type="select"><i class="glyphicon glyphicon-remove"></i></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="add-button custom-field-add btn btn-primary btn-sm" data-type="select"><i class="glyphicon glyphicon-plus-sign"></i> Add More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="cancel-button-cont"></span>
                    <input type="submit" value="Add" class="add_field btn btn-primary" data-edit="false" data-edit-index=""/>
                </div>
            </form>
        </div>
    </div>
</div>