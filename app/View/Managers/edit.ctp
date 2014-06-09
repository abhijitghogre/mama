<div class="users form">
    <div class="formMessage">
        <?php echo $this->Session->flash(); ?>
    </div>
    <div class="content-box-header">
        <div class="panel-title formTitle">
            <?php echo __('Edit manager'); ?>
        </div>
    </div>
    <div class="content-box-large padding-10 box-with-header">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <?php
                    echo $this->Form->create('Manager', array('class' => 'form-horizontal', 'role' => 'form'));
                    ?>
                    <div class="form-group formField">
                        <label for="inputfname" class="col-sm-2 control-label">First Name:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('fname', array('value' => $manager[0]['Manager']['First Name'], 'label' => false, 'class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'inputfname')); ?>
                        </div>
                    </div>
                    <div class="form-group formField">
                        <label for="inputlname" class="col-sm-2 control-label">Last Name:</label>
                        <div class="col-sm-10">
                            <?php echo $this->Form->input('lname', array('value' => $manager[0]['Manager']['Last Name'], 'label' => false, 'class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'inputlname')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center"><?php
                            echo $this->Form->submit('Save', array('class' => 'btn btn-primary', 'title' => 'Save'));
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>