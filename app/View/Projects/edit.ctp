<div class="formMessage">
    <?php echo $this->Session->flash(); ?>
</div>
<div class="content-box-header">
    <div class="panel-title formTitle">
        <?php
        echo __('Edit project');
        ?>
    </div>
</div>
<div class="content-box-large padding-10 box-with-header">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?php
                echo $this->Form->create('Project', array('class' => 'form-horizontal', 'role' => 'form'));
                ?>
                <div class="form-group formField">
                    <label for="inputproject" class="col-sm-2 control-label">Project name:</label>
                    <div class="col-sm-10">
                        <?php echo $this->Form->input('project_name', array('label' => false, 'value' => $project[0]['Project']['Name'], 'class' => 'form-control', 'placeholder' => 'Project name', 'id' => 'inputproject')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <?php
                        echo $this->Form->submit('Save', array('class' => 'btn btn-primary', 'title' => 'Save project'));
                        echo $this->Form->end();
                        ?>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>